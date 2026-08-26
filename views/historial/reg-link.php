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
    <link rel="stylesheet" href="../../assets/css/styles-historiales.css">
</head>
<style>
    /* Historial de Links de Pago — acento magenta */
    :root {
        --hist-accent:     #ff99ff;
        --hist-accent-rgb: 255, 153, 255;
        --hist-maxw:       1200px;
    }
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
