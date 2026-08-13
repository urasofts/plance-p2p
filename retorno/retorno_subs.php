<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

// ══════════════════════════════════════════
// Conexión a BD
// ══════════════════════════════════════════
require_once '../php/conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
}

// ══════════════════════════════════════════
// Recibir sub_id desde la URL
// ══════════════════════════════════════════
$sub_id = intval($_GET['sub'] ?? 0);

if (!$sub_id) {
    header("Location: ../home.php");
    exit();
}

// Obtener request_id desde la BD
$sub_id_safe = mysqli_real_escape_string($conexion, $sub_id);
$row         = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM suscripciones WHERE id = '$sub_id_safe'"));
$request_id  = $row['request_id'] ?? '';

if (!$request_id) {
    header("Location: ../home.php");
    exit();
}

// ══════════════════════════════════════════
// Consultar estado a PlaceToPay
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
// Determinar estado del pago
// ══════════════════════════════════════════
$status_p2p = $result['status']['status'] ?? 'UNKNOWN';

if ($status_p2p === 'APPROVED') {
    $nuevo_estado = 'aprobada';
    $icono        = '✅';
    $color        = '#3ecf8e';
    $bg_icon      = 'rgba(62, 207, 142, 0.15)';
} elseif ($status_p2p === 'REJECTED') {
    $nuevo_estado = 'rechazada';
    $icono        = '❌';
    $titulo       = 'Pago rechazado';
    $mensaje      = 'Tu pago no pudo ser procesado. Intenta de nuevo.';
    $color        = '#e05252';
    $bg_icon      = 'rgba(224, 82, 82, 0.15)';
} elseif ($status_p2p === 'PENDING') {
    $nuevo_estado = 'pendiente';
    $icono        = '⏳';
    $titulo       = 'Pago pendiente';
    $mensaje      = 'Tu pago está siendo procesado. Te notificaremos pronto.';
    $color        = '#f0b429';
    $bg_icon      = 'rgba(240, 180, 41, 0.15)';
} else {
    $nuevo_estado = 'cancelada';
    $icono        = '🚫';
    $titulo       = 'Suscripción cancelada';
    $mensaje      = 'Cancelaste el proceso de pago.';
    $color        = '#8a8d96';
    $bg_icon      = 'rgba(138, 141, 150, 0.15)';
}

// ══════════════════════════════════════════
// Actualizar estado en BD
// ══════════════════════════════════════════
$sub_id_safe = mysqli_real_escape_string($conexion, $sub_id);
$estado_safe = mysqli_real_escape_string($conexion, $nuevo_estado);

// ══════════════════════════════════════════
// Extraer token de la respuesta de PlaceToPay
// ══════════════════════════════════════════
$token = '';

// Caso 1 — Tarjeta nueva: token en subscription.instrument
if (isset($result['subscription']['instrument']) && is_array($result['subscription']['instrument'])) {
    foreach ($result['subscription']['instrument'] as $item) {
        if (($item['keyword'] ?? '') === 'token') {
            $token = $item['value'] ?? '';
            break;
        }
    }
}

// Caso 2 — Tarjeta guardada: token en payment[0].subscription
if (empty($token) && isset($result['payment'][0]['subscription']) && is_array($result['payment'][0]['subscription'])) {
    foreach ($result['payment'][0]['subscription'] as $item) {
        if (($item['keyword'] ?? '') === 'token') {
            $token = $item['value'] ?? '';
            break;
        }
    }
}

// Caso 3 — Buscar en processorFields de payment[0] por si acaso
if (empty($token) && isset($result['payment'][0]['processorFields']) && is_array($result['payment'][0]['processorFields'])) {
    foreach ($result['payment'][0]['processorFields'] as $item) {
        if (($item['keyword'] ?? '') === 'token') {
            $token = $item['value'] ?? '';
            break;
        }
    }
}

// Actualizar estado y token en BD
$token_safe = mysqli_real_escape_string($conexion, $token);
mysqli_query($conexion, "UPDATE suscripciones SET estado = '$estado_safe', token = '$token_safe' WHERE id = '$sub_id_safe'");

// ── Definir título y mensaje según si hay token ──
if ($status_p2p === 'APPROVED') {
    if (!empty($token)) {
        $titulo  = '¡Suscripción activada!';
        $mensaje = 'Tu suscripción fue procesada y tu tarjeta quedó guardada para futuros cobros. ¡Disfrútala!';
    } else {
        $titulo  = '¡Pago aprobado!';
        $mensaje = 'Tu pago fue exitoso. Guarda tu tarjeta para activar la suscripción completa y agilizar futuros pagos.';
    }
}

