<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

$gw = $_SESSION['gw_mus_result'] ?? null;
unset($_SESSION['gw_mus_result']);

if (!$gw) {
    header("Location: ../home.php");
    exit();
}

$status       = $gw['status']    ?? 'FAILED';
$nuevo_estado = $gw['estado']    ?? 'rechazada';
$servicio     = $gw['servicio']  ?? '';
$plan         = $gw['plan']      ?? '';
$precio       = $gw['precio']    ?? 0;
$nombre       = $gw['nombre']    ?? '';
$correo       = $gw['correo']    ?? '';
$orden_id     = $gw['orden_id']  ?? '';
$reference    = $gw['reference'] ?? '';
$token        = $gw['token']     ?? '';
$message      = $gw['message']   ?? '';

if ($status === 'APPROVED') {
    $icono = '🔐'; $titulo = '¡Suscripción registrada!';
    $mensaje = 'Tu tarjeta fue tokenizada y la suscripción quedó activa correctamente.';
    $color = '#3ecf8e'; $bg_icon = 'rgba(62,207,142,0.15)';
} elseif ($status === 'PENDING') {
    $icono = '⏳'; $titulo = 'Proceso pendiente';
    $mensaje = 'Tu solicitud está siendo procesada.';
    $color = '#f0b429'; $bg_icon = 'rgba(240,180,41,0.15)';
} else {
    $icono = '❌'; $titulo = 'Proceso rechazado';
    $mensaje = !empty($message) ? $message : 'No se pudo procesar. Verifica los datos e intenta de nuevo.';
    $color = '#e05252'; $bg_icon = 'rgba(224,82,82,0.15)';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — Suscripción Música</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800&display=swap" rel="stylesheet">
        <?php require_once dirname(__DIR__) . '/php/theme.php'; ?>
    <style>
        :root{--bg-base:#0d0e10;--bg-surface:#16181c;--bg-card:#1e2128;--border:#2e3038;--text-primary:#f0f1f3;--text-secondary:#8a8d96;--font-display:'Barlow',sans-serif;--font-body:'Barlow',sans-serif;}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background-color:var(--pt-bg-base);color:var(--pt-text);font-family:var(--font-body);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
        .result-card{background:var(--pt-boxitem);/* border:1px solid var(--pt-border) */;border-radius:16px;padding:2.5rem 2rem;max-width:480px;width:100%;text-align:center;animation:fadeUp 0.4s ease both; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        .result-icon{font-size:3rem;width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;background:<?= $bg_icon ?>;}
        .result-title{font-family:var(--font-display);font-size:2rem;font-weight:800;color:<?= $color ?>;margin-bottom:0.5rem;letter-spacing:0.02em;}
        .result-message{font-size:0.95rem;color:var(--pt-text);margin-bottom:1.5rem;}
        .order-details{background:var(--pt-bg-card);/* border:1px solid var(--pt-border) */;border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;text-align:left;}
        .order-row{display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;font-size:0.875rem;/* border-bottom:1px solid var(--pt-border) */;}
        .order-row:last-child{border-bottom:none;}
        .order-row span:first-child{color:var(--pt-text-sec);}
        .order-row span:last-child{font-weight:600;color:var(--pt-text);}
        .estado-badge{display:inline-block;padding:0.2rem 0.6rem;border-radius:4px;font-size:0.78rem;font-weight:700;font-family:var(--font-display);letter-spacing:0.05em;background:<?= $bg_icon ?>;color:<?= $color ?>;}
        .gw-badge{background:rgba(29,185,84,0.12);border:1px solid rgba(29,185,84,0.2);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1.2rem;font-size:0.8rem;color:#86efac;display:flex;gap:0.5rem;align-items:center;}
        .token-box{background:rgba(62,207,142,0.08);border:1px solid rgba(62,207,142,0.2);border-radius:8px;padding:0.7rem 1rem;margin-bottom:1.2rem;font-size:0.8rem;color:#86efac;display:flex;gap:0.5rem;align-items:flex-start;text-align:left;}
        .btn-home{display:inline-block;padding:0.75rem 2rem;background:<?= $color ?>;color:#0d0e10;border:none;border-radius:8px;font-family:var(--font-display);font-size:1rem;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;text-decoration:none;transition:opacity 0.2s;margin-right:0.5rem;}
        .btn-home:hover{opacity:0.85;color:#0d0e10;text-decoration:none;}
        .btn-volver{display:inline-block;padding:0.75rem 1.5rem;background:transparent;color:var(--pt-text-sec);border:1px solid var(--pt-border);border-radius:8px;font-family:var(--font-display);font-size:1rem;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;text-decoration:none;transition:all 0.2s;}
        .btn-volver:hover{border-color:<?= $color ?>;color:<?= $color ?>;text-decoration:none;}
    </style>
</head>
<body>
    <div class="result-card">
        <div class="result-icon"><?= $icono ?></div>
        <div class="result-title"><?= $titulo ?></div>
        <p class="result-message"><?= htmlspecialchars($mensaje) ?></p>

        <div class="gw-badge">
            <i class="bi bi-cpu-fill"></i>
            Procesado via <strong>API Gateway</strong> · Evertec PlacetoPay
        </div>

        <?php if ($status === 'APPROVED' && !empty($token)): ?>
        <div class="token-box">
            <i class="bi bi-shield-lock-fill"></i>
            <span>🔐 <strong>Tarjeta tokenizada</strong> — Tu medio de pago quedó registrado de forma segura para futuros cobros.</span>
        </div>
        <?php endif; ?>

        <div class="order-details">
            <div class="order-row"><span>Orden #</span><span>#<?= htmlspecialchars($orden_id) ?></span></div>
            <div class="order-row"><span>Servicio</span><span><?= htmlspecialchars($servicio) ?></span></div>
            <div class="order-row"><span>Plan</span><span><?= htmlspecialchars($plan) ?></span></div>
            <div class="order-row"><span>Nombre</span><span><?= htmlspecialchars($nombre) ?></span></div>
            <div class="order-row"><span>Correo</span><span><?= htmlspecialchars($correo) ?></span></div>
            <div class="order-row"><span>Total</span><span style="color:<?= $color ?>;font-size:1.1rem;">$<?= number_format((float)$precio, 0, ',', '.') ?> COP</span></div>
            <div class="order-row"><span>Token</span><span style="color:<?= !empty($token) ? '#3ecf8e' : '#8a8d96' ?>;"><?= !empty($token) ? '✅ Guardado' : '— No tokenizado' ?></span></div>
            <?php if (!empty($reference)): ?>
            <div class="order-row"><span>Referencia</span><span style="font-size:0.78rem;color:var(--text-secondary);"><?= htmlspecialchars($reference) ?></span></div>
            <?php endif; ?>
            <div class="order-row"><span>Estado</span><span><span class="estado-badge"><?= strtoupper($nuevo_estado) ?></span></span></div>
        </div>

        <a href="../home.php" class="btn-home">← Inicio</a>
        <a href="../views/plataformas/music_gateway.php" class="btn-volver">Ver planes</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>