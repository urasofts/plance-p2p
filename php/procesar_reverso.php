<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$id   = intval($_GET['id']   ?? 0);
$tipo = $_GET['tipo'] ?? '';

$tipos_permitidos = ['orden', 'suscripcion', 'recurrencia'];
if (!$id || !in_array($tipo, $tipos_permitidos)) {
    header("Location: ../views/historial/reversos.php");
    exit();
}

$id_safe     = mysqli_real_escape_string($conexion, $id);
$correo_safe = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

// ══════════════════════════════════════════
// Verificar que la transacción existe,
// pertenece al usuario y está aprobada
// ══════════════════════════════════════════
if ($tipo === 'orden') {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT * FROM ordenes WHERE id = '$id_safe' AND estado = 'aprobada'"
    ));
    $tabla  = 'ordenes';
    $nombre = $trx['producto'] ?? '';

} elseif ($tipo === 'suscripcion') {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT * FROM suscripciones WHERE id = '$id_safe' AND estado = 'aprobada' AND usuario_id = '$correo_safe'"
    ));
    $tabla  = 'suscripciones';
    $nombre = ($trx['plataforma'] ?? '') . ' — ' . ($trx['plan'] ?? '');

} else {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT * FROM recurrencias WHERE id = '$id_safe' AND estado = 'aprobada' AND usuario_id = '$correo_safe'"
    ));
    $tabla  = 'recurrencias';
    $nombre = ($trx['servicio'] ?? '') . ' — ' . ($trx['plan'] ?? '');
}

if (!$trx) {
    $_SESSION['reverso_msg']      = '❌ No se encontró la transacción o no tienes permisos para reversarla.';
    $_SESSION['reverso_msg_type'] = 'error';
    header("Location: ../views/historial/reversos.php");
    exit();
}

// ══════════════════════════════════════════
// Reverso real ante PlaceToPay (Web Checkout API)
// ══════════════════════════════════════════
$request_id = trim($trx['request_id'] ?? '');
if (empty($request_id)) {
    $_SESSION['reverso_msg']      = '❌ La transacción no tiene un request_id de PlaceToPay asociado; no se puede reversar.';
    $_SESSION['reverso_msg_type'] = 'error';
    header("Location: ../views/historial/reversos.php");
    exit();
}

$login     = "2d9eaf1e662518756a3d78806543af5b";
$secretKey = "3YC5brb5eAR4xBGQ";

function ptp_auth($secretKey, $login) {
    $seed  = date('c');
    $nonce = bin2hex(random_bytes(16));
    return [
        "login"   => $login,
        "tranKey" => base64_encode(hash('sha256', $nonce . $seed . $secretKey, true)),
        "nonce"   => base64_encode($nonce),
        "seed"    => $seed
    ];
}

function ptp_post($url, $body) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($body));
    curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT,        30);
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);
    return [$response, $error];
}

// 1) Consultar la sesión para obtener el internalReference de la transacción aprobada
[$sessionResponse, $sessionError] = ptp_post(
    "https://checkout-test.placetopay.com/api/session/$request_id",
    ["auth" => ptp_auth($secretKey, $login)]
);

if (!$sessionResponse) {
    $_SESSION['reverso_msg']      = '❌ No se pudo consultar la transacción en PlaceToPay: ' . $sessionError;
    $_SESSION['reverso_msg_type'] = 'error';
    header("Location: ../views/historial/reversos.php");
    exit();
}

$sessionResult      = json_decode($sessionResponse, true);
$internalReference  = $sessionResult['payment'][0]['internalReference'] ?? null;

if (!$internalReference) {
    $_SESSION['reverso_msg']      = '❌ PlaceToPay no reportó una transacción reversable para esta orden.';
    $_SESSION['reverso_msg_type'] = 'error';
    header("Location: ../views/historial/reversos.php");
    exit();
}

// 2) Solicitar el reverso ante PlaceToPay
[$reverseResponse, $reverseError] = ptp_post(
    "https://checkout-test.placetopay.com/api/reverse",
    [
        "auth"              => ptp_auth($secretKey, $login),
        "internalReference" => $internalReference
    ]
);

if (!$reverseResponse) {
    $_SESSION['reverso_msg']      = '❌ No se pudo conectar con PlaceToPay para reversar: ' . $reverseError;
    $_SESSION['reverso_msg_type'] = 'error';
    header("Location: ../views/historial/reversos.php");
    exit();
}

$reverseResult = json_decode($reverseResponse, true);
$gw_status     = $reverseResult['status']['status']  ?? 'FAILED';
$gw_message    = $reverseResult['status']['message'] ?? 'Sin respuesta del servidor';

if ($gw_status !== 'OK') {
    $_SESSION['reverso_msg']      = '❌ PlaceToPay rechazó el reverso: ' . $gw_message;
    $_SESSION['reverso_msg_type'] = 'error';
    header("Location: ../views/historial/reversos.php");
    exit();
}

// ══════════════════════════════════════════
// Actualizar estado a "reversada" en BD
// ══════════════════════════════════════════
$resultado = mysqli_query($conexion,
    "UPDATE $tabla SET estado = 'reversada' WHERE id = '$id_safe'"
);

if ($resultado) {
    $_SESSION['reverso_msg']      = '✅ Transacción #' . $id . ' (' . $nombre . ') reversada correctamente ante PlaceToPay. El dinero será devuelto al cliente.';
    $_SESSION['reverso_msg_type'] = 'success';
} else {
    $_SESSION['reverso_msg']      = '⚠️ PlaceToPay aprobó el reverso pero no se pudo actualizar el estado local: ' . mysqli_error($conexion);
    $_SESSION['reverso_msg_type'] = 'error';
}

header("Location: ../views/historial/reversos.php");
exit();
?>