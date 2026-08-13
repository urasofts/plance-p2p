<?php
session_start();
// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../home.php");
    exit();
}

// Conexión
require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
}

// Recibir y limpiar datos
$producto   = trim($_POST['producto']   ?? '');
$precio     = trim($_POST['precio']     ?? '');
$jugador_id = trim($_POST['jugador_id'] ?? '');

// Validación básica
if (empty($producto) || empty($precio) || empty($jugador_id)) {
    die("❌ Faltan datos. Por favor vuelve y completa todos los campos.");
}

// Sanitizar para evitar SQL injection básico
$producto   = mysqli_real_escape_string($conexion, $producto);
$precio     = mysqli_real_escape_string($conexion, $precio);
$jugador_id = mysqli_real_escape_string($conexion, $jugador_id);

// Insertar orden en BD
$estado = "pendiente";
$query  = "INSERT INTO ordenes (producto, precio, jugador_id, estado) 
           VALUES ('$producto', '$precio', '$jugador_id', '$estado')";

$resultado = mysqli_query($conexion, $query);

if (!$resultado) {
    die("❌ Error al guardar la orden: " . mysqli_error($conexion));
}

$order_id = mysqli_insert_id($conexion);


//  WEB CHECKOUT — PlaceToPay


// Credenciales (reemplaza con las tuyas)
$login     = "2d9eaf1e662518756a3d78806543af5b";
$secretKey = "3YC5brb5eAR4xBGQ";
$url       = "https://checkout-test.placetopay.com/api/session";

// Autenticación
$seed     = date('c');
$nonce    = bin2hex(random_bytes(16));
$tranKey  = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
$nonceB64 = base64_encode($nonce);

// Cuerpo del request
$data = [
    "auth" => [
        "login"   => $login,
        "tranKey" => $tranKey,
        "nonce"   => $nonceB64,
        "seed"    => $seed
    ],
    "payment" => [
        "reference"   => "ORD-" . str_pad($order_id, 6, '0', STR_PAD_LEFT),
        "description" => substr(preg_replace('/[^a-zA-Z0-9 ]/u', '', $producto), 0, 80),
        "amount"      => [
            "currency" => "COP",
            "total"    => (float)$precio
        ]
    ],
    "expiration" => date('c', strtotime('+1 hour')),
    "returnUrl"  => app_base_url() . "/retorno/retorno.php?order=" . $order_id,
    //este notifyUrl es el que se llama desde PlaceToPay para informar del resultado del pago, es importante que sea accesible públicamente (ngrok o hosting)
    "notifyUrl"  => "https://doorman-situated-delivery.ngrok-free.dev/plance/php/notify.php",



    "ipAddress"  => $_SERVER['REMOTE_ADDR'],
    "userAgent"  => $_SERVER['HTTP_USER_AGENT']
];

// Llamada a la API de PlaceToPay
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST,           true);
curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
// En local omitimos verificación SSL (solo desarrollo)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response  = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

// Si curl falla del todo
if (!$response) {
    die("❌ Error de conexión con PlaceToPay: " . $curlError);
}

$result = json_decode($response, true);

// Redirigir al checkout de PlaceToPay
if (isset($result['processUrl'])) {
    // Guardar requestId en BD antes de redirigir
    $request_id = mysqli_real_escape_string($conexion, $result['requestId'] ?? '');
    mysqli_query($conexion, "UPDATE ordenes SET request_id = '$request_id' WHERE id = '$order_id'");

    $_SESSION['p2p_requestId'] = $result['requestId'] ?? '';
    $_SESSION['p2p_order_id']  = $order_id;
    header("Location: " . $result['processUrl']);
    exit();
} else {
    // Error — mostramos respuesta para depurar
    echo "<h3 style='font-family:sans-serif;color:#e05252;'>❌ Error al crear sesión de pago</h3>";
    echo "<p style='font-family:sans-serif;color:#f0f1f3;'>Orden <strong>#$order_id</strong> guardada en BD pero el pago no pudo iniciarse.</p>";
    echo "<pre style='background:#1e2128;color:#f0f1f3;padding:1rem;border-radius:8px;font-size:0.85rem;'>";
    print_r($result);
    echo "</pre>";
    echo "<a href='../home.php' style='color:#f0b429;font-family:sans-serif;'>← Volver al inicio</a>";
}
?>