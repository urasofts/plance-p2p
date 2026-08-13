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

$mix = $_SESSION['mix_result'] ?? null;
unset($_SESSION['mix_result']);

if (!$mix) {
    header("Location: ../home.php");
    exit();
}

$orden_id      = $mix['orden_id']      ?? 0;
$productos     = $mix['productos']     ?? '';
$total         = $mix['total']         ?? 0;
$monto_parcial = $mix['monto_parcial'] ?? $total;
$allow_partial = $mix['allow_partial'] ?? false;
$reference     = $mix['reference']     ?? '';
$requestId     = $mix['requestId']     ?? null;
$mix_status    = $mix['status']        ?? 'FAILED';

// Si venimos del checkout de PlacetoPay, consultamos el estado real
$nuevo_estado  = 'pendiente';
$estado_final  = 'PENDING';
$monto_pagado  = null;

if ($requestId) {
    $login     = "2d9eaf1e662518756a3d78806543af5b";
    $secretKey = "3YC5brb5eAR4xBGQ";
    $seed      = date('c');
    $nonce     = bin2hex(random_bytes(16));
    $tranKey   = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));
    $nonceB64  = base64_encode($nonce);

    $queryBody = json_encode([
        "auth" => ["login" => $login, "tranKey" => $tranKey, "nonce" => $nonceB64, "seed" => $seed]
    ]);

    $ch = curl_init("https://checkout-test.placetopay.com/api/session/{$requestId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $queryBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT,        15);
    $resp   = curl_exec($ch);
    curl_close($ch);

    $data         = json_decode($resp, true);
    $estado_final = $data['status']['status'] ?? 'PENDING';

    // Si tiene transacciones, tomamos el monto pagado real
    if (!empty($data['payment'])) {
        $pago         = $data['payment'][0] ?? [];
        $monto_pagado = $pago['amount']['from']['total'] ?? $monto_parcial;
        $estado_final = $pago['status']['status'] ?? $estado_final;
    }

    $nuevo_estado = match($estado_final) {
        'APPROVED' => 'aprobada',
        'PENDING'  => 'pendiente',
        default    => 'rechazada'
    };

    // Actualizar BD
    $est_safe  = mysqli_real_escape_string($conexion, $nuevo_estado);
    $monto_safe = $monto_pagado ? (float)$monto_pagado : 'NULL';
    mysqli_query($conexion, "UPDATE ordenes SET estado='$est_safe', monto_pagado=$monto_safe WHERE id=$orden_id");
}

