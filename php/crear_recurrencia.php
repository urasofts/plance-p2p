<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/plataformas/redes.php");
    exit();
}

// Conexión
require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

// Recibir y limpiar datos
$servicio   = trim($_POST['servicio']   ?? '');
$plan       = trim($_POST['plan']       ?? '');
$precio     = trim($_POST['precio']     ?? '');
$usuario_id = trim($_POST['usuario_id'] ?? '');

// Validación básica
if (empty($servicio) || empty($plan) || empty($precio) || empty($usuario_id)) {
    die("❌ Faltan datos. Por favor vuelve y completa todos los campos.");
}

// Sanitizar
$servicio   = mysqli_real_escape_string($conexion, $servicio);
$plan       = mysqli_real_escape_string($conexion, $plan);
$precio     = mysqli_real_escape_string($conexion, $precio);
$usuario_id = mysqli_real_escape_string($conexion, $usuario_id);

// Calcular próximo cobro (1 mes desde hoy) y fecha fin (12 meses desde hoy)
$next_payment = date('Y-m-d', strtotime('+1 month'));
$fecha_fin    = date('Y-m-d', strtotime('+12 months'));

// Insertar en tabla recurrencias
$estado = "pendiente";
$query  = "INSERT INTO recurrencias (servicio, plan, precio, usuario_id, estado, periodicidad, next_payment, fecha_fin)
           VALUES ('$servicio', '$plan', '$precio', '$usuario_id', '$estado', 'M', '$next_payment', '$fecha_fin')";

$resultado = mysqli_query($conexion, $query);

if (!$resultado) {
    die("❌ Error al guardar la recurrencia: " . mysqli_error($conexion));
}

$rec_id = mysqli_insert_id($conexion);

// ══════════════════════════════════════════
// 🔄 WEB CHECKOUT — PlaceToPay con RECURRENCIA
// ══════════════════════════════════════════

$login     = "2d9eaf1e662518756a3d78806543af5b";
$secretKey = "3YC5brb5eAR4xBGQ";
$url       = "https://checkout-test.placetopay.com/api/session";

// Auth
$seed     = date('c');
$nonce    = bin2hex(random_bytes(16));
$tranKey  = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
$nonceB64 = base64_encode($nonce);

// Descripción limpia
$descripcion = substr(preg_replace('/[^a-zA-Z0-9 ]/u', '', $servicio . ' ' . $plan), 0, 80);

// Request con campo recurring
$data = [
    "locale" => "es_CO",
    "auth"   => [
        "login"   => $login,
        "tranKey" => $tranKey,
        "nonce"   => $nonceB64,
        "seed"    => $seed
    ],
    // Pre-diligenciar correo del usuario
    "buyer"  => [
        "email" => $usuario_id
    ],
    "payment" => [
        "reference"   => "REC-" . (string)$rec_id,
        "description" => $descripcion,
        "amount"      => [
            "currency" => "COP",
            "total"    => (float)$precio
        ],
        // ← CLAVE: campo recurring para cobro automático mensual
        "recurring"   => [
            "periodicity"   => "M",          // M = mensual
            "interval"      => "1",           // cada 1 mes
            "nextPayment"   => $next_payment, // próximo cobro
            "maxPeriods"    => 12             // máximo 12 meses
        ]
    ],
    "expiration" => date('c', strtotime('+1 hour')),
    "returnUrl"  => app_base_url() . "/retorno/retorno_rec.php?rec=" . $rec_id,
    "notifyUrl"  => "https://doorman-situated-delivery.ngrok-free.dev/plance/php/notify.php",
    "ipAddress"  => $_SERVER['REMOTE_ADDR'],
    "userAgent"  => $_SERVER['HTTP_USER_AGENT']
];

// Llamada a PlaceToPay
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

if (!$response) {
    die("❌ Error de conexión con PlaceToPay: " . $curlError);
}

$result = json_decode($response, true);

if (isset($result['processUrl'])) {
    // Guardar requestId en BD antes de redirigir
    $request_id = mysqli_real_escape_string($conexion, $result['requestId'] ?? '');
    mysqli_query($conexion, "UPDATE recurrencias SET request_id = '$request_id' WHERE id = '$rec_id'");

    header("Location: " . $result['processUrl']);
    exit();
} else {
    echo "<h3 style='font-family:sans-serif;color:#e05252;'>❌ Error al crear sesión de pago</h3>";
    echo "<p style='font-family:sans-serif;color:#f0f1f3;'>Recurrencia <strong>#$rec_id</strong> guardada en BD pero el pago no pudo iniciarse.</p>";
    echo "<pre style='background:#1e2128;color:#f0f1f3;padding:1rem;border-radius:8px;font-size:0.85rem;'>";
    print_r($result);
    echo "</pre>";
    echo "<a href='../views/plataformas/redes.php' style='color:#4d9fff;font-family:sans-serif;'>← Volver</a>";
}
?>