<?php
session_start();
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/games/bloodstrike.php");
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}
require_once 'p2p_config.php';
require_once 'p2p_sonda_core.php';

// Recibir datos
$orden_id   = (int) ($_POST['orden_id']   ?? 0);
$producto   = trim($_POST['producto']     ?? '');
$precio     = (float) ($_POST['precio']   ?? 0);
$jugador_id = trim($_POST['jugador_id']   ?? '');
$metodo     = trim($_POST['metodo']       ?? 'tarjeta');
$tipo_doc   = trim($_POST['tipo_doc']     ?? '');
$num_doc    = trim($_POST['num_doc']      ?? '');
$correo     = trim($_POST['correo']       ?? '');
$telefono   = trim($_POST['telefono']     ?? '');
$nombre     = trim($_POST['card_name']    ?? $_POST['nombre'] ?? '');
$monto_pagar = (float) ($_POST['monto_pagar'] ?? 0);

if (empty($jugador_id) || (!$orden_id && (empty($producto) || $precio <= 0))) {
    die("❌ Faltan datos principales.");
}

// ── Orden nueva vs. abono sobre una orden existente ──
if ($orden_id) {
    $orden_row = mysqli_fetch_assoc(mysqli_query(
        $conexion,
        "SELECT * FROM gateway_ordenes WHERE id = $orden_id AND tipo_pago = 'mixto'"
    ));
    if (!$orden_row) die("❌ Orden no encontrada.");

    $precio       = (float) $orden_row['precio'];
    $producto     = $orden_row['producto'];
    $monto_pagado = (float) ($orden_row['monto_pagado'] ?? 0);
} else {
    $monto_pagado = 0.0;
}

$saldo_pendiente = $precio - $monto_pagado;

if ($monto_pagar < 1000 || $monto_pagar > $saldo_pendiente) {
    die("❌ El monto a pagar debe estar entre \$1.000 COP y el saldo pendiente (\$" . number_format($saldo_pendiente, 0, ',', '.') . " COP).");
}

// Sanitizar
$producto_safe   = mysqli_real_escape_string($conexion, $producto);
$jugador_id_safe = mysqli_real_escape_string($conexion, $jugador_id);
$correo_safe     = mysqli_real_escape_string($conexion, $correo);
$telefono_safe   = mysqli_real_escape_string($conexion, $telefono);
$tipo_doc_safe   = mysqli_real_escape_string($conexion, $tipo_doc);
$num_doc_safe    = mysqli_real_escape_string($conexion, $num_doc);
$nombre_safe     = mysqli_real_escape_string($conexion, $nombre);

if (!$orden_id) {
    $query = "INSERT INTO gateway_ordenes (producto, tipo_pago, precio, nombre, correo, telefono, tipo_doc, num_doc, estado, monto_pagado)
              VALUES ('$producto_safe', 'mixto', '$precio', '$nombre_safe', '$correo_safe', '$telefono_safe', '$tipo_doc_safe', '$num_doc_safe', 'pendiente', 0)";
    if (!mysqli_query($conexion, $query)) die("❌ Error al crear la orden: " . mysqli_error($conexion));
    $orden_id = mysqli_insert_id($conexion);
}

// ══════════════════════════════════════════
// API Gateway — PlacetoPay (mismas credenciales del comercio principal)
// El monto enviado es el ABONO elegido por el usuario, no el total del producto.
// ══════════════════════════════════════════
$cred      = p2p_credenciales()['principal'];
$endpoint  = "https://api-test.placetopay.com/rest/gateway/process";
$auth      = p2p_construir_auth($cred['login'], $cred['secretKey']);
$reference = 'GWM-' . strtoupper(bin2hex(random_bytes(4)));

// Datos de tarjeta (solo si metodo es tarjeta)
$card_number = preg_replace('/\s/', '', $_POST['card_number'] ?? '');
$card_expiry = trim($_POST['card_expiry'] ?? '12/26');
$card_cvv    = trim($_POST['card_cvv']    ?? '');

$instrument = [];
if ($metodo === 'tarjeta') {
    $instrument = [
        "card" => [
            "number"     => $card_number,
            "expiration" => $card_expiry,
            "cvv"        => $card_cvv
        ]
    ];
} else {
    $instrument = [
        "bank" => [
            "code"    => trim($_POST['cuenta_banco'] ?? 'BANCOLOMBIA'),
            "account" => trim($_POST['num_cuenta']   ?? '')
        ]
    ];
}

$body = [
    "auth" => $auth,
    "payer" => [
        "name"         => $nombre,
        "surname"      => "",
        "email"        => $correo,
        "documentType" => $tipo_doc,
        "document"     => $num_doc,
        "mobile"       => $telefono
    ],
    "payment" => [
        "reference"   => $reference,
        "description" => "Abono: " . $producto,
        "amount"      => [
            "currency" => "COP",
            "total"    => $monto_pagar
        ]
    ],
    "instrument"      => $instrument,
    "notificationUrl" => P2P_NOTIFY_URL,
    "ipAddress"       => $_SERVER['REMOTE_ADDR']      ?? '127.0.0.1',
    "userAgent"       => $_SERVER['HTTP_USER_AGENT']  ?? 'PlanceDemoAgent/1.0'
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST,           true);
curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($body));
curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT,        30);

$response  = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

$result     = json_decode($response, true);
$gw_reason  = $result['status']['reason']  ?? '';
$gw_message = $result['status']['message'] ?? 'Sin respuesta del servidor';

// ── Estado elegido por el usuario en estados-gateway.php (demo) ──
$estado_elegido = trim($_POST['estado_elegido'] ?? '');
$razon_elegida  = trim($_POST['razon_elegida']  ?? $gw_reason);

if (in_array($estado_elegido, ['aprobada', 'pendiente', 'rechazada'])) {
    $nuevo_estado_abono = $estado_elegido;
    $gw_status = match ($nuevo_estado_abono) {
        'aprobada'  => 'APPROVED',
        'pendiente' => 'PENDING',
        default     => 'REJECTED'
    };
} else {
    $gw_status = $result['status']['status'] ?? 'FAILED';
    $nuevo_estado_abono = match ($gw_status) {
        'APPROVED' => 'aprobada',
        'PENDING'  => 'pendiente',
        default    => 'rechazada'
    };
}

// ── Registrar el abono y recalcular la orden ──
$gw_request_id   = $result['internalReference'] ?? $reference;
$ref_safe        = mysqli_real_escape_string($conexion, $gw_request_id);
$estado_safe     = mysqli_real_escape_string($conexion, $nuevo_estado_abono);
$metodo_safe     = mysqli_real_escape_string($conexion, $metodo);

$query = "INSERT INTO gateway_abonos (gateway_orden_id, monto, medio_pago, estado, request_id)
          VALUES ($orden_id, '$monto_pagar', '$metodo_safe', '$estado_safe', '$ref_safe')";
if (!mysqli_query($conexion, $query)) die("❌ Error al guardar el abono: " . mysqli_error($conexion));
$abono_id = mysqli_insert_id($conexion);

p2p_recalcular_orden_mixta($conexion, $orden_id);

$orden_actual = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM gateway_ordenes WHERE id = $orden_id"));

// Guardar en sesión para retorno
$_SESSION['gwm_result'] = [
    'orden_id'    => $orden_id,
    'abono_id'    => $abono_id,
    'status'      => $gw_status,
    'estado'      => $nuevo_estado_abono,
    'producto'    => $producto,
    'precio'      => $precio,
    'monto_pagar' => $monto_pagar,
    'monto_pagado'=> (float) ($orden_actual['monto_pagado'] ?? 0),
    'correo'      => $correo,
    'nombre'      => $nombre,
    'message'     => $gw_message,
    'razon'       => $gw_reason,
    'reference'   => $reference,
    'metodo'      => $metodo
];

header("Location: ../retorno/retorno_mixto_gateway.php?orden=" . $orden_id);
exit();
?>
