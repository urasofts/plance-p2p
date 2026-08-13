<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexion: " . mysqli_connect_error());
}

// Recibir parametros
$tabla      = $_GET['tabla']      ?? '';
$id         = intval($_GET['id']  ?? 0);
$request_id = $_GET['request_id'] ?? '';
$redirect   = $_GET['redirect']   ?? '../views/historial/historial.php';

// Validar tabla permitida (seguridad)
$tablas_permitidas = ['ordenes', 'suscripciones', 'recurrencias', 'suscription_rec', 'suscription', 'gateway_suscripciones', 'gateway_suscription', 'gateway_ordenes', 'reservaciones', 'dispersiones'];
if (!in_array($tabla, $tablas_permitidas) || !$id || !$request_id) {
    header("Location: $redirect");
    exit();
}

// Detectar si la tabla pertenece al API Gateway
$tablas_gateway = ['gateway_ordenes', 'gateway_suscripciones', 'gateway_suscription'];
$es_gateway     = in_array($tabla, $tablas_gateway);

// ==========================================
// Construir autenticacion PlaceToPay
// Las preautorizaciones (reservaciones) usan credenciales distintas al resto
// ==========================================
require_once 'p2p_config.php';
if ($tabla === 'reservaciones') {
    $login     = P2P_CREDENCIALES['preautorizacion']['login'];
    $secretKey = P2P_CREDENCIALES['preautorizacion']['secretKey'];
} elseif ($tabla === 'dispersiones') {
    $login     = P2P_CREDENCIALES['dispersion']['login'];
    $secretKey = P2P_CREDENCIALES['dispersion']['secretKey'];
} else {
    $login     = P2P_CREDENCIALES['principal']['login'];
    $secretKey = P2P_CREDENCIALES['principal']['secretKey'];
}

$seed     = date('c');
$nonce    = bin2hex(random_bytes(16));
$tranKey  = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
$nonceB64 = base64_encode($nonce);

$auth = [
    "login"   => $login,
    "tranKey" => $tranKey,
    "nonce"   => $nonceB64,
    "seed"    => $seed
];

// ==========================================
// Elegir endpoint segun el tipo de pago
// ==========================================
if ($es_gateway) {
    // API Gateway: consulta por internalReference (requestId)
    $url     = "https://api-test.placetopay.com/rest/gateway/query";
    $payload = [
        "auth"              => $auth,
        "internalReference" => $request_id
    ];
} else {
    // Web Checkout: consulta por sesion
    $url     = "https://checkout-test.placetopay.com/api/session/" . $request_id;
    $payload = [
        "auth" => $auth
    ];
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// ==========================================
// Determinar nuevo estado
// ==========================================
$status_p2p = $result['status']['status'] ?? 'UNKNOWN';

// El estado real de la transaccion puede venir en payment[] (checkout);
// se prioriza sobre el estado general de la sesion.
if (!$es_gateway && !empty($result['payment'][0]['status']['status'])) {
    $status_p2p = $result['payment'][0]['status']['status'];
}

$map = [
    'APPROVED'  => 'aprobada',
    'REJECTED'  => 'rechazada',
    'PENDING'   => 'pendiente',
    'CANCELLED' => 'cancelada',
    'REFUNDED'  => 'cancelada',
    'FAILED'    => 'rechazada'
];

$id_safe    = mysqli_real_escape_string($conexion, $id);
$tabla_safe = $tabla; // ya validada arriba

if (isset($map[$status_p2p])) {
    // Estado reconocido: actualizar BD
    $nuevo_estado = $map[$status_p2p];
    $estado_safe  = mysqli_real_escape_string($conexion, $nuevo_estado);
    mysqli_query($conexion, "UPDATE $tabla_safe SET estado = '$estado_safe' WHERE id = '$id_safe'");
    $_SESSION['verify_msg'] = "Orden #$id actualizada a: " . strtoupper($nuevo_estado);
} else {
    // No se pudo determinar el estado: NO degradar a cancelada
    $_SESSION['verify_msg'] = "No se pudo verificar el estado de la orden #$id. Estado sin cambios.";
}

header("Location: $redirect");
exit();
?>