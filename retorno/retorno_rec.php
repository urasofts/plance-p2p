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
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

// ══════════════════════════════════════════
// Recibir rec_id desde la URL
// ══════════════════════════════════════════
$rec_id = intval($_GET['rec'] ?? 0);

if (!$rec_id) {
    header("Location: ../home.php");
    exit();
}

// Obtener request_id desde la BD
$rec_id_safe = mysqli_real_escape_string($conexion, $rec_id);
$row         = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM recurrencias WHERE id = '$rec_id_safe'"));
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
    $titulo       = '¡Recurrencia activada!';
    $mensaje      = 'Tu membresía fue activada. Los cobros se realizarán automáticamente cada mes.';
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
    $titulo       = 'Recurrencia cancelada';
    $mensaje      = 'Cancelaste el proceso de pago.';
    $color        = '#8a8d96';
    $bg_icon      = 'rgba(138, 141, 150, 0.15)';
}

// ══════════════════════════════════════════
// Actualizar estado en BD
// ══════════════════════════════════════════
$estado_safe = mysqli_real_escape_string($conexion, $nuevo_estado);

// Si fue aprobada, guardar también fecha_fin
if ($nuevo_estado === 'aprobada') {
    $fecha_fin_safe = date('Y-m-d', strtotime('+12 months'));
    mysqli_query($conexion, "UPDATE recurrencias SET estado = '$estado_safe', fecha_fin = '$fecha_fin_safe' WHERE id = '$rec_id_safe'");
} else {
    mysqli_query($conexion, "UPDATE recurrencias SET estado = '$estado_safe' WHERE id = '$rec_id_safe'");
}

$rec = $row;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — Membresía Recurrente</title>
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
            max-width: 480px;
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
            width: 80px; height: 80px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
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

        .order-details {
            background: var(--pt-bg-card);
            /* border: 1px solid var(--pt-border); */
            border-radius: 10px;
            padding: 1rem 1.2rem;
            margin-bottom: 1rem;
            text-align: left;
        }

        .order-row {
            display: flex; justify-content: space-between;
            align-items: center; padding: 0.4rem 0;
            font-size: 0.875rem;
            /* border-bottom: 1px solid var(--pt-border); */
        }
        .order-row:last-child { border-bottom: none; }
        .order-row span:first-child { color: var(--pt-text-sec); }
        .order-row span:last-child  { font-weight: 600; color: var(--pt-text); }

        .estado-badge {
            display: inline-block; padding: 0.2rem 0.6rem;
            border-radius: 4px; font-size: 0.78rem; font-weight: 700;
            font-family: var(--font-display); letter-spacing: 0.05em;
            background: <?= $bg_icon ?>; color: <?= $color ?>;
        }

        /* Info recurrencia */
        .recurring-detail {
            background: rgba(77,159,255,0.08);
            border: 1px solid rgba(77,159,255,0.2);
            border-radius: 8px; padding: 0.75rem 1rem;
            margin-bottom: 1.2rem; text-align: left;
            display: flex; gap: 0.5rem; align-items: flex-start;
            font-size: 0.82rem; color: #7ab8ff;
        }
        .recurring-detail i { flex-shrink: 0; margin-top: 0.1rem; }

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

        <?php if ($rec): ?>
        <div class="order-details">
            <div class="order-row">
                <span>Recurrencia #</span>
                <span><?= htmlspecialchars($rec['id']) ?></span>
            </div>
            <div class="order-row">
                <span>Servicio</span>
                <span><?= htmlspecialchars($rec['servicio']) ?></span>
            </div>
            <div class="order-row">
                <span>Plan</span>
                <span><?= htmlspecialchars($rec['plan']) ?></span>
            </div>
            <div class="order-row">
                <span>Correo</span>
                <span><?= htmlspecialchars($rec['usuario_id']) ?></span>
            </div>
            <div class="order-row">
                <span>Total / mes</span>
                <span>$<?= number_format($rec['precio'], 0, ',', '.') ?> COP</span>
            </div>
            <div class="order-row">
                <span>Próximo cobro</span>
                <span><?= htmlspecialchars($rec['next_payment'] ?? 'N/A') ?></span>
            </div>
            <div class="order-row">
                <span>Fin de recurrencia</span>
                <span style="color:#f0b429;"><?= $nuevo_estado === 'aprobada' ? date('Y-m-d', strtotime('+12 months')) : 'N/A' ?></span>
            </div>
            <div class="order-row">
                <span>Estado</span>
                <span><span class="estado-badge"><?= strtoupper($nuevo_estado) ?></span></span>
            </div>
        </div>

        <?php if ($nuevo_estado === 'aprobada'): ?>
        <div class="recurring-detail">
            <i class="bi bi-arrow-repeat"></i>
            <span>
                Tu membresía se renovará automáticamente cada mes durante <strong>12 meses</strong>.<br>
                Próximo cobro: <strong><?= htmlspecialchars($rec['next_payment'] ?? 'N/A') ?></strong><br>
                Fin de recurrencia: <strong><?= date('Y-m-d', strtotime('+12 months')) ?></strong>
            </span>
        </div>
        <?php endif; ?>

        <?php endif; ?>

        <a href="../home.php" class="btn-home">← Inicio</a>
        <a href="../views/plataformas/suscripciones.php" class="btn-volver">Volver al comercio</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/validaciones.js"></script>
</body>
</html>