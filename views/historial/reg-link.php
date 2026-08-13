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

// Links de pago generados (API Link de pagos — PlacetoPay)
$resultado = mysqli_query($conexion, "SELECT * FROM payment_link ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial — Links de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    body {
        background-color: #000000;
        color: white;
        background-repeat: no-repeat;
        background-position: center;
        background-attachment: fixed;
        background-size: cover;
        font-family: 'Barlow', sans-serif;
    }
    .navbar {
        background-color: #0f0f0fa9 !important;
        backdrop-filter: blur(8px);
    }
    .tabla-container {
        background: rgba(15, 15, 15, 0.85);
        border-radius: 12px;
        padding: 1.5rem;
        margin: 2rem auto;
        max-width: 1200px;
        backdrop-filter: blur(8px);
    }
    .tabla-titulo {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: rgb(255, 153, 255);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .table {
        color: white;
        border-color: rgba(255,255,255,0.1);
    }
    .table thead th {
        background: rgba(0, 0, 0, 0.79);
        color: hsl(298, 94%, 68%);
        border-color: rgba(255,255,255,0.1);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .table tbody tr {
        border-color: rgba(255,255,255,0.07);
        background-color: black;
        transition: background 0.15s;
    }
    .table tbody tr:hover { background: rgba(255,255,255,0.05); }
    .table tbody td { border-color: rgba(255,255,255,0.07); font-size: 0.88rem; vertical-align: middle; background: #1312129a; color: white; }

    .badge-activo    { background: rgba(246, 59, 237, 0.2);  color: rgb(230, 166, 255); }
    .badge-error     { background: rgba(224,82,82,0.2);   color: #e05252; }
    .badge-expirado  { background: rgba(138,141,150,0.2); color: #8a8d96; }

    .estado-pill {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .sin-registros {
        text-align: center;
        padding: 3rem;
        color: #968a8a;
        font-size: 0.95rem;
    }
    .btn-link-action {
        background: rgba(59,130,246,0.15);
        border: 1px solid rgba(59,130,246,0.4);
        color: #ff85eb;
        border-radius: 6px;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s;
        white-space: nowrap;
        font-family: 'Barlow', sans-serif;
    }
    .btn-link-action:hover { background: rgba(59,130,246,0.3); color: #60a5fa; text-decoration: none; }
    .btn-link-action.copied { background: rgba(62,207,142,0.2); border-color: rgba(62,207,142,0.4); color: #3ecf8e; }
</style>
<body>
    <?php
        $nav_back_url  = "historial.php";
        $nav_back_text = "Atras";
        $nav_base      = "../../";
        require_once '../../php/navbar.php';
    ?>

    <div class="tabla-container">
        <div class="tabla-titulo"><i class="fa-solid fa-link" style="color: rgb(235, 84, 255);"></i>
             Historial de Links de Pago
        </div>

        <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Referencia</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th>Pagos</th>
                        <th>Expira</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado)):
                        $estado    = strtolower($row['estado']);
                        $expirado  = !empty($row['expiracion']) && strtotime($row['expiracion']) < time();
                        if ($estado === 'activo' && $expirado) $estado_show = 'expirado';
                        else $estado_show = $estado;
                    ?>
                    <tr>
                        <td><span style="color:#8a8d96;">#<?= htmlspecialchars($row['id']) ?></span></td>
                        <td><?= htmlspecialchars($row['producto']) ?></td>
                        <td style="color:#3b82f6; font-weight:700;">$<?= number_format((float)$row['precio'], 0, ',', '.') ?> COP</td>
                        <td><code style="color:#60a5fa;"><?= htmlspecialchars($row['referencia']) ?></code></td>
                        <td><code style="color:#93c5fd; font-size:0.8rem;"><?= htmlspecialchars($row['correo']) ?></code></td>
                        <td>
                            <span class="estado-pill badge-<?= $estado_show ?>">
                                <?= strtoupper($estado_show) ?>
                            </span>
                        </td>
                        <td style="text-align:center; color:#f0f1f3;"><?= (int)($row['pagos_usados'] ?? 0) ?></td>
                        <td style="color:#f0b429; font-size:0.8rem;"><?= !empty($row['expiracion']) ? htmlspecialchars($row['expiracion']) : '—' ?></td>
                        <td style="color:#8a8d96; font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (!empty($row['link_url']) && $estado === 'activo' && !$expirado): ?>
                            <div style="display:flex; gap:0.3rem;">
                                <a href="<?= htmlspecialchars($row['link_url']) ?>" target="_blank" class="btn-link-action">
                                    <i class="bi bi-box-arrow-up-right"></i> Abrir
                                </a>
                                <button type="button" class="btn-link-action btn-copy"
                                        data-link="<?= htmlspecialchars($row['link_url'], ENT_QUOTES) ?>"
                                        onclick="copyLink(this)">
                                    <i class="bi bi-clipboard"></i> Copiar
                                </button>
                            </div>
                            <?php else: ?>
                            <span style="color:#555860; font-size:0.75rem;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="sin-registros">
                <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                No hay links de pago generados aún.
            </div>
        <?php endif; ?>
    </div>

    <script>
    function copyLink(btn) {
        const link = btn.getAttribute('data-link');
        navigator.clipboard.writeText(link).then(function() {
            const original = btn.innerHTML;
            btn.classList.add('copied');
            btn.innerHTML = '<i class="bi bi-check2"></i> Copiado';
            setTimeout(function() {
                btn.classList.remove('copied');
                btn.innerHTML = original;
            }, 2000);
        });
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="paginacion.css">
    <style>:root{--pag-accent:#eb54ff;}</style>
    <script src="paginacion.js"></script>
</body>
</html>
