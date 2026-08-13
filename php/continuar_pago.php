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

$orden_id = (int)($_GET['id'] ?? 0);
if (!$orden_id) {
    header("Location: ../views/historial/reg-pgb.php?modo=mixto");
    exit();
}

// Traer la orden
$row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM ordenes WHERE id = $orden_id"));
if (!$row) {
    header("Location: ../views/historial/reg-pgb.php?modo=mixto");
    exit();
}

$total        = (float)$row['precio'];
$monto_pagado = (float)$row['monto_pagado'];
$saldo_rest   = $total - $monto_pagado;
$productos    = $row['producto'];
$jugador_id   = $row['jugador_id'];

if ($saldo_rest <= 0) {
    header("Location: ../views/historial/reg-pgb.php?modo=mixto");
    exit();
}

// ── Crear nueva sesión en PlacetoPay por el saldo restante ──
$login     = "2d9eaf1e662518756a3d78806543af5b";
$secretKey = "3YC5brb5eAR4xBGQ";
$endpoint  = "https://checkout-test.placetopay.com/api/session";

$seed     = date('c');
$nonce    = bin2hex(random_bytes(16));
$tranKey  = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
$nonceB64 = base64_encode($nonce);

$reference = 'MIX-CONT-' . strtoupper(bin2hex(random_bytes(4)));

$body = [
    "auth" => [
        "login"   => $login,
        "tranKey" => $tranKey,
        "nonce"   => $nonceB64,
        "seed"    => $seed
    ],
    "payment" => [
        "reference"   => $reference,
        "description" => "Saldo restante: " . $productos,
        "amount"      => [
            "currency" => "COP",
            "total"    => $saldo_rest
        ]
    ],
    "expiration"  => date('c', strtotime('+30 minutes')),
    "returnUrl"   => app_base_url() . "/retorno/retorno_continuar.php?orden_id={$orden_id}",
    "ipAddress"   => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    "userAgent"   => $_SERVER['HTTP_USER_AGENT'] ?? 'PlanceDemoAgent/1.0',
    "locale"      => "es_CO"
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

if ($curlError) {
    die("❌ Error de conexión: " . $curlError);
}

$result     = json_decode($response, true);
$status     = $result['status']['status'] ?? 'FAILED';
$processUrl = $result['processUrl']       ?? null;
$requestId  = $result['requestId']        ?? null;

if ($status === 'OK' && $processUrl) {
    // Guardar en sesión para el retorno
    $_SESSION['continuar_result'] = [
        'orden_id'    => $orden_id,
        'productos'   => $productos,
        'total'       => $total,
        'monto_pagado'=> $monto_pagado,
        'saldo_rest'  => $saldo_rest,
        'requestId'   => $requestId,
        'reference'   => $reference,
    ];
    header("Location: " . $processUrl);
    exit();
}

// Si falla redirigir al historial con error
header("Location: ../views/historial/reg-pgb.php?modo=mixto&error=1");
exit();
?>
