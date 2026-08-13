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

$data      = $_SESSION['continuar_result'] ?? null;
$orden_id  = (int)($_GET['orden_id'] ?? ($data['orden_id'] ?? 0));
unset($_SESSION['continuar_result']);

if (!$orden_id) { header("Location: ../home.php"); exit(); }

// Traer orden actualizada
$row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM ordenes WHERE id = $orden_id"));
if (!$row) { header("Location: ../home.php"); exit(); }

$total        = (float)$row['precio'];
$monto_previo = (float)$row['monto_pagado'];
$saldo_rest   = $total - $monto_previo;
$requestId    = $data['requestId'] ?? null;

// Consultar estado en PlacetoPay
$nuevo_estado = 'pendiente';
$monto_ahora  = 0;

if ($requestId) {
    $login     = "2d9eaf1e662518756a3d78806543af5b";
    $secretKey = "3YC5brb5eAR4xBGQ";
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

    $rdata       = json_decode($resp, true);
    $gw_status   = $rdata['status']['status'] ?? 'PENDING';

    if (!empty($rdata['payment'])) {
        $pago       = $rdata['payment'][0] ?? [];
        $monto_ahora = (float)($pago['amount']['from']['total'] ?? $saldo_rest);
        $gw_status  = $pago['status']['status'] ?? $gw_status;
    } else {
        $monto_ahora = $saldo_rest;
    }

    $nuevo_estado = match($gw_status) {
        'APPROVED' => 'aprobada',
        'PENDING'  => 'pendiente',
        default    => 'rechazada'
    };

    // Actualizar BD — sumar el nuevo monto al previo
    if ($nuevo_estado === 'aprobada') {
        $nuevo_monto = $monto_previo + $monto_ahora;
        $nuevo_monto_safe = (float)$nuevo_monto;
        mysqli_query($conexion, "UPDATE ordenes SET monto_pagado = $nuevo_monto_safe WHERE id = $orden_id");
        $saldo_final = $total - $nuevo_monto;
    } else {
        $nuevo_monto = $monto_previo;
        $saldo_final = $saldo_rest;
    }
} else {
    $nuevo_monto = $monto_previo;
    $saldo_final = $saldo_rest;
    $monto_ahora = $saldo_rest;
}

// Colores
if ($nuevo_estado === 'aprobada') {
    $icono = $saldo_final <= 0 ? '🎉' : '✅';
    $titulo = $saldo_final <= 0 ? '¡Pago completado!' : '¡Abono registrado!';
    $mensaje = $saldo_final <= 0
        ? '¡Excelente! Completaste el pago total de tu pedido.'
        : 'Tu abono fue registrado. Aún tienes un saldo pendiente.';
    $color = '#3ecf8e'; $bg_icon = 'rgba(62,207,142,0.15)';
} elseif ($nuevo_estado === 'pendiente') {
    $icono = '⏳'; $titulo = 'Pago pendiente';
    $mensaje = 'Tu pago está siendo procesado.';
    $color = '#f0b429'; $bg_icon = 'rgba(240,180,41,0.15)';
} else {
    $icono = '❌'; $titulo = 'Pago rechazado';
    $mensaje = 'No se pudo procesar el pago. Por favor intenta de nuevo.';
    $color = '#e05252'; $bg_icon = 'rgba(224,82,82,0.15)';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — Continuación de pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <?php require_once dirname(__DIR__) . '/php/theme.php'; ?>
    <style>
        :root{--bg:#0d0e10;--surface:#16181c;--card:#1e2128;--border:#2e3038;--text:#f0f1f3;--muted:#8a8d96;--font-d:'Barlow',sans-serif;--font-b:'Barlow',sans-serif;}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background:var(--pt-bg-base);color:var(--pt-text);font-family:var(--font-b);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
        .result-card{background:var(--pt-boxitem);/* border:1px solid var(--pt-border) */;border-radius:16px;padding:2.5rem 2rem;max-width:500px;width:100%;text-align:center;animation:fadeUp 0.4s ease both; gap: 10px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        .result-icon{font-size:3rem;width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;background:<?= $bg_icon ?>;}
        .result-title{font-family:var(--font-d);font-size:2rem;font-weight:800;color:<?= $color ?>;margin-bottom:0.5rem;letter-spacing:0.02em;}
        .result-msg{font-size:0.9rem;color:var(--pt-text);margin-bottom:1.5rem;line-height:1.6;}

        .mix-badge{background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1.2rem;font-size:0.8rem;color:#93c5fd;display:flex;gap:0.5rem;align-items:center;justify-content:center;}

        .breakdown{background:rgba(62,207,142,0.07);border:1px solid rgba(62,207,142,0.2);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;text-align:left;}
        .breakdown-title{font-family:var(--font-d);font-size:0.73rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--pt-text-sec);margin-bottom:0.6rem;}
        .breakdown-row{display:flex;justify-content:space-between;align-items:center;padding:0.35rem 0;font-size:0.85rem;border-bottom:1px solid rgba(255,255,255,0.05);}
        .breakdown-row:last-child{border-bottom:none;padding-top:0.5rem;margin-top:0.2rem;}

        .order-details{background:var(--pt-bg-card);/* border:1px solid var(--pt-border) */;border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;text-align:left;}
        .order-row{display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;font-size:0.875rem;/* border-bottom:1px solid var(--pt-border) */;}
        .order-row:last-child{border-bottom:none;}
        .order-row span:first-child{color:var(--pt-text-sec);}
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
            Continuación de pago mixto · Web Checkout · PlacetoPay
        </div>

        <!-- Desglose actualizado -->
        <div class="breakdown">
            <div class="breakdown-title">Desglose del pago</div>
            <div class="breakdown-row">
                <span>Total del pedido</span>
                <span style="font-weight:700;">$<?= number_format($total, 0, ',', '.') ?> COP</span>
            </div>
            <div class="breakdown-row">
                <span>Abono anterior</span>
                <span style="color:#8a8d96;">$<?= number_format($monto_previo, 0, ',', '.') ?> COP</span>
            </div>
            <div class="breakdown-row">
                <span>Abono ahora</span>
                <span style="color:#3ecf8e;font-weight:700;">$<?= number_format($monto_ahora, 0, ',', '.') ?> COP</span>
            </div>
            <div class="breakdown-row">
                <span style="font-weight:700;">Saldo restante</span>
                <span style="color:<?= $saldo_final <= 0 ? '#3ecf8e' : '#f0b429' ?>;font-weight:800;font-size:1rem;">
                    <?= $saldo_final <= 0 ? '✅ $0 — Pagado completo' : '$' . number_format($saldo_final, 0, ',', '.') . ' COP' ?>
                </span>
            </div>
        </div>

        <div class="order-details">
            <div class="order-row"><span>Orden #</span><span>#<?= $orden_id ?></span></div>
            <div class="order-row"><span>Producto</span><span style="font-size:0.82rem;"><?= htmlspecialchars($row['producto']) ?></span></div>
            <div class="order-row">
                <span>Estado</span>
                <span><span class="estado-badge"><?= strtoupper($nuevo_estado) ?></span></span>
            </div>
        </div>

        <a href="../home.php" class="btn-home">← Inicio</a>
        <a href="../views/historial/reg-pgb.php?modo=mixto" class="btn-volver">Ver historial</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
