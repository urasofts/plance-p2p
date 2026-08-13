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

$correo  = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

// Traer ordenes aprobadas
$ordenes = mysqli_query($conexion, "SELECT id, producto as nombre, precio, jugador_id as usuario, created_at, estado, 'orden' as tipo FROM ordenes WHERE estado = 'aprobada' ORDER BY created_at DESC");

// Traer suscripciones aprobadas del usuario
$suscripciones = mysqli_query($conexion, "SELECT id, CONCAT(plataforma, ' — ', plan) as nombre, precio, usuario_id as usuario, created_at, estado, 'suscripcion' as tipo FROM suscripciones WHERE estado = 'aprobada' AND usuario_id = '$correo' ORDER BY created_at DESC");

// Traer recurrencias aprobadas del usuario
$recurrencias = mysqli_query($conexion, "SELECT id, CONCAT(servicio, ' — ', plan) as nombre, precio, usuario_id as usuario, created_at, estado, 'recurrencia' as tipo FROM recurrencias WHERE estado = 'aprobada' AND usuario_id = '$correo' ORDER BY created_at DESC");

// Unir en un array
$transacciones = [];
while ($row = mysqli_fetch_assoc($ordenes))      $transacciones[] = $row;
while ($row = mysqli_fetch_assoc($suscripciones)) $transacciones[] = $row;
while ($row = mysqli_fetch_assoc($recurrencias))  $transacciones[] = $row;

// Ordenar por fecha desc
usort($transacciones, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

// Mensaje
$msg      = $_SESSION['reverso_msg']      ?? '';
$msg_type = $_SESSION['reverso_msg_type'] ?? '';
unset($_SESSION['reverso_msg'], $_SESSION['reverso_msg_type']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reversos — Transacciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
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
    .navbar { background-color: #0f0f0fa9 !important; backdrop-filter: blur(8px); }
    .tabla-container {
        background: rgba(15,15,15,0.85);
        border-radius: 12px;
        padding: 1.5rem;
        margin: 2rem auto;
        max-width: 1100px;
        backdrop-filter: blur(8px);
    }
    .tabla-titulo {
        font-size: 1.3rem; font-weight: 700;
        margin-bottom: 1rem; color: #e05252;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .table { color: white; border-color: rgba(255,255,255,0.1); }
    .table thead th {
        background: rgba(224,82,82,0.15);
        color: #e05252;
        border-color: rgba(255,255,255,0.1);
        font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em;
    }
    .table tbody tr { border-color: rgba(255,255,255,0.07); transition: background 0.15s; }
    .table tbody tr:hover { background: rgba(255,255,255,0.05); }
    .table tbody td { border-color: rgba(255,255,255,0.07); font-size: 0.88rem; vertical-align: middle; background: #1312129a; color: white; }

    .tipo-badge {
        display: inline-block; padding: 0.15rem 0.55rem;
        border-radius: 20px; font-size: 0.72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .tipo-orden       { background: rgba(240,180,41,0.15); color: #f0b429; }
    .tipo-suscripcion { background: rgba(168,85,247,0.15); color: #a855f7; }
    .tipo-recurrencia { background: rgba(77,159,255,0.15); color: #4d9fff; }

    .badge-aprobada { background: rgba(62,207,142,0.2); color: #3ecf8e; }
    .estado-pill {
        display: inline-block; padding: 0.2rem 0.65rem;
        border-radius: 20px; font-size: 0.75rem;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .btn-detalle {
        background: rgba(224,82,82,0.15);
        border: 1px solid rgba(224,82,82,0.4);
        color: #e05252; border-radius: 6px;
        padding: 0.2rem 0.7rem; font-size: 0.75rem;
        font-weight: 700; text-decoration: none;
        display: inline-flex; align-items: center; gap: 0.3rem;
        transition: all 0.2s;
    }
    .btn-detalle:hover { background: rgba(224,82,82,0.3); color: #e05252; text-decoration: none; }

    .alert-success-custom { background: rgba(62,207,142,0.12); color: #3ecf8e; border: 1px solid rgba(62,207,142,0.3); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.88rem; }
    .alert-error-custom   { background: rgba(224,82,82,0.12);  color: #e05252; border: 1px solid rgba(224,82,82,0.3);  border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.88rem; }

    .sin-registros { text-align: center; padding: 3rem; color: #8a8d96; font-size: 0.95rem; }
</style>
<body>
    <?php
    $nav_back_url  = "historial.php";
    $nav_back_text = "volver";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>

    <div class="tabla-container">
        <div class="tabla-titulo">
            <i class="bi bi-arrow-counterclockwise"></i> Transacciones para Reverso
        </div>

        <?php if ($msg): ?>
        <div class="alert-<?= $msg_type === 'success' ? 'success' : 'error' ?>-custom">
            <?= htmlspecialchars($msg) ?>
        </div>
        <?php endif; ?>

        <?php if (count($transacciones) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Tipo</th>
                        <th>Producto / Servicio</th>
                        <th>Usuario</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacciones as $trx): ?>
                    <tr>
                        <td><span style="color:#8a8d96;">#<?= htmlspecialchars($trx['id']) ?></span></td>
                        <td>
                            <span class="tipo-badge tipo-<?= $trx['tipo'] ?>">
                                <?= $trx['tipo'] === 'orden' ? '<i class="bi bi-controller"></i> Juego' : ($trx['tipo'] === 'suscripcion' ? '<i class="bi bi-tv"></i> Suscripción' : '🔄 Recurrencia') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($trx['nombre']) ?></td>
                        <td><code style="color:#8a8d96;"><?= htmlspecialchars($trx['usuario']) ?></code></td>
                        <td style="color:#3ecf8e; font-weight:700;">$<?= number_format($trx['precio'], 0, ',', '.') ?> COP</td>
                        <td>
                            <span class="estado-pill badge-<?= strtolower($trx['estado']) ?>">
                                <?= strtoupper($trx['estado']) ?>
                            </span>
                        </td>
                        <td style="color:#8a8d96; font-size:0.8rem;"><?= htmlspecialchars($trx['created_at']) ?></td>
                        <td>
                            <a href="detalle_reverso.php?id=<?= $trx['id'] ?>&tipo=<?= $trx['tipo'] ?>" class="btn-detalle">
                                <i class="bi bi-eye-fill"></i> Ver detalle
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="sin-registros">
                <i class="bi bi-arrow-counterclockwise" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                No tienes transacciones aprobadas disponibles para reverso.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="paginacion.css">
    <style>:root{--pag-accent:#e05252;}</style>
    <script src="paginacion.js"></script>
</body>
</html>