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

$disp_id = (int)($_GET['disp_id'] ?? 0);
if (!$disp_id) { header("Location: ../home.php"); exit(); }

// Traer datos desde BD
$row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM dispersiones WHERE id = $disp_id"));
if (!$row) { header("Location: ../home.php"); exit(); }

$destino   = $row['destino'];
$total     = (float)$row['precio_total'];
$base      = (float)$row['precio_base'];
$impuesto  = (float)$row['impuesto'];
$requestId = $row['request_id'];

// Consultar estado real en PlacetoPay
$nuevo_estado = 'pendiente';
$gw_status    = 'PENDING';

if ($requestId) {
    $login     = "8ddd7ab3d5a270608832d033849a1a8d";
    $secretKey = "U7rCf9me0vqk7755";
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

    if (!empty($data['payment'])) {
        $gw_status = $data['payment'][0]['status']['status'] ?? $gw_status;
    }

    $nuevo_estado = match($gw_status) {
        'APPROVED' => 'aprobada',
        'PENDING'  => 'pendiente',
        default    => 'rechazada'
    };

    $est_safe = mysqli_real_escape_string($conexion, $nuevo_estado);
    mysqli_query($conexion, "UPDATE dispersiones SET estado='$est_safe' WHERE id=$disp_id");
}

// Colores
if ($gw_status === 'APPROVED') {
    $icono = '✅'; $titulo = '¡Tiquete confirmado!';
    $mensaje = 'Tu pago fue procesado y dispersado exitosamente entre la aerolínea y los impuestos aeroportuarios.';
    $color = '#10b981'; $bg_icon = 'rgba(16,185,129,0.15)';
} elseif ($gw_status === 'PENDING') {
    $icono = '⏳'; $titulo = 'Pago pendiente';
    $mensaje = 'Tu pago está siendo procesado. Te notificaremos cuando se confirme.';
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
    <title>Resultado — Tiquete</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <?php require_once dirname(__DIR__) . '/php/theme.php'; ?>
    <style>
        :root{--bg:#0d0e10;--surface:#16181c;--card:#1e2128;--border:#2e3038;--text:#f0f1f3;--muted:#8a8d96;--font-d:'Barlow',sans-serif;--font-b:'Barlow',sans-serif;}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background:var(--);color:var(--text);font-family:var(--font-b);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}
        .result-card{background:var(--pt-boxitem);border:1px solid var(--pt-border);border-radius:16px;padding:2.5rem 2rem;max-width:500px;width:100%;text-align:center;animation:fadeUp 0.4s ease both;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        .result-icon{font-size:3rem;width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;background:<?= $bg_icon ?>;}
        .result-title{font-family:var(--font-d);font-size:2rem;font-weight:800;color:<?= $color ?>;margin-bottom:0.5rem;letter-spacing:0.02em;}
        .result-msg{font-size:0.9rem;color:var(--muted);margin-bottom:1.5rem;line-height:1.6;}

        .disp-badge{background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);border-radius:8px;padding:0.6rem 1rem;margin-bottom:1.2rem;font-size:0.8rem;color:#6ee7b7;display:flex;gap:0.5rem;align-items:center;justify-content:center;}

        /* Desglose dispersión */
        .disp-box{background:rgba(16,185,129,0.07);border:1px solid rgba(16,185,129,0.2);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;text-align:left;}
        .disp-title{font-family:var(--font-d);font-size:0.73rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin-bottom:0.6rem;}
        .disp-row{display:flex;justify-content:space-between;align-items:center;padding:0.35rem 0;font-size:0.85rem;border-bottom:1px solid rgba(255,255,255,0.05);}
        .disp-row:last-child{border-bottom:none;padding-top:0.4rem;margin-top:0.2rem;}
        .disp-row span:first-child{color:var(--muted);}
        .disp-row.total span{color:#10b981;font-weight:800;font-size:1rem;}

        .order-details{background:var(--pt-bg-card);border:1px solid var(--pt-border);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.2rem;text-align:left;}
        .order-row{display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;font-size:0.875rem;border-bottom:1px solid var(--pt-border);}
        .order-row:last-child{border-bottom:none;}
        .order-row span:first-child{color:var(--muted);}
        .order-row span:last-child{font-weight:600;}
        .estado-badge{display:inline-block;padding:0.2rem 0.6rem;border-radius:4px;font-size:0.78rem;font-weight:700;font-family:var(--font-d);letter-spacing:0.05em;background:<?= $bg_icon ?>;color:<?= $color ?>;}

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

        <div class="disp-badge">
            <i class="bi bi-diagram-3-fill"></i>
            Dispersión de pago · Web Checkout · PlacetoPay
        </div>

        <?php if ($gw_status === 'APPROVED'): ?>
        <!-- Desglose dispersión -->
        <div class="disp-box">
            <div class="disp-title">💸 Distribución del pago</div>
            <div class="disp-row">
                <span>✈️ Aerolínea (vuelo)</span>
                <span style="color:#10b981;font-weight:700;">$<?= number_format($base, 0, ',', '.') ?> COP</span>
            </div>
            <div class="disp-row">
                <span>🏛️ Impuestos aeroportuarios</span>
                <span style="color:#f0b429;font-weight:700;">$<?= number_format($impuesto, 0, ',', '.') ?> COP</span>
            </div>
            <div class="disp-row total">
                <span>Total dispersado</span>
                <span>$<?= number_format($total, 0, ',', '.') ?> COP</span>
            </div>
        </div>
        <?php endif; ?>

        <div class="order-details">
            <div class="order-row"><span>Tiquete #</span><span>#<?= $disp_id ?></span></div>
            <div class="order-row"><span>Destino</span><span>✈️ <?= htmlspecialchars($destino) ?></span></div>
            <div class="order-row"><span>Total</span><span style="color:<?= $color ?>;font-size:1.05rem;">$<?= number_format($total, 0, ',', '.') ?> COP</span></div>
            <div class="order-row"><span>Referencia</span><span style="font-size:0.78rem;color:var(--muted);"><?= htmlspecialchars($row['request_id']) ?></span></div>
            <div class="order-row">
                <span>Estado</span>
                <span><span class="estado-badge"><?= strtoupper($nuevo_estado) ?></span></span>
            </div>
        </div>

        <a href="../home.php" class="btn-home">← Inicio</a>
        <a href="../views/dispersiones/tickets.php" class="btn-volver">Ver tiquetes</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
