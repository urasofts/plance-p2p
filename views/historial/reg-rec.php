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

// Traer solo las recurrencias del usuario en sesión (por correo)
$correo_sesion = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');
$resultado     = mysqli_query($conexion, "SELECT * FROM recurrencias WHERE usuario_id = '$correo_sesion' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial — Membresías Recurrentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    body {
        /* background-image: url(../assets/images/bg26.jpg); */
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
        max-width: 1100px;
        backdrop-filter: blur(8px);
    }
    .tabla-titulo {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #4d9fff;
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
        color: #4d9fff;
        border-color: rgba(255,255,255,0.1);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .table tbody tr {
        border-color: rgba(255,255,255,0.07);
        transition: background 0.15s;
    }
    .table tbody tr:hover { background: rgba(255,255,255,0.05); }
    .table tbody td { border-color: rgba(255,255,255,0.07); font-size: 0.88rem; vertical-align: middle; background: #1312129a; color: white;   }

    .badge-aprobada  { background: rgba(62,207,142,0.2);  color: #3ecf8e; }
    .badge-pendiente { background: rgba(240,180,41,0.2);  color: #f0b429; }
    .badge-rechazada { background: rgba(224,82,82,0.2);   color: #e05252; }
    .badge-cancelada { background: rgba(138,141,150,0.2); color: #8a8d96; }

    .estado-pill {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .recurrente-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: rgba(77,159,255,0.12);
        color: #4d9fff;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 20px;
    }

    .sin-registros {
        text-align: center;
        padding: 3rem;
        color: #8a8d96;
        font-size: 0.95rem;
    }
    .btn-verificar {
        background: rgba(77,159,255,0.15);
        border: 1px solid rgba(77,159,255,0.4);
        color: #4d9fff;
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
    }
    .btn-verificar:hover { background: rgba(77,159,255,0.3); color: #4d9fff; text-decoration: none; }
    .alert-verify {
        background: rgba(62,207,142,0.12); color: #3ecf8e;
        border: 1px solid rgba(62,207,142,0.3);
        border-radius: 8px; padding: 0.75rem 1rem;
        margin-bottom: 1rem; font-size: 0.88rem;
    }
    .btn-cancelar {
        background: rgba(224,82,82,0.15);
        border: 1px solid rgba(224,82,82,0.4);
        color: #e05252;
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
        margin-top: 0.3rem;
    }
    .btn-cancelar:hover { background: rgba(224,82,82,0.3); color: #e05252; text-decoration: none; }
    .alert-cancel {
        background: rgba(224,82,82,0.12); color: #e05252;
        border: 1px solid rgba(224,82,82,0.3);
        border-radius: 8px; padding: 0.75rem 1rem;
        margin-bottom: 1rem; font-size: 0.88rem;
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
        <div class="tabla-titulo">
            <i class="bi bi-calendar-check-fill" style="color: #4d9fff;"></i>Historial de Membresías Recurrentes
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
                    <tr>
                        <th>#ID</th>
                        <th>Servicio</th>
                        <th>Plan</th>
                        <th>Correo</th>
                        <th>Precio / mes</th>
                        <th>Próximo cobro</th>
                        <th>Fin recurrencia</th>
                        <th>Periodicidad</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><span style="color:#8a8d96;">#<?= htmlspecialchars($row['id']) ?></span></td>
                        <td><?= htmlspecialchars($row['servicio']) ?></td>
                        <td><?= htmlspecialchars($row['plan']) ?></td>
                        <td><code style="color:#4d9fff;"><?= htmlspecialchars($row['usuario_id']) ?></code></td>
                        <td style="color:#4d9fff; font-weight:700;">
                            $<?= number_format($row['precio'], 0, ',', '.') ?> COP
                        </td>
                        <td style="color:#f0f1f3;">
                            <?= !empty($row['next_payment']) ? htmlspecialchars($row['next_payment']) : '—' ?>
                        </td>
                        <td style="color:#f0b429;">
                            <?= !empty($row['fecha_fin']) ? htmlspecialchars($row['fecha_fin']) : '—' ?>
                        </td>
                        <td>
                            <span class="recurrente-badge">
                                <i class="bi bi-arrow-repeat"></i>
                                <?= $row['periodicidad'] === 'M' ? 'Mensual' : htmlspecialchars($row['periodicidad']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="estado-pill badge-<?= strtolower($row['estado']) ?>">
                                <?= strtoupper($row['estado']) ?>
                            </span>
                        </td>
                        <td style="color:#8a8d96; font-size:0.8rem;">
                            <?= htmlspecialchars($row['created_at']) ?>
                        </td>
                        <td style="display:flex; flex-direction:column; gap:0.3rem;">
                            <?php if (strtolower($row['estado']) === 'pendiente' && !empty($row['request_id'])): ?>
                            <a href="../../php/verificar_pago.php?tabla=recurrencias&id=<?= $row['id'] ?>&request_id=<?= urlencode($row['request_id']) ?>&redirect=../views/historial/reg-rec.php"
                               class="btn-verificar">
                                <i class="bi bi-arrow-repeat"></i> Verificar
                            </a>
                            <?php elseif (strtolower($row['estado']) === 'aprobada'): ?>
                            <a href="../../php/cancelar_rec.php?id=<?= $row['id'] ?>"
                               class="btn-cancelar"
                               onclick="return confirm('⚠️ ¿Estás seguro de cancelar esta membresía? Esta acción no se puede deshacer.')">
                                <i class="bi bi-x-circle-fill"></i> Cancelar
                            </a>
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
                <i class="bi bi-arrow-repeat" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                No tienes membresías recurrentes registradas aún.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="paginacion.css">
    <style>:root{--pag-accent:#4d9fff;}</style>
    <script src="paginacion.js"></script>
</body>
</html>