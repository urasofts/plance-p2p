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

$pre = $_SESSION['pre_result'] ?? null;
unset($_SESSION['pre_result']);

// Leer reserva_id desde GET (viene del returnUrl) o desde sesión
$reserva_id = (int)($_GET['reserva_id'] ?? ($pre['reserva_id'] ?? 0));

if (!$reserva_id) {
    header("Location: ../home.php");
    exit();
}

// Traer datos desde BD
$row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM reservaciones WHERE id = $reserva_id"));
if (!$row) {
    header("Location: ../home.php");
    exit();
}

$habitacion = $row['habitacion'];
$total      = (float)$row['precio'];
$correo     = $row['usuario_id'];
$reference  = $row['request_id'];  // referencia PRE-XXXX
$requestId  = $row['session_id'];  // requestId numérico de PlacetoPay

// Extraer checkin/checkout de la descripcion
$descripcion = $row['descripcion'];
preg_match('/checkin: (.+) al (.+)\)/', $descripcion, $matches);
$checkin  = $matches[1] ?? '';
$checkout = $matches[2] ?? '';
$noches   = $checkin && $checkout ? (int)((strtotime($checkout) - strtotime($checkin)) / 86400) : 1;
$nombre   = $pre['nombre'] ?? '';

// Consultar estado real en PlacetoPay
$nuevo_estado = 'pendiente';
$gw_status    = 'PENDING';
$gw_reason    = '';

if ($requestId) {
    $login     = "62f3eeeb7655485cbf65b306b4585dfd";
    $secretKey = "K8zGmmoark19y2ey";
    $seed      = date('c');
    $nonce     = bin2hex(random_bytes(16));
    $tranKey   = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
    $nonceB64  = base64_encode($nonce);

    $ch = curl_init("https://checkout-test.placetopay.com/api/session/{$requestId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode(["auth" => ["login" => $login, "tranKey" => $tranKey, "nonce" => $nonceB64, "seed" => $seed]]));
    curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT,        15);
    $resp = curl_exec($ch);
    curl_close($ch);

    $data      = json_decode($resp, true);
    $gw_status = $data['status']['status'] ?? 'PENDING';
    $gw_reason = $data['status']['reason'] ?? '';

    if (!empty($data['payment'])) {
        $gw_status = $data['payment'][0]['status']['status'] ?? $gw_status;
        $gw_reason = $data['payment'][0]['status']['reason'] ?? $gw_reason;
    }

    $nuevo_estado = match($gw_status) {
        'APPROVED' => 'aprobada',
        'PENDING'  => 'pendiente',
        default    => 'rechazada'
    };

    // Actualizar BD
    $est_safe = mysqli_real_escape_string($conexion, $nuevo_estado);
    mysqli_query($conexion, "UPDATE reservaciones SET estado='$est_safe' WHERE id=$reserva_id");
}