// Obtener info de la suscripción (ya la tenemos en $row)
$subs = $row;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de suscripción</title>
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

        * { box-sizing: border-box; margin: 0; padding: 0; }

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
            /* border: 1px solid var(--border); */
            border-radius: 16px;
            padding: 2.5rem 2rem;
            max-width: 460px;
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
            font-size: 3rem;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            background: <?= $bg_icon ?>;
        }

        .result-title {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 800;
            color: <?= $color ?>;
            margin-bottom: 0.5rem;
            letter-spacing: 0.02em;
        }

        .result-message {
            font-size: 0.95rem;
            color: var(--pt-text-sec);
            margin-bottom: 1.5rem;
        }

        .order-details {
            background: var(--pt-bg-card);
            /* border: 1px solid var(--border); */
            border-radius: 10px;
            padding: 1rem 1.2rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            font-size: 0.875rem;
            /* border-bottom: 1px solid var(--border); */
            color: var(--pt-text);
        }

        .order-row:last-child { border-bottom: none; }
        .order-row span:first-child { color: var(--pt-text-sec); }
        .order-row span:last-child  { font-weight: 600; color: var(--pt-text); }

        .estado-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.78rem;
            font-weight: 700;
            font-family: var(--font-display);
            letter-spacing: 0.05em;
            background: <?= $bg_icon ?>;
            color: <?= $color ?>;
        }

        .btn-home {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: <?= $color ?>;
            color: #0d0e10;
            border: none;
            border-radius: 8px;
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-decoration: none;
            transition: opacity 0.2s;
            margin-right: 0.5rem;
        }
        .btn-home:hover { opacity: 0.85; color: #0d0e10; text-decoration: none; }

        .btn-volver {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: transparent;
            color: var(--pt-text-sec);
            border: 1px solid var(--pt-border);
            border-radius: 8px;
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-volver:hover { border-color: <?= $color ?>; color: <?= $color ?>; text-decoration: none; }

        .tip-box {
            background: rgba(240, 180, 41, 0.08);
            border: 1px solid rgba(240, 180, 41, 0.25);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.2rem;
            text-align: left;
            display: flex;
            gap: 0.6rem;
            align-items: flex-start;
        }
        .tip-box-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 0.1rem; }
        .tip-box-text { font-size: 0.82rem; color: #c99010; line-height: 1.5; }
        .tip-box-text strong { color: #f0b429; }

        .btn-tokenizar {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            justify-content: center;
            padding: 0.75rem 1.2rem;
            background: rgba(240,180,41,0.12);
            border: 1.5px solid #f0b429;
            color: #f0b429;
            border-radius: 8px;
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            text-decoration: none;
            margin-bottom: 0.8rem;
            transition: all 0.2s;
        }
        .btn-tokenizar:hover {
            background: rgba(240,180,41,0.25);
            color: #f0b429;
            text-decoration: none;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="result-card">

        <div class="result-icon"><?= $icono ?></div>
        <div class="result-title"><?= $titulo ?></div>
        <p class="result-message"><?= $mensaje ?></p>

        <?php if ($nuevo_estado === 'aprobada' && empty($token)): ?>
        <a href="../php/tokenizar.php?sub=<?= $sub_id ?>" class="btn-tokenizar">
            🔐 Guardar tarjeta para futuros cobros
        </a>
        <?php endif; ?>

        <?php if ($nuevo_estado === 'aprobada' && !empty($token)): ?>
        <div style="background:rgba(62,207,142,0.08);border:1px solid rgba(62,207,142,0.25);border-radius:8px;padding:0.7rem 1rem;margin-bottom:1.2rem;font-size:0.82rem;color:#3ecf8e;text-align:left;">
            🔐 <strong>Tarjeta guardada</strong> — Tus próximos pagos serán automáticos.
        </div>
        <?php endif; ?>

        <?php if ($subs): ?>
        <div class="order-details">
            <div class="order-row">
                <span>Suscripción #</span>
                <span><?= htmlspecialchars($subs['id']) ?></span>
            </div>
            <div class="order-row">
                <span>Plataforma</span>
                <span><?= htmlspecialchars($subs['plataforma']) ?></span>
            </div>
            <div class="order-row">
                <span>Plan</span>
                <span><?= htmlspecialchars($subs['plan']) ?></span>
            </div>
            <div class="order-row">
                <span>Correo</span>
                <span><?= htmlspecialchars($subs['usuario_id']) ?></span>
            </div>
            <div class="order-row">
                <span>Total</span>
                <span>$<?= number_format($subs['precio'], 0, ',', '.') ?> COP</span>
            </div>
            <div class="order-row">
                <span>Estado</span>
                <span><span class="estado-badge"><?= strtoupper($nuevo_estado) ?></span></span>
            </div>
        </div>
        <?php endif; ?>

        <a href="sesiones.php" class="btn-home">← Inicio</a>
        <a href="../views/plataformas/suscripciones.php" class="btn-volver">Volver al comercior</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/validaciones.js"></script>
</body>
</html>