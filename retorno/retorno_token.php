<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

require_once '../php/conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$sub_id     = intval($_GET['sub'] ?? $_SESSION['token_sub_id'] ?? 0);
$request_id = $_SESSION['token_requestId'] ?? '';

if (!$sub_id || !$request_id) {
    header("Location: ../home.php");
    exit();
}

// ══════════════════════════════════════════
// Consultar resultado de tokenización
// ══════════════════════════════════════════
$login     = "2d9eaf1e662518756a3d78806543af5b";
$secretKey = "3YC5brb5eAR4xBGQ";
$url       = "https://checkout-test.placetopay.com/api/session/" . $request_id;

$seed     = date('c');
$nonce    = bin2hex(random_bytes(16));
$tranKey  = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
$nonceB64 = base64_encode($nonce);

$auth = [
    "auth" => [
        "login"   => $login,
        "tranKey" => $tranKey,
        "nonce"   => $nonceB64,
        "seed"    => $seed
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($auth));
curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// ══════════════════════════════════════════
// Extraer token
// ══════════════════════════════════════════
$token      = '';
$status_p2p = $result['status']['status'] ?? 'UNKNOWN';

if (isset($result['subscription']['instrument']) && is_array($result['subscription']['instrument'])) {
    foreach ($result['subscription']['instrument'] as $item) {
        if (($item['keyword'] ?? '') === 'token') {
            $token = $item['value'] ?? '';
            break;
        }
    }
}

// ══════════════════════════════════════════
// Guardar token en BD y actualizar estado
// ══════════════════════════════════════════
$sub_id_safe = mysqli_real_escape_string($conexion, $sub_id);
$token_safe  = mysqli_real_escape_string($conexion, $token);

if (!empty($token)) {
    // Tiene token → suscripción completamente activada
    mysqli_query($conexion, "UPDATE suscripciones SET token = '$token_safe', estado = 'aprobada' WHERE id = '$sub_id_safe'");
    $exito   = true;
    $titulo  = '🔐 ¡Tarjeta guardada!';
    $mensaje = 'Tu tarjeta fue tokenizada exitosamente. Tu suscripción está completamente activada.';
    $color   = '#3ecf8e';
    $bg_icon = 'rgba(62,207,142,0.15)';
    $icono   = '✅';
} else {
    $exito   = false;
    $titulo  = 'No se pudo guardar';
    $mensaje = 'No logramos tokenizar tu tarjeta. Puedes intentarlo nuevamente desde tu historial.';
    $color   = '#e05252';
    $bg_icon = 'rgba(224,82,82,0.15)';
    $icono   = '❌';
}

// Limpiar sesión
unset($_SESSION['token_requestId'], $_SESSION['token_sub_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — Tokenización</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <?php require_once dirname(__DIR__) . '/php/theme.php'; ?>
    <style>
        :root {
            --bg-base:    #0d0e10;
            --bg-surface: #16181c;
            --bg-card:    #1e2128;
            --border:     #2e3038;
            --text-primary:   #f0f1f3;
            --text-secondary: #8a8d96;
            --font-display: 'Barlow', sans-serif;
            --font-body:    'Barlow', sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--pt-bg-base);
            color: var(--pt-text);
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .result-card {
            background: var(--pt-boxitem);
            /* border: 1px solid var(--pt-border); */
            border-radius: 16px;
            padding: 2.5rem 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            animation: fadeUp 0.4s ease both;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .result-icon {
            font-size: 3rem; width: 80px; height: 80px;
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
            margin: 0 auto 1.2rem;
            background: <?= $bg_icon ?>;
        }
        .result-title {
            font-family: var(--font-display);
            font-size: 2rem; font-weight: 800;
            color: <?= $color ?>;
            margin-bottom: 0.5rem; letter-spacing: 0.02em;
        }
        .result-message {
            font-size: 0.95rem;
            color: var(--pt-text);
            margin-bottom: 1.5rem;
        }
        .btn-home {
            display: inline-block; padding: 0.75rem 2rem;
            background: <?= $color ?>; color: #0d0e10;
            border: none; border-radius: 8px;
            font-family: var(--font-display); font-size: 1rem;
            font-weight: 800; letter-spacing: 0.05em;
            text-transform: uppercase; text-decoration: none;
            transition: opacity 0.2s; margin-right: 0.5rem;
        }
        .btn-home:hover { opacity: 0.85; color: #0d0e10; text-decoration: none; }
        .btn-volver {
            display: inline-block; padding: 0.75rem 1.5rem;
            background: transparent; color: var(--pt-text-sec);
            border: 1px solid var(--pt-border); border-radius: 8px;
            font-family: var(--font-display); font-size: 1rem;
            font-weight: 700; letter-spacing: 0.05em;
            text-transform: uppercase; text-decoration: none;
            transition: all 0.2s;
        }
        .btn-volver:hover { border-color: <?= $color ?>; color: <?= $color ?>; text-decoration: none; }
    </style>
</head>
<body>
    <div class="result-card">
        <div class="result-icon"><?= $icono ?></div>
        <div class="result-title"><?= $titulo ?></div>
        <p class="result-message"><?= $mensaje ?></p>

        <a href="../home.php" class="btn-home">← Inicio</a>
        <a href="../views/plataformas/streaming.php" class="btn-volver">Ver planes</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>