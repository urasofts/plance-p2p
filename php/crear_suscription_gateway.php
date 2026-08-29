<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/plataformas/streaming_gateway.php");
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$servicio = trim($_POST['servicio'] ?? '');
$plan     = trim($_POST['plan']     ?? '');
$precio   = trim($_POST['precio']   ?? '');
$nombre   = trim($_POST['nombre']   ?? '');
$correo   = trim($_POST['correo']   ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$tipo_doc = trim($_POST['tipo_doc'] ?? '');
$num_doc  = trim($_POST['num_doc']  ?? '');

if (empty($servicio) || empty($plan) || empty($precio) || empty($nombre) || empty($correo) || empty($num_doc)) {
    die("❌ Faltan datos. Por favor completa todos los campos.");
}

$servicio = mysqli_real_escape_string($conexion, $servicio);
$plan     = mysqli_real_escape_string($conexion, $plan);
$precio   = mysqli_real_escape_string($conexion, $precio);
$nombre   = mysqli_real_escape_string($conexion, $nombre);
$correo   = mysqli_real_escape_string($conexion, $correo);
$telefono = mysqli_real_escape_string($conexion, $telefono);
$tipo_doc = mysqli_real_escape_string($conexion, $tipo_doc);
$num_doc  = mysqli_real_escape_string($conexion, $num_doc);

// ══════════════════════════════════════════
// API Gateway Real — Suscripción pura
// Solo tokeniza la tarjeta, NO cobra nada en este request
// (mismo criterio que otras_streaming.php / crear_suscription.php en Web Checkout)
// Endpoint: api-test.placetopay.com
// ══════════════════════════════════════════
require_once 'p2p_config.php';
require_once 'p2p_sonda_core.php';

$cred      = p2p_credenciales()['principal'];
$endpoint  = "https://api-test.placetopay.com/rest/gateway/process";
$auth      = p2p_construir_auth($cred['login'], $cred['secretKey']);
$reference = 'GWSUS-' . strtoupper(bin2hex(random_bytes(4)));

// Suscripción pura solo admite tarjeta: es lo único que se puede tokenizar
$card_number = preg_replace('/\s/', '', $_POST['card_number'] ?? '');
$card_expiry = trim($_POST['card_expiry'] ?? '12/26');
$card_cvv    = trim($_POST['card_cvv']    ?? '');

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
    // "subscription" en vez de "payment": tokeniza sin cobrar
    "subscription" => [
        "reference"   => $reference,
        "description" => $servicio . ' — ' . $plan
    ],
    "instrument" => [
        "card" => [
            "number"     => $card_number,
            "expiration" => $card_expiry,
            "cvv"        => $card_cvv
        ]
    ],
    "ipAddress"       => $_SERVER['REMOTE_ADDR']     ?? '127.0.0.1',
    "notificationUrl" => P2P_NOTIFY_URL,
    "userAgent"       => $_SERVER['HTTP_USER_AGENT'] ?? 'PlanceDemoAgent/1.0'
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST,           true);
curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($body));


curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT,        30);

$response = curl_exec($ch);
curl_close($ch);

$result     = json_decode($response, true);
$gw_reason  = $result['status']['reason']  ?? '';
$gw_message = $result['status']['message'] ?? 'Sin respuesta del servidor';
$gw_token   = $result['subscription']['token']['token'] ?? '';

// ── Estado elegido por el usuario en estados-subs-gateway.php ──
$estado_elegido = trim($_POST['estado_elegido'] ?? '');
$razon_elegida  = trim($_POST['razon_elegida']  ?? $gw_reason);

if (in_array($estado_elegido, ['aprobada-token', 'aprobada-sin', 'pendiente', 'rechazada'])) {
    $nuevo_estado = match($estado_elegido) {
        'aprobada-token', 'aprobada-sin' => 'aprobada',
        'pendiente' => 'pendiente',
        default     => 'rechazada'
    };
    $status    = match($nuevo_estado) {
        'aprobada'  => 'APPROVED',
        'pendiente' => 'PENDING',
        default     => 'REJECTED'
    };
    $con_token = ($estado_elegido === 'aprobada-token');
    $token     = $con_token ? (!empty($gw_token) ? $gw_token : 'TOK-' . strtoupper(bin2hex(random_bytes(8)))) : '';
} else {
    $gw_status = $result['status']['status'] ?? 'FAILED';
    $nuevo_estado = match($gw_status) {
        'APPROVED' => 'aprobada',
        'PENDING'  => 'pendiente',
        default    => 'rechazada'
    };
    $status    = $gw_status;
    $con_token = !empty($gw_token);
    $token     = $con_token ? $gw_token : '';
}

$estado_safe = mysqli_real_escape_string($conexion, $nuevo_estado);
$gw_request_id = $result['internalReference'] ?? $reference;
$ref_safe    = mysqli_real_escape_string($conexion, $gw_request_id);
$token_safe  = mysqli_real_escape_string($conexion, $token);

$query = "INSERT INTO gateway_suscription (servicio, plan, precio, nombre, correo, telefono, tipo_doc, num_doc, estado, request_id, token)
          VALUES ('$servicio', '$plan', '$precio', '$nombre', '$correo', '$telefono', '$tipo_doc', '$num_doc', '$estado_safe', '$ref_safe', '$token_safe')";

$resultado = mysqli_query($conexion, $query);
if (!$resultado) die("❌ Error al guardar: " . mysqli_error($conexion));

$orden_id = mysqli_insert_id($conexion);

$_SESSION['gw_sus_result'] = [
    'orden_id'  => $orden_id,
    'status'    => $status,
    'estado'    => $nuevo_estado,
    'servicio'  => $servicio,
    'plan'      => $plan,
    'precio'    => $precio,
    'nombre'    => $nombre,
    'correo'    => $correo,
    'reference' => $reference,
    'token'     => $token,
    'message'   => $gw_message,
];

unset($_SESSION['gw_subs_pending']);
header("Location: ../retorno/retorno_suscription_gateway.php?orden=" . $orden_id);
exit();
?>