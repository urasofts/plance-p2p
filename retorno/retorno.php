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
// Recibir order_id desde la URL
// ══════════════════════════════════════════
$order_id   = intval($_GET['order'] ?? 0);
$request_id = $_SESSION['p2p_requestId'] ?? '';

if (!$order_id || !$request_id) {
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

// Mapear estado de PlaceToPay a nuestro estado en BD
if ($status_p2p === 'APPROVED') {
    $nuevo_estado = 'aprobada';
    $icono        = '✅';
    $titulo       = '¡Pago aprobado!';
    $mensaje      = 'Tu recarga fue procesada exitosamente.';
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
    // CANCELED u otro
    $nuevo_estado = 'cancelada';
    $icono        = '🚫';
    $titulo       = 'Pago cancelado';
    $mensaje      = 'Cancelaste el proceso de pago.';
    $color        = '#8a8d96';
    $bg_icon      = 'rgba(138, 141, 150, 0.15)';
}

// ══════════════════════════════════════════
// Actualizar estado en BD
// ══════════════════════════════════════════
$order_id_safe = mysqli_real_escape_string($conexion, $order_id);
$estado_safe   = mysqli_real_escape_string($conexion, $nuevo_estado);

mysqli_query($conexion, "UPDATE ordenes SET estado = '$estado_safe' WHERE id = '$order_id_safe'");

// Obtener info de la orden para mostrarla
$orden = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM ordenes WHERE id = '$order_id_safe'"));

// Limpiar sesión de P2P
unset($_SESSION['p2p_requestId'], $_SESSION['p2p_order_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.min.js"></script>
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
            background-color: var(--bg-base);
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
            position: relative;
        }

        /* Spinner animado para estado pendiente */
        .pending-spinner {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .pending-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: <?= $color ?>;
            border-right-color: <?= $color ?>;
        }
        .pending-dots { display: flex; gap: 5px; }
        .pending-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: <?= $color ?>;
        }
        .pending-label {
            font-size: 0.78rem;
            color: <?= $color ?>;
            margin-top: 0.6rem;
            font-weight: 600;
            letter-spacing: 0.03em;
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
            color: var(--pt-text);
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
        .order-row { opacity: 0; }

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
        }
        .btn-home:hover { opacity: 0.85; color: #0d0e10; text-decoration: none; }
    </style>
</head>
<body>
    <div class="result-card">

        <?php if ($status_p2p === 'PENDING'): ?>
        <div class="result-icon">
            <div class="pending-spinner">
                <div class="pending-ring" id="pendingRing"></div>
                <div class="pending-dots" id="pendingDots">
                    <span class="pending-dot"></span>
                    <span class="pending-dot"></span>
                    <span class="pending-dot"></span>
                </div>
            </div>
        </div>
        <div class="pending-label">Procesando...</div>
        <?php else: ?>
        <div class="result-icon"><?= $icono ?></div>
        <?php endif; ?>
        <div class="result-title"><?= $titulo ?></div>
        <p class="result-message"><?= $mensaje ?></p>

        <?php if ($orden): ?>
        <div class="order-details">
            <div class="order-row">
                <span>Orden #</span>
                <span><?= htmlspecialchars($orden['id']) ?></span>
            </div>
            <div class="order-row">
                <span>Producto</span>
                <span><?= htmlspecialchars($orden['producto']) ?></span>
            </div>
            <div class="order-row">
                <span>ID Jugador</span>
                <span><?= htmlspecialchars($orden['jugador_id']) ?></span>
            </div>
            <div class="order-row">
                <span>Total</span>
                <span>$<?= number_format($orden['precio'], 0, ',', '.') ?> COP</span>
            </div>
            <div class="order-row">
                <span>Estado</span>
                <span><span class="estado-badge"><?= strtoupper($nuevo_estado) ?></span></span>
            </div>
        </div>
        <?php endif; ?>

        <a href="../views/games/juegos.php" class="btn-home">← Volver al comercio</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if (typeof anime !== 'undefined') {
            // Anillo girando
            anime({
                targets: '#pendingRing',
                rotate: '360deg',
                duration: 1100,
                easing: 'linear',
                loop: true
            });
            // Puntitos pulsando en cascada
            anime({
                targets: '#pendingDots .pending-dot',
                scale: [1, 1.5, 1],
                opacity: [0.4, 1, 0.4],
                delay: anime.stagger(160, { start: 0 }),
                duration: 900,
                easing: 'easeInOutSine',
                loop: true
            });
            // Entrada de la card de detalles
            anime({
                targets: '.order-row',
                opacity: [0, 1],
                translateX: [-10, 0],
                delay: anime.stagger(70, { start: 200 }),
                duration: 450,
                easing: 'easeOutQuad'
            });
        }
    </script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/validaciones.js"></script>
</body>
</html>