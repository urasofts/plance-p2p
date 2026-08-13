<?php
session_start();
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/games/pubg.php");
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

// Recibir datos
$producto   = trim($_POST['producto']   ?? '');
$precio     = trim($_POST['precio']     ?? '');
$jugador_id = trim($_POST['jugador_id'] ?? '');
$metodo     = trim($_POST['metodo']     ?? 'tarjeta');
$tipo_doc   = trim($_POST['tipo_doc']   ?? '');
$num_doc    = trim($_POST['num_doc']    ?? '');
$correo     = trim($_POST['correo']     ?? '');
$telefono   = trim($_POST['telefono']   ?? '');
$nombre     = trim($_POST['card_name']  ?? $_POST['nombre'] ?? '');

if (empty($producto) || empty($precio) || empty($jugador_id)) {
    die("❌ Faltan datos principales.");
}

// Sanitizar
$producto   = mysqli_real_escape_string($conexion, $producto);
$precio     = mysqli_real_escape_string($conexion, $precio);
$jugador_id = mysqli_real_escape_string($conexion, $jugador_id);
$correo     = mysqli_real_escape_string($conexion, $correo);
$telefono   = mysqli_real_escape_string($conexion, $telefono);
$tipo_doc   = mysqli_real_escape_string($conexion, $tipo_doc);
$num_doc    = mysqli_real_escape_string($conexion, $num_doc);
$nombre     = mysqli_real_escape_string($conexion, $nombre);

// ══════════════════════════════════════════
// API Gateway Real — PlacetoPay
// Endpoint: api-test.placetopay.com
// ══════════════════════════════════════════
$login     = "2d9eaf1e662518756a3d78806543af5b";
$secretKey = "3YC5brb5eAR4xBGQ";
$endpoint  = "https://api-test.placetopay.com/rest/gateway/process";

// Auth
$seed     = date('c');
$nonce    = bin2hex(random_bytes(16));
$tranKey  = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
$nonceB64 = base64_encode($nonce);

$reference = 'GW-BS-' . strtoupper(bin2hex(random_bytes(4)));

// Datos de tarjeta (solo si metodo es tarjeta)
$card_number = preg_replace('/\s/', '', $_POST['card_number'] ?? '');
$card_expiry = trim($_POST['card_expiry'] ?? '12/26');
$card_cvv    = trim($_POST['card_cvv']    ?? '');

// Armar instrument según método
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
    $num_cuenta = trim($_POST['num_cuenta'] ?? '');
    $instrument = [
        "bank" => [
            "code"    => trim($_POST['cuenta_banco'] ?? 'BANCOLOMBIA'),
            "account" => $num_cuenta
        ]
    ];
}

// Body del request
$body = [
    "auth" => [
        "login"   => $login,
        "tranKey" => $tranKey,
        "nonce"   => $nonceB64,
        "seed"    => $seed
    ],
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
        "description" => $producto,
        "amount"      => [
            "currency" => "COP",
            "total"    => (float)$precio
        ]
    ],
    "instrument" => $instrument,
    "notificationUrl"  => "https://doorman-situated-delivery.ngrok-free.dev/plance/php/notify.php",
    "ipAddress"  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    "userAgent"  => $_SERVER['HTTP_USER_AGENT'] ?? 'PlanceDemoAgent/1.0'
];

// Llamada cURL
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

// Interpretar respuesta
$result     = json_decode($response, true);
$gw_reason  = $result['status']['reason']  ?? '';
$gw_message = $result['status']['message'] ?? 'Sin respuesta del servidor';

// ── Estado elegido por el usuario en estados-gateway.php ──
// Tiene prioridad sobre la respuesta del API (es demo)
$estado_elegido = trim($_POST['estado_elegido'] ?? '');
$razon_elegida  = trim($_POST['razon_elegida']  ?? $gw_reason);

if (in_array($estado_elegido, ['aprobada', 'pendiente', 'rechazada'])) {
    $nuevo_estado = $estado_elegido;
    $status = match($nuevo_estado) {
        'aprobada'  => 'APPROVED',
        'pendiente' => 'PENDING',
        default     => 'REJECTED'
    };
} else {
    $gw_status    = $result['status']['status'] ?? 'FAILED';
    $nuevo_estado = match($gw_status) {
        'APPROVED' => 'aprobada',
        'PENDING'  => 'pendiente',
        default    => 'rechazada'
    };
    $status = $gw_status;
}

// Guardar en BD
$estado_safe = mysqli_real_escape_string($conexion, $nuevo_estado);
$gw_request_id = $result['internalReference'] ?? $reference;
$ref_safe    = mysqli_real_escape_string($conexion, $gw_request_id);

$query = "INSERT INTO gateway_ordenes (producto, precio, nombre, correo, telefono, tipo_doc, num_doc, estado, request_id)
          VALUES ('$producto', '$precio', '$nombre', '$correo', '$telefono', '$tipo_doc', '$num_doc', '$estado_safe', '$ref_safe')";

$resultado = mysqli_query($conexion, $query);
if (!$resultado) die("❌ Error al guardar: " . mysqli_error($conexion));

$orden_id = mysqli_insert_id($conexion);

// Guardar en sesión para retorno
$_SESSION['gw_result'] = [
    'orden_id'  => $orden_id,
    'status'    => $status,
    'estado'    => $nuevo_estado,
    'producto'  => $producto,
    'precio'    => $precio,
    'correo'    => $correo,
    'nombre'    => $nombre,
    'message'   => $gw_message,
    'razon'     => $gw_reason,
    'gw_status' => $gw_status,
    'reference' => $reference,
    'metodo'    => $metodo
];

unset($_SESSION['gw_pending']);
header("Location: ../retorno/retorno_gateway.php?orden=" . $orden_id);
exit();
?>