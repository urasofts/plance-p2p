<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../php/conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$correo_sesion = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');
$resultado = mysqli_query($conexion, "SELECT * FROM dispersiones WHERE usuario_id = '$correo_sesion' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial — Dispersiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
    
</head>
<style>
    body { background-color: #000000; color: white; font-family: 'Barlow', sans-serif; }
    .navbar { background-color: #0f0f0fa9 !important; backdrop-filter: blur(8px); }

    .tabla-container {
        background: rgba(15,15,15,0.85);
        border-radius: 12px; padding: 1.5rem;
        margin: 2rem auto; max-width: 1150px;
        backdrop-filter: blur(8px);
    }
    .tabla-titulo {
        font-size: 1.3rem; font-weight: 700;
        margin-bottom: 1rem; color: #10b981;
        display: flex; align-items: center; gap: 0.5rem;
    }

    .table { color: white; border-color: rgba(255,255,255,0.1); }
    .table thead th {
        background: rgba(0,0,0,0.79); color: #6ee7b7;
        border-color: rgba(255,255,255,0.1);
        font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em;
    }
    .table tbody tr { border-color: rgba(255,255,255,0.07); transition: background 0.15s; }
    .table tbody tr:hover { background: rgba(255,255,255,0.05); }
    .table tbody td { border-color: rgba(255,255,255,0.07); font-size: 0.88rem; vertical-align: middle; background: #1312129a; color: white; }

    .badge-aprobada  { background: rgba(62,207,142,0.2);  color: #3ecf8e; }
    .badge-pendiente { background: rgba(240,180,41,0.2);  color: #f0b429; }
    .badge-rechazada { background: rgba(224,82,82,0.2);   color: #e05252; }

    .estado-pill {
        display: inline-block; padding: 0.2rem 0.65rem;
        border-radius: 20px; font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .disp-pill {
        display: inline-flex; align-items: center; gap: 0.3rem;
        background: rgba(16,185,129,0.15); color: #6ee7b7;
        border: 1px solid rgba(16,185,129,0.3);
        padding: 0.15rem 0.5rem; border-radius: 20px;
        font-size: 0.72rem; font-weight: 700; letter-spacing: 0.04em;
    }

    /* Desglose inline */
    .desglose-cell { font-size: 0.78rem; line-height: 1.7; }
    .desglose-vuelo { color: #10b981; font-weight: 600; }
    .desglose-imp   { color: #f0b429; font-weight: 600; }

    .sin-registros {
        text-align: center; padding: 3rem;
        color: #8a8d96; font-size: 0.95rem;
    }
    .btn-verificar {
        background: rgba(16,185,129,0.15);
        border: 1px solid rgba(16,185,129,0.4);
        color: #6ee7b7; border-radius: 6px;
        padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 700;
        cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 0.3rem;
        transition: all 0.2s; white-space: nowrap;
    }
    .btn-verificar:hover { background: rgba(16,185,129,0.3); color: #6ee7b7; text-decoration: none; }
    .alert-verify {
        background: rgba(62,207,142,0.12); color: #3ecf8e;
        border: 1px solid rgba(62,207,142,0.3);
        border-radius: 8px; padding: 0.75rem 1rem;
        margin-bottom: 1rem; font-size: 0.88rem;
    }

    .info-banner {
        background: rgba(16,185,129,0.07);
        border: 1px solid rgba(16,185,129,0.2);
        border-left: 3px solid #10b981;
        border-radius: 0 8px 8px 0;
        padding: 0.75rem 1rem; margin-bottom: 1.2rem;
        font-size: 0.82rem; color: #6ee7b7; line-height: 1.6;
    }
</style>
<body>
    <?php
    $nav_back_url  = "historial.php";
    $nav_back_text = "Atrás";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>

    <div class="tabla-container">
        <div class="tabla-titulo">
            <i class="bi bi-diagram-3-fill" style="color:#10b981;"></i>
            Historial de Tiquetes — Dispersión de Pago
        </div>

        <div class="info-banner">
            <i class="bi bi-info-circle-fill"></i>
            En los pagos con <strong>dispersión</strong>, el monto total se divide automáticamente entre la aerolínea y los impuestos aeroportuarios. Aquí puedes ver el desglose de cada tiquete.
        </div>

        <?php if (!empty($_SESSION['verify_msg'])): ?>
        <div class="alert-verify"><?= htmlspecialchars($_SESSION['verify_msg']) ?></div>
        <?php unset($_SESSION['verify_msg']); endif; ?>

        <?php if (mysqli_num_rows($resultado) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Destino</th>
                        <th>Tipo</th>
                        <th>Vuelo (aerolínea)</th>
                        <th>Impuestos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><span style="color:#8a8d96;">#<?= htmlspecialchars($row['id']) ?></span></td>
                        <td style="font-weight:600;">✈️ <?= htmlspecialchars($row['destino']) ?></td>
                        <td><span class="disp-pill"><i class="bi bi-diagram-3-fill"></i> Dispersión</span></td>
                        <td class="desglose-cell">
                            <span class="desglose-vuelo">$<?= number_format($row['precio_base'], 0, ',', '.') ?> COP</span>
                        </td>
                        <td class="desglose-cell">
                            <span class="desglose-imp">$<?= number_format($row['impuesto'], 0, ',', '.') ?> COP</span>
                        </td>
                        <td style="color:#10b981;font-weight:700;font-size:0.95rem;">
                            $<?= number_format($row['precio_total'], 0, ',', '.') ?> COP
                        </td>
                        <td>
                            <span class="estado-pill badge-<?= strtolower($row['estado']) ?>">
                                <?= strtoupper($row['estado']) ?>
                            </span>
                        </td>
                        <td style="color:#8a8d96;font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=dispersiones&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-disp.php"
                               class="btn-verificar">
                                <i class="bi bi-arrow-repeat"></i> Verificar
                            </a>
                            <?php else: ?>
                            <span style="color:#555860;font-size:0.75rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="sin-registros">
                <i class="bi bi-airplane" style="font-size:2rem;display:block;margin-bottom:0.5rem;"></i>
                No tienes tiquetes registrados aún.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="paginacion.css">
    <style>:root{--pag-accent:#10b981;}</style>
    <script src="paginacion.js"></script>
</body>
</html>