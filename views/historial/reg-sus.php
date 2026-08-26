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

// Traer solo las suscripciones del usuario en sesión (por correo)
$correo_sesion = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

// Determinar modo
$modo = $_GET['modo'] ?? 'wc-sub';

switch ($modo) {
    case 'wc-rec':
        $resultado = mysqli_query($conexion, "SELECT *, 'wc-rec' as modo FROM suscription_rec WHERE usuario_id = '$correo_sesion' ORDER BY created_at DESC");
        break;
    case 'wc-pura':
        $resultado = mysqli_query($conexion, "SELECT *, 'wc-pura' as modo FROM suscription WHERE usuario_id = '$correo_sesion' ORDER BY created_at DESC");
        break;
    case 'gw-sub':
        $resultado = mysqli_query($conexion, "SELECT *, 'gw-sub' as modo FROM gateway_suscripciones WHERE correo = '$correo_sesion' ORDER BY created_at DESC");
        break;
    case 'gw-pura':
        $resultado = mysqli_query($conexion, "SELECT *, 'gw-pura' as modo FROM gateway_suscription WHERE correo = '$correo_sesion' ORDER BY created_at DESC");
        break;
    default: // wc-sub
        $resultado = mysqli_query($conexion, "SELECT *, 'wc-sub' as modo FROM suscripciones WHERE usuario_id = '$correo_sesion' ORDER BY created_at DESC");
        break;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial — Suscripciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php $theme_seccion = 'historial'; require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
    <link rel="stylesheet" href="../../assets/css/styles-historiales.css">

</head>
<style>
    /* Historial de Suscripciones — acento morado */
    :root {
        --hist-accent:     #a855f7;
        --hist-accent-rgb: 168, 85, 247;
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
        <div class="tabla-titulo"><i class="fa-solid fa-credit-card" style="color: rgb(153, 0, 255);"></i>
         Historial de Suscripciones
        </div>

        <!-- TABS Web Checkout -->
        <div class="modo-tabs-group">
            <div class="modo-tabs-label"><i class="bi bi-display"></i> Web Checkout</div>
            <div class="modo-tabs">
                <a href="reg-sus.php?modo=wc-sub"  class="modo-tab <?= $modo === 'wc-sub'  ? 'active-purple' : '' ?>"><i class="bi bi-tv"></i> Pago + Suscripción</a>
                <a href="reg-sus.php?modo=wc-rec"  class="modo-tab <?= $modo === 'wc-rec'  ? 'active-blue'   : '' ?>"><i class="fa-solid fa-credit-card"></i> Recurrentes</a>
                <a href="reg-sus.php?modo=wc-pura" class="modo-tab <?= $modo === 'wc-pura' ? 'active-green'  : '' ?>"><i class="bi bi-key"></i>  Suscripción pura</a>
            </div>
            <div class="tabs-divider"></div>
            <div class="modo-tabs-label">⚡ API Gateway</div>
            <div class="modo-tabs">
                <a href="reg-sus.php?modo=gw-sub"  class="modo-tab <?= $modo === 'gw-sub'  ? 'active-orange' : '' ?>"><i class="bi bi-tv"></i> Pago + Suscripción</a>
                <a href="reg-sus.php?modo=gw-pura" class="modo-tab <?= $modo === 'gw-pura' ? 'active-orange' : '' ?>"><i class="bi bi-key"></i>  Suscripción pura</a>
            </div>
        </div>

        <?php
        if (!empty($_SESSION['verify_msg'])) {
            echo '<div class="alert-verify">' . htmlspecialchars($_SESSION['verify_msg']) . '</div>';
            unset($_SESSION['verify_msg']);
        }
        if (!empty($_SESSION['cancel_msg'])) {
            echo '<div class="alert-cancel">' . htmlspecialchars($_SESSION['cancel_msg']) . '</div>';
            unset($_SESSION['cancel_msg']);
        }
        ?>

        <?php if (mysqli_num_rows($resultado) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <?php if (in_array($modo, ['wc-sub'])): ?>
                    <tr><th>#ID</th><th>Plataforma</th><th>Plan</th><th>Correo</th><th>Precio</th><th>Token</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr>
                    <?php elseif ($modo === 'wc-rec'): ?>
                    <tr><th>#ID</th><th>Servicio</th><th>Plan</th><th>Correo</th><th>Precio/mes</th><th>Periodicidad</th><th>Próx. cobro</th><th>Fin</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr>
                    <?php elseif ($modo === 'wc-pura'): ?>
                    <tr><th>#ID</th><th>Servicio</th><th>Plan</th><th>Correo</th><th>Precio</th><th>Token</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr>
                    <?php elseif ($modo === 'gw-sub'): ?>
                    <tr><th>#ID</th><th>Servicio</th><th>Plan</th><th>Nombre</th><th>Correo</th><th>Precio</th><th>Token</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr>
                    <?php elseif ($modo === 'gw-pura'): ?>
                    <tr><th>#ID</th><th>Servicio</th><th>Plan</th><th>Nombre</th><th>Correo</th><th>Precio</th><th>Token</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><span style="color:#8a8d96;">#<?= htmlspecialchars($row['id']) ?></span></td>

                        <?php if ($modo === 'wc-sub'): ?>
                        <td><?= htmlspecialchars($row['plataforma']) ?></td>
                        <td><?= htmlspecialchars($row['plan']) ?></td>
                        <td><code style="color:#a855f7;"><?= htmlspecialchars($row['usuario_id']) ?></code></td>
                        <td style="color:#a855f7;font-weight:700;">$<?= number_format($row['precio'],0,',','.') ?> COP</td>
                        <td style="color:<?= !empty($row['token']) ? '#3ecf8e' : '#555860' ?>; font-size:0.78rem;"><?= !empty($row['token']) ? '<i class="bi bi-key-fill fs-5"></i> Guardado' : '—' ?></td>
                        <td><span class="estado-pill badge-<?= strtolower($row['estado']) ?>"><?= strtoupper($row['estado']) ?></span></td>
                        <td style="color:#8a8d96;font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=suscripciones&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-sus.php?modo=wc-sub" class="btn-verificar"><i class="bi bi-arrow-repeat"></i> Verificar</a>
                            <?php else: ?><span style="color:#555860;font-size:0.75rem;">—</span><?php endif; ?>
                        </td>

                        <?php elseif ($modo === 'wc-rec'): ?>
                        <td><?= htmlspecialchars($row['servicio']) ?></td>
                        <td><?= htmlspecialchars($row['plan']) ?></td>
                        <td><code style="color:#4d9fff;"><?= htmlspecialchars($row['usuario_id']) ?></code></td>
                        <td style="color:#4d9fff;font-weight:700;">$<?= number_format($row['precio'],0,',','.') ?> COP</td>
                        <td><span style="background:rgba(77,159,255,0.12);color:#4d9fff;font-size:0.72rem;padding:0.1rem 0.4rem;border-radius:3px;"><?= $row['periodicidad'] === 'Y' ? 'Anual' : 'Mensual' ?></span></td>
                        <td style="color:#f0f1f3;"><?= !empty($row['next_payment']) ? htmlspecialchars($row['next_payment']) : '—' ?></td>
                        <td style="color:#f0b429;"><?= !empty($row['fecha_fin']) ? htmlspecialchars($row['fecha_fin']) : '—' ?></td>
                        <td><span class="estado-pill badge-<?= strtolower($row['estado']) ?>"><?= strtoupper($row['estado']) ?></span></td>
                        <td style="color:#8a8d96;font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td style="display:flex; flex-direction:column; gap:0.3rem;">
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=suscription_rec&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-sus.php?modo=wc-rec" class="btn-verificar"><i class="bi bi-arrow-repeat"></i> Verificar</a>
                            <?php elseif (strtolower($row['estado']) === 'aprobada'): ?>
                            <a href="../../php/cancelar_rec.php?id=<?= $row['id'] ?>&tabla=suscription_rec&modo=wc-rec"
                               class="btn-cancelar"
                               onclick="return confirm('⚠️ ¿Estás seguro de cancelar esta suscripción de IA? Esta acción no se puede deshacer.')">
                                <i class="bi bi-x-circle-fill"></i> Cancelar
                            </a>
                            <?php else: ?>
                            <span style="color:#555860;font-size:0.75rem;">—</span>
                            <?php endif; ?>
                        </td>

                        <?php elseif ($modo === 'wc-pura'): ?>
                        <td><?= htmlspecialchars($row['servicio']) ?></td>
                        <td><?= htmlspecialchars($row['plan']) ?></td>
                        <td><code style="color:#3ecf8e;"><?= htmlspecialchars($row['usuario_id']) ?></code></td>
                        <td style="color:#3ecf8e;font-weight:700;">$<?= number_format($row['precio'],0,',','.') ?> COP</td>
                        <td style="color:<?= !empty($row['token']) ? '#3ecf8e' : '#555860' ?>;font-size:0.78rem;"><?= !empty($row['token']) ? '<i class="bi bi-key-fill fs-5"></i> Guardado' : '—' ?></td>
                        <td><span class="estado-pill badge-<?= strtolower($row['estado']) ?>"><?= strtoupper($row['estado']) ?></span></td>
                        <td style="color:#8a8d96;font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=suscription&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-sus.php?modo=wc-pura" class="btn-verificar"><i class="bi bi-arrow-repeat"></i> Verificar</a>
                            <?php else: ?><span style="color:#555860;font-size:0.75rem;">—</span><?php endif; ?>
                        </td>

                        <?php elseif ($modo === 'gw-sub'): ?>
                        <td><?= htmlspecialchars($row['servicio']) ?></td>
                        <td><?= htmlspecialchars($row['plan']) ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><code style="color:#f59e0b;"><?= htmlspecialchars($row['correo']) ?></code></td>
                        <td style="color:#f59e0b;font-weight:700;">$<?= number_format($row['precio'],0,',','.') ?> COP</td>
                        <td style="color:<?= !empty($row['token']) ? '#3ecf8e' : '#555860' ?>;font-size:0.78rem;"><?= !empty($row['token']) ? '<i class="bi bi-key-fill fs-5"></i> Guardado' : '—' ?></td>
                        <td><span class="estado-pill badge-<?= strtolower($row['estado']) ?>"><?= strtoupper($row['estado']) ?></span></td>
                        <td style="color:#8a8d96;font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=gateway_suscripciones&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-sus.php?modo=gw-sub" class="btn-verificar"><i class="bi bi-arrow-repeat"></i> Verificar</a>
                            <?php else: ?><span style="color:#555860;font-size:0.75rem;">—</span><?php endif; ?>
                        </td>

                        <?php elseif ($modo === 'gw-pura'): ?>
                        <td><?= htmlspecialchars($row['servicio']) ?></td>
                        <td><?= htmlspecialchars($row['plan']) ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><code style="color:#f59e0b;"><?= htmlspecialchars($row['correo']) ?></code></td>
                        <td style="color:#f59e0b;font-weight:700;">$<?= number_format($row['precio'],0,',','.') ?> COP</td>
                        <td style="color:<?= !empty($row['token']) ? '#3ecf8e' : '#555860' ?>;font-size:0.78rem;"><?= !empty($row['token']) ? '<i class="bi bi-key-fill fs-5"></i> Guardado' : '—' ?></td>
                        <td><span class="estado-pill badge-<?= strtolower($row['estado']) ?>"><?= strtoupper($row['estado']) ?></span></td>
                        <td style="color:#8a8d96;font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=gateway_suscription&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-sus.php?modo=gw-pura" class="btn-verificar"><i class="bi bi-arrow-repeat"></i> Verificar</a>
                            <?php else: ?><span style="color:#555860;font-size:0.75rem;">—</span><?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="sin-registros">
                <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                No tienes registros en esta categoría aún.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="paginacion.css">
    <style>:root{--pag-accent:#a855f7;}</style>
    <script src="paginacion.js"></script>
</body>
</html>
