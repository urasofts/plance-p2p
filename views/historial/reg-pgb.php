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

// Traer solo las órdenes del jugador_id que coincide con el usuario en sesión
// En ordenes, jugador_id lo escribe el usuario manualmente
// Pero también podemos filtrar por user_id si lo tienes,
// por ahora filtramos todas (admin view) o por correo si aplica
// Como jugador_id es libre, mostramos todas las órdenes — puedes restringir después
// Determinar qué tabla mostrar
$modo = $_GET['modo'] ?? 'wc';

if ($modo === 'gateway') {
    $resultado = mysqli_query($conexion, "SELECT * FROM gateway_ordenes ORDER BY created_at DESC");
} elseif ($modo === 'mixto') {
    // Mixtos: registros de ordenes que tienen monto_pagado (pago parcial) o productos múltiples
    $resultado = mysqli_query($conexion, "SELECT * FROM ordenes WHERE monto_pagado IS NOT NULL OR producto LIKE '%+%' ORDER BY created_at DESC");
} else {
    $resultado = mysqli_query($conexion, "SELECT * FROM ordenes WHERE monto_pagado IS NULL AND producto NOT LIKE '%+%' ORDER BY created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial — Pagos Básicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800&display=swap" rel="stylesheet">
    <?php $theme_seccion = 'registros'; require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/styles-historiales.css">
</head>
<style>
    /* Historial de Pagos Básicos — acento dorado */
    :root {
        --hist-accent:     #f0b429;
        --hist-accent-rgb: 240, 180, 41;
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
        <div class="tabla-titulo"><i class="fa-solid fa-money-bill-1-wave fs-3l" style="color: #f0b429;"></i>
             Historial de Pagos Básicos
        </div>

        <!-- TABS -->
        <div class="modo-tabs">
            <a href="reg-pgb.php?modo=wc" class="modo-tab <?= $modo === 'wc' ? 'active-wc' : '' ?>">
                <i class="bi bi-display"></i> Web Checkout
            </a>
            <a href="reg-pgb.php?modo=gateway" class="modo-tab <?= $modo === 'gateway' ? 'active-gw' : '' ?>">
                <i class="bi bi-code-slash"></i> API Gateway
            </a>
            <a href="reg-pgb.php?modo=mixto" class="modo-tab <?= $modo === 'mixto' ? 'active-mixto' : '' ?>">
                <i class="bi bi-shuffle"></i> Pago Mixto
            </a>
        </div>

        <?php
        if (!empty($_SESSION['verify_msg'])) {
            echo '<div class="alert-verify">' . htmlspecialchars($_SESSION['verify_msg']) . '</div>';
            unset($_SESSION['verify_msg']);
        }
        ?>

        <?php if (mysqli_num_rows($resultado) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <?php if ($modo === 'gateway'): ?>
                    <tr>
                        <th>#ID</th>
                        <th>Producto</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                    <?php elseif ($modo === 'mixto'): ?>
                    <tr>
                        <th>#ID</th>
                        <th>Productos</th>
                        <th>ID Jugador</th>
                        <th>Total pedido</th>
                        <th>Monto pagado</th>
                        <th>Saldo restante</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <th>#ID</th>
                        <th>Producto</th>
                        <th>ID Jugador</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <?php if ($modo === 'gateway'): ?>
                        <td><span style="color:#8a8d96;">#<?= htmlspecialchars($row['id']) ?></span></td>
                        <td><?= htmlspecialchars($row['producto']) ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><code style="color:#f59e0b;"><?= htmlspecialchars($row['correo']) ?></code></td>
                        <td style="color:#f0b429; font-weight:700;">$<?= number_format($row['precio'], 0, ',', '.') ?> COP</td>
                        <td>
                            <span class="estado-pill badge-<?= strtolower($row['estado']) ?>">
                                <?= strtoupper($row['estado']) ?>
                            </span>
                        </td>
                        <td style="color:#8a8d96; font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=gateway_ordenes&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-pgb.php?modo=gateway"
                               class="btn-verificar">
                                <i class="bi bi-arrow-repeat"></i> Verificar
                            </a>
                            <?php else: ?>
                            <span style="color:#555860; font-size:0.75rem;">—</span>
                            <?php endif; ?>
                        </td>

                        <?php elseif ($modo === 'mixto'): ?>
                        <?php
                            $total         = (float)$row['precio'];
                            $monto_pagado  = isset($row['monto_pagado']) && $row['monto_pagado'] !== null ? (float)$row['monto_pagado'] : $total;
                            $saldo_rest    = $total - $monto_pagado;
                            $es_parcial    = $row['monto_pagado'] !== null && $monto_pagado < $total;
                        ?>
                        <td><span style="color:#8a8d96;">#<?= htmlspecialchars($row['id']) ?></span></td>
                        <td style="font-size:0.82rem;max-width:200px;"><?= htmlspecialchars($row['producto']) ?></td>
                        <td><code style="color:#a855f7;"><?= htmlspecialchars($row['jugador_id']) ?></code></td>
                        <td style="color:#f0f1f3;font-weight:700;">$<?= number_format($total, 0, ',', '.') ?> COP</td>
                        <td style="color:#3ecf8e;font-weight:700;">$<?= number_format($monto_pagado, 0, ',', '.') ?> COP</td>
                        <td>
                            <?php if ($es_parcial): ?>
                            <span style="color:#f0b429;font-weight:700;">$<?= number_format($saldo_rest, 0, ',', '.') ?> COP</span>
                            <?php else: ?>
                            <span style="color:#555860;font-size:0.75rem;">— Completo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="estado-pill badge-<?= strtolower($row['estado']) ?>">
                                <?= strtoupper($row['estado']) ?>
                            </span>
                        </td>
                        <td style="color:#8a8d96;font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=ordenes&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-pgb.php?modo=mixto"
                               class="btn-verificar">
                                <i class="bi bi-arrow-repeat"></i>Verificar
                            </a>
                            <?php elseif (strtolower($row['estado']) === 'aprobada' && $es_parcial && $saldo_rest > 0): ?>
                            <a href="../../php/continuar_pago.php?id=<?= $row['id'] ?>" class="btn-continuar">
                                <i class="bi bi-play-circle-fill"></i> Continuar pago
                            </a>
                            <?php else: ?>
                            <span style="color:#555860;font-size:0.75rem;">—</span>
                            <?php endif; ?>
                        </td>

                        <?php else: ?>
                        <td><span style="color:#8a8d96;">#<?= htmlspecialchars($row['id']) ?></span></td>
                        <td><?= htmlspecialchars($row['producto']) ?></td>
                        <td><code style="color:#a855f7;"><?= htmlspecialchars($row['jugador_id']) ?></code></td>
                        <td style="color:#f0b429; font-weight:700;">$<?= number_format($row['precio'], 0, ',', '.') ?> COP</td>
                        <td>
                            <span class="estado-pill badge-<?= strtolower($row['estado']) ?>">
                                <?= strtoupper($row['estado']) ?>
                            </span>
                        </td>
                        <td style="color:#8a8d96; font-size:0.8rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=ordenes&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-pgb.php"
                               class="btn-verificar">
                                <i class="bi bi-arrow-repeat"></i> Verificar
                            </a>
                            <?php else: ?>
                            <span style="color:#555860; font-size:0.75rem;">—</span>
                            <?php endif; ?>
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
                No hay órdenes registradas aún.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="paginacion.css">
    <style>:root{--pag-accent:#f0b429;}</style>
    <script src="paginacion.js"></script>
</body>
</html>