// Colores y textos según estado
if ($gw_status === 'APPROVED') {
    $icono   = '🏨'; $titulo  = '¡Reserva confirmada!';
    $mensaje = 'Tu habitación ha sido reservada exitosamente. La preautorización fue aprobada — tu tarjeta no ha sido cobrada aún.';
    $color   = '#3ecf8e'; $bg_icon = 'rgba(62,207,142,0.15)';
} elseif ($gw_status === 'PENDING') {
    $icono   = '⏳'; $titulo  = 'Reserva pendiente';
    $mensaje = 'Tu reserva está siendo procesada. Te notificaremos cuando se confirme la preautorización.';
    $color   = '#f0b429'; $bg_icon = 'rgba(240,180,41,0.15)';
} elseif ($gw_reason === 'EX') {
    $icono   = '⏱️'; $titulo  = 'Sesión expirada';
    $mensaje = 'El tiempo para completar la preautorización se agotó y la sesión de pago venció. Tu tarjeta no fue afectada. Vuelve a intentar la reserva.';
    $color   = '#f0b429'; $bg_icon = 'rgba(240,180,41,0.15)';
} elseif ($gw_reason === '¬C') {
    $icono   = '✋'; $titulo  = 'Pago cancelado';
    $mensaje = 'Cancelaste el proceso de preautorización antes de completarlo. Tu tarjeta no fue afectada. Puedes volver a intentar la reserva cuando quieras.';
    $color   = '#8a8d96'; $bg_icon = 'rgba(138,141,150,0.15)';
} else {
    $icono   = '❌'; $titulo  = 'Reserva rechazada';
    $mensaje = 'No se pudo procesar la preautorización. Por favor intenta con otra tarjeta o contacta a tu banco.';
    $color   = '#e05252'; $bg_icon = 'rgba(224,82,82,0.15)';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — Preautorización</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <?php require_once dirname(__DIR__) . '/php/theme.php'; ?>
    <style>
        :root{--bg:#0d0e10;--surface:#16181c;--card:#1e2128;--border:#2e3038;--text:#f0f1f3;--muted:#8a8d96;--font-d:'Barlow',sans-serif;--font-b:'Barlow',sans-serif;}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background:var(--bg);color:var(--text);font-family:var(--font-b);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
        .result-card{background:var(--pt-boxitem);border:1px solid var(--pt-border);border-radius:16px;padding:2.5rem 2rem;max-width:500px;width:100%;text-align:center;animation:fadeUp 0.4s ease both;  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        .result-icon{font-size:3rem;width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;background:<?= $bg_icon ?>;}
        .result-title{font-family:var(--font-d);font-size:2rem;font-weight:800;color:<?= $color ?>;margin-bottom:0.5rem;letter-spacing:0.02em;}
        .result-msg{font-size:0.9rem;color:var(--muted);margin-bottom:1.5rem;line-height:1.6;}

        .pre-badge{background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.25);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1.2rem;font-size:0.8rem;color:#a5b4fc;display:flex;gap:0.5rem;align-items:center;justify-content:center;}

        /* Fechas de estadía */
        .stay-box{background:rgba(99,102,241,0.07);border:1px solid rgba(99,102,241,0.2);border-radius:10px;padding:0.9rem 1.2rem;margin-bottom:1.2rem;display:flex;justify-content:space-around;align-items:center;}
        .stay-item{text-align:center;}
        .stay-label{font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--muted);margin-bottom:0.2rem;}
        .stay-date{font-family:var(--font-d);font-size:1.1rem;font-weight:800;color:#a5b4fc;}
        .stay-sep{color:var(--muted);font-size:1.2rem;}

        .order-details{background:var(--pt-bg-card);border:1px solid var(--pt-border);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;text-align:left;}
        .order-row{display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;font-size:0.875rem;border-bottom:1px solid var(--pt-border);}
        .order-row:last-child{border-bottom:none;}
        .order-row span:first-child{color:var(--muted);}
        .order-row span:last-child{font-weight:600;}
        .estado-badge{display:inline-block;padding:0.2rem 0.6rem;border-radius:4px;font-size:0.78rem;font-weight:700;font-family:var(--font-d);letter-spacing:0.05em;background:<?= $bg_icon ?>;color:<?= $color ?>;}

        /* Aviso preauth */
        .preauth-aviso{background:rgba(240,180,41,0.07);border:1px solid rgba(240,180,41,0.2);border-radius:8px;padding:0.7rem 1rem;margin-bottom:1.2rem;font-size:0.8rem;color:#fbbf24;text-align:left;line-height:1.5;}

        .btn-home{display:inline-block;padding:0.75rem 2rem;background:<?= $color ?>;color:#0d0e10;border:none;border-radius:8px;font-family:var(--font-d);font-size:1rem;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;text-decoration:none;transition:opacity 0.2s;margin-right:0.5rem;}
        .btn-home:hover{opacity:0.85;color:#0d0e10;text-decoration:none;}
        .btn-volver{display:inline-block;padding:0.75rem 1.5rem;background:transparent;color:var(--muted);border:1px solid var(--pt-border);border-radius:8px;font-family:var(--font-d);font-size:1rem;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;text-decoration:none;transition:all 0.2s;}
        .btn-volver:hover{border-color:<?= $color ?>;color:<?= $color ?>;text-decoration:none;}
    </style>
</head>
<body>
    <div class="result-card">
        <div class="result-icon"><?= $icono ?></div>
        <div class="result-title"><?= $titulo ?></div>
        <p class="result-msg"><?= $mensaje ?></p>

        <div class="pre-badge">
            <i class="bi bi-shield-lock-fill"></i>
            Preautorización (Check-in) · Web Checkout · PlacetoPay
        </div>

        <?php if ($gw_status === 'APPROVED'): ?>
        <!-- Fechas de estadía -->
        <div class="stay-box">
            <div class="stay-item">
                <div class="stay-label">Check-in</div>
                <div class="stay-date"><?= htmlspecialchars($checkin) ?></div>
            </div>
            <div class="stay-sep">→</div>
            <div class="stay-item">
                <div class="stay-label">Check-out</div>
                <div class="stay-date"><?= htmlspecialchars($checkout) ?></div>
            </div>
            <div class="stay-sep">·</div>
            <div class="stay-item">
                <div class="stay-label">Noches</div>
                <div class="stay-date"><?= $noches ?></div>
            </div>
        </div>

        <div class="preauth-aviso">
            ⚠️ <strong>Recuerda:</strong> Esta es una <strong>preautorización</strong> — tu tarjeta no ha sido cobrada. El cargo se realizará al momento del check-out en el hotel.
        </div>
        <?php endif; ?>

        <div class="order-details">
            <div class="order-row"><span>Reserva #</span><span>#<?= $reserva_id ?></span></div>
            <div class="order-row"><span>Habitación</span><span><?= htmlspecialchars($habitacion) ?></span></div>
            <div class="order-row"><span>Huésped</span><span><?= htmlspecialchars($nombre) ?></span></div>
            <div class="order-row"><span>Correo</span><span><?= htmlspecialchars($correo) ?></span></div>
            <div class="order-row">
                <span>Monto preautorizado</span>
                <span style="color:<?= $color ?>;font-size:1.05rem;">$<?= number_format($total, 0, ',', '.') ?> COP</span>
            </div>
            <div class="order-row"><span>Referencia</span><span style="font-size:0.78rem;color:var(--muted);"><?= htmlspecialchars($reference) ?></span></div>
            <div class="order-row">
                <span>Estado</span>
                <span><span class="estado-badge"><?= strtoupper($nuevo_estado) ?></span></span>
            </div>
        </div>

        <a href="../home.php" class="btn-home">← Inicio</a>
        <a href="../views/reservaciones/hotel.php" class="btn-volver">Ver habitaciones</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>