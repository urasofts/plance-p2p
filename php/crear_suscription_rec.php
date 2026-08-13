<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/plataformas/ia.php");
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$servicio     = trim($_POST['servicio']     ?? '');
$plan         = trim($_POST['plan']         ?? '');
$precio       = trim($_POST['precio']       ?? '');
$usuario_id   = trim($_POST['usuario_id']   ?? '');
$periodicidad = trim($_POST['periodicidad'] ?? 'M');

if (empty($servicio) || empty($plan) || empty($precio) || empty($usuario_id)) {
    die("❌ Faltan datos.");
}

$servicio     = mysqli_real_escape_string($conexion, $servicio);
$plan         = mysqli_real_escape_string($conexion, $plan);
$precio       = mysqli_real_escape_string($conexion, $precio);
$usuario_id   = mysqli_real_escape_string($conexion, $usuario_id);
$periodicidad = mysqli_real_escape_string($conexion, $periodicidad);

// Calcular fechas según periodicidad
if ($periodicidad === 'Y') {
    $next_payment = date('Y-m-d', strtotime('+1 year'));
    $fecha_fin    = date('Y-m-d', strtotime('+1 year'));
    $maxPeriods   = 1;
    $interval     = "12";
} else {
    $next_payment = date('Y-m-d', strtotime('+1 month'));
    $fecha_fin    = date('Y-m-d', strtotime('+12 months'));
    $maxPeriods   = 12;
    $interval     = "1";
}

$estado = "pendiente";
$query  = "INSERT INTO suscription_rec (servicio, plan, precio, usuario_id, estado, periodicidad, next_payment, fecha_fin)
           VALUES ('$servicio', '$plan', '$precio', '$usuario_id', '$estado', '$periodicidad', '$next_payment', '$fecha_fin')";

$resultado = mysqli_query($conexion, $query);
if (!$resultado) die("❌ Error al guardar: " . mysqli_error($conexion));

$rec_id = mysqli_insert_id($conexion);

// ══════════════════════════════════════════
// WEB CHECKOUT — Pago recurrente
// ══════════════════════════════════════════
$login     = "2d9eaf1e662518756a3d78806543af5b";
$secretKey = "3YC5brb5eAR4xBGQ";
$url       = "https://checkout-test.placetopay.com/api/session";

$seed     = date('c');
$nonce    = bin2hex(random_bytes(16));
$tranKey  = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
$nonceB64 = base64_encode($nonce);

$descripcion = substr(preg_replace('/[^a-zA-Z0-9 ]/u', '', $servicio . ' ' . $plan), 0, 80);

$data = [
    "locale" => "es_CO",
    "auth"   => [
        "login"   => $login,
        "tranKey" => $tranKey,
        "nonce"   => $nonceB64,
        "seed"    => $seed
    ],
    "buyer" => ["email" => $usuario_id],
    "payment" => [
        "reference"   => "SREC-" . (string)$rec_id,
        "description" => $descripcion,
        "amount"      => [
            "currency" => "COP",
            "total"    => (float)$precio
        ],
        "recurring" => [
            "periodicity" => $periodicidad,
            "interval"    => $interval,
            "nextPayment" => $next_payment,
            "maxPeriods"  => $maxPeriods
        ]
    ],
    "expiration" => date('c', strtotime('+1 hour')),
    "returnUrl"  => app_base_url() . "/retorno/retorno_suscription_rec.php?rec=" . $rec_id,
    "notifyUrl"  => "https://doorman-situated-delivery.ngrok-free.dev/plance/php/notify.php",
    "ipAddress"  => $_SERVER['REMOTE_ADDR'],
    "userAgent"  => $_SERVER['HTTP_USER_AGENT']
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST,           true);
curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response  = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if (!$response) die("❌ Error de conexión: " . $curlError);

$result = json_decode($response, true);

if (isset($result['processUrl'])) {
    $request_id = mysqli_real_escape_string($conexion, $result['requestId'] ?? '');
    mysqli_query($conexion, "UPDATE suscription_rec SET request_id = '$request_id' WHERE id = '$rec_id'");
    header("Location: " . $result['processUrl']);
    exit();
} else {
    echo "<h3 style='font-family:sans-serif;color:#e05252;'>❌ Error al crear sesión</h3>";
    echo "<pre style='background:#1e2128;color:#f0f1f3;padding:1rem;border-radius:8px;'>";
    print_r($result);
    echo "</pre>";
    echo "<a href='../views/plataformas/ia.php' style='color:#8b5cf6;'>← Volver</a>";
}
?>