// Colores según estado
if ($estado_final === 'APPROVED') {
    $icono = '✅'; $titulo = '¡Pago aprobado!';
    $color = '#3ecf8e'; $bg_icon = 'rgba(62,207,142,0.15)';
    $mensaje = $allow_partial
        ? '¡Pago parcial procesado exitosamente! El saldo restante quedará pendiente.'
        : '¡Tu pago fue procesado exitosamente!';
} elseif ($estado_final === 'PENDING') {
    $icono = '⏳'; $titulo = 'Pago pendiente';
    $color = '#f0b429'; $bg_icon = 'rgba(240,180,41,0.15)';
    $mensaje = 'Tu pago está siendo procesado. Te notificaremos cuando se complete.';
} else {
    $icono = '❌'; $titulo = 'Pago rechazado';
    $color = '#e05252'; $bg_icon = 'rgba(224,82,82,0.15)';
    $mensaje = 'No se pudo procesar el pago. Por favor intenta de nuevo.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — Pago Mixto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <?php require_once dirname(__DIR__) . '/php/theme.php'; ?>
    <style>
        :root{--bg:#0d0e10;--surface:#16181c;--card:#1e2128;--border:#2e3038;--text:#f0f1f3;--muted:#8a8d96;--font-d:'Barlow',sans-serif;--font-b:'Barlow',sans-serif;}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background:var(--pt-bg-base);color:var(--pt-text);font-family:var(--font-b);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
        .result-card{background:var(--pt-boxitem);/* border:1px solid var(--pt-border) */;border-radius:16px;padding:2.5rem 2rem;max-width:500px;width:100%;text-align:center;animation:fadeUp 0.4s ease both; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        .result-icon{font-size:3rem;width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;background:<?= $bg_icon ?>;}
        .result-title{font-family:var(--font-d);font-size:2rem;font-weight:800;color:<?= $color ?>;margin-bottom:0.5rem;letter-spacing:0.02em;}
        .result-msg{font-size:0.9rem;color:var(--pt-text);margin-bottom:1.5rem;line-height:1.6;}

        /* Badge mixto */
        .mix-badge{background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1.2rem;font-size:0.8rem;color:#93c5fd;display:flex;gap:0.5rem;align-items:center;}

        /* Desglose de pago */
        .pago-breakdown{background:rgba(<?= $estado_final==='APPROVED'?'62,207,142':'240,180,41' ?>,0.07);border:1px solid rgba(<?= $estado_final==='APPROVED'?'62,207,142':'240,180,41' ?>,0.2);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;text-align:left;}
        .breakdown-title{font-family:var(--font-d);font-size:0.75rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--pt-text-sec);margin-bottom:0.6rem;}
        .breakdown-row{display:flex;justify-content:space-between;align-items:center;padding:0.35rem 0;font-size:0.85rem;border-bottom:1px solid rgba(255,255,255,0.05);}
        .breakdown-row:last-child{border-bottom:none;}
        .breakdown-row span:first-child{color:var(--pt-text-sec);}
        .breakdown-row span:last-child{font-weight:700;}

        .order-details{background:var(--pt-bg-card);/* border:1px solid var(--pt-border) */;border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;text-align:left;}
        .order-row{display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;font-size:0.875rem;/* border-bottom:1px solid var(--pt-border) */;}
        .order-row:last-child{border-bottom:none;}
        .order-row span:first-child{color:var(--pt-text-sec);}
        .order-row span:last-child{font-weight:600;}
        .estado-badge{display:inline-block;padding:0.2rem 0.6rem;border-radius:4px;font-size:0.78rem;font-weight:700;font-family:var(--font-d);letter-spacing:0.05em;background:<?= $bg_icon ?>;color:<?= $color ?>;}

        .btn-home{display:inline-block;padding:0.75rem 2rem;background:<?= $color ?>;color:#0d0e10;border:none;border-radius:8px;font-family:var(--font-d);font-size:1rem;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;text-decoration:none;transition:opacity 0.2s;margin-right:0.5rem;}
        .btn-home:hover{opacity:0.85;color:#0d0e10;text-decoration:none;}
        .btn-volver{display:inline-block;padding:0.75rem 1.5rem;background:transparent;color:var(--pt-text-sec);border:1px solid var(--pt-border);border-radius:8px;font-family:var(--font-d);font-size:1rem;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;text-decoration:none;transition:all 0.2s;}
        .btn-volver:hover{border-color:<?= $color ?>;color:<?= $color ?>;text-decoration:none;}
    </style>
</head>
<body>
    <div class="result-card">
        <div class="result-icon"><?= $icono ?></div>
        <div class="result-title"><?= $titulo ?></div>
        <p class="result-msg"><?= $mensaje ?></p>

        <div class="mix-badge">
            <i class="bi bi-shuffle"></i>
            <?= $allow_partial ? 'Pago <strong>mixto / parcial</strong> · Web Checkout · PlacetoPay' : 'Pago <strong>múltiple</strong> · Web Checkout · PlacetoPay' ?>
        </div>

        <?php if ($allow_partial): ?>
        <div class="pago-breakdown">
            <div class="breakdown-title">Desglose del pago</div>
            <div class="breakdown-row">
                <span>Total del pedido</span>
                <span>$<?= number_format($total, 0, ',', '.') ?> COP</span>
            </div>
            <div class="breakdown-row">
                <span>Monto pagado ahora</span>
                <span style="color:<?= $color ?>;">$<?= number_format($monto_pagado ?? $monto_parcial, 0, ',', '.') ?> COP</span>
            </div>
            <div class="breakdown-row">
                <span>Saldo restante</span>
                <span style="color:#f0b429;">$<?= number_format($total - ($monto_pagado ?? $monto_parcial), 0, ',', '.') ?> COP</span>
            </div>
        </div>
        <?php endif; ?>

        <div class="order-details">
            <div class="order-row"><span>Orden #</span><span>#<?= $orden_id ?></span></div>
            <div class="order-row"><span>Productos</span><span style="font-size:0.8rem;text-align:right;max-width:60%;"><?= htmlspecialchars($productos) ?></span></div>
            <div class="order-row"><span>Referencia</span><span style="font-size:0.78rem;color:var(--muted);"><?= htmlspecialchars($reference) ?></span></div>
            <div class="order-row">
                <span>Estado</span>
                <span><span class="estado-badge"><?= strtoupper($nuevo_estado) ?></span></span>
            </div>
        </div>

        <a href="../home.php" class="btn-home">← Inicio</a>
        <a href="../views/games/juegos.php" class="btn-volver">Volver al comercio</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>