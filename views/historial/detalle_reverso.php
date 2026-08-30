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

$id   = intval($_GET['id']   ?? 0);
$tipo = $_GET['tipo'] ?? '';

$tipos_permitidos = ['orden', 'suscripcion', 'recurrencia'];
if (!$id || !in_array($tipo, $tipos_permitidos)) {
    header("Location: reversos.php");
    exit();
}

$id_safe = mysqli_real_escape_string($conexion, $id);
$correo  = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

// Obtener la transacción según el tipo
if ($tipo === 'orden') {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT *, 'orden' as tipo FROM ordenes WHERE id = '$id_safe' AND estado = 'aprobada'"
    ));
    $nombre   = $trx['producto']   ?? '';
    $usuario  = $trx['jugador_id'] ?? '';
} elseif ($tipo === 'suscripcion') {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT *, 'suscripcion' as tipo FROM suscripciones WHERE id = '$id_safe' AND estado = 'aprobada' AND usuario_id = '$correo'"
    ));
    $nombre  = ($trx['plataforma'] ?? '') . ' — ' . ($trx['plan'] ?? '');
    $usuario = $trx['usuario_id'] ?? '';
} else {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT *, 'recurrencia' as tipo FROM recurrencias WHERE id = '$id_safe' AND estado = 'aprobada' AND usuario_id = '$correo'"
    ));
    $nombre  = ($trx['servicio'] ?? '') . ' — ' . ($trx['plan'] ?? '');
    $usuario = $trx['usuario_id'] ?? '';
}

if (!$trx) {
    header("Location: reversos.php");
    exit();
}

$msg      = $_SESSION['reverso_msg']      ?? '';
$msg_type = $_SESSION['reverso_msg_type'] ?? '';
unset($_SESSION['reverso_msg'], $_SESSION['reverso_msg_type']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle — Reverso #<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
    <link rel="stylesheet" href="../../assets/css/styles-historiales-detalle.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css">
    <link rel="stylesheet"
        href="../../assets/css/components/driver-theme.css?v=<?php echo filemtime(dirname(__DIR__, 2) . '/assets/css/components/driver-theme.css'); ?>">
</head>
<body>
    <?php
    $nav_back_url  = "reversos.php";
    $nav_back_text = "Volver";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>

    <?php if ($msg): ?>
    <div style="max-width:900px;margin:1rem auto;padding:0 1.2rem;">
        <div class="alert-<?= $msg_type === 'success' ? 'success' : 'error' ?>-custom">
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="detalle-layout">

        <!-- IZQUIERDA: Info transacción -->
        <div>
            <div class="pcard" id="detrev-info-trx" style="margin-bottom:1.2rem;">
                <div class="pcard-title">📋 Información de la transacción</div>

                <div class="info-row">
                    <span>ID Transacción</span>
                    <span>#<?= htmlspecialchars($trx['id']) ?></span>
                </div>
                <div class="info-row">
                    <span>Tipo</span>
                    <span><?= $tipo === 'orden' ? '🎮 Pago Básico' : ($tipo === 'suscripcion' ? '📺 Suscripción' : '<i class="bi bi-calendar-check-fill" style="color: #4d9fff;"></i>Recurrencia') ?></span>
                </div>
                <div class="info-row">
                    <span>Producto / Servicio</span>
                    <span><?= htmlspecialchars($nombre) ?></span>
                </div>
                <div class="info-row">
                    <span>Estado</span>
                    <span><span class="estado-badge">APROBADA</span></span>
                </div>
                <div class="info-row">
                    <span>Fecha</span>
                    <span><?= htmlspecialchars($trx['created_at']) ?></span>
                </div>
                <div class="info-row">
                    <span>Total pagado</span>
                    <span style="color:#3ecf8e; font-size:1.1rem;">$<?= number_format($trx['precio'], 0, ',', '.') ?> COP</span>
                </div>
            </div>

            <div class="pcard" id="detrev-info-pagador">
                <div class="pcard-title"><i class="bi bi-person-vcard-fill"></i> Información del pagador</div>
                <div class="info-row">
                    <span>Usuario / Correo</span>
                    <span><?= htmlspecialchars($usuario) ?></span>
                </div>
                <div class="info-row">
                    <span>Request ID</span>
                    <span style="font-size:0.75rem; color:#8a8d96;">
                        <?= !empty($trx['request_id']) ? htmlspecialchars(substr($trx['request_id'], 0, 20)) . '...' : 'N/A' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- DERECHA: Opciones -->
        <div>
            <div class="pcard">
                <div class="pcard-title">⚙️ Acciones</div>

                <div class="opciones-wrap" id="detrev-opciones">
                    <button class="btn-opciones" onclick="toggleOpciones()">
                        <span>Opciones</span>
                        <i class="bi bi-chevron-down" id="chevron"></i>
                    </button>

                    <div class="opciones-menu" id="opcionesMenu">

                        <!-- Imprimir comprobante -->
                        <a href="javascript:void(0)" onclick="imprimirComprobante()" class="opcion-item">
                            <i class="bi bi-printer-fill"></i> Imprimir comprobante
                        </a>

                        <!-- Carta de reverso -->
                        <a href="javascript:void(0)" onclick="cartaReverso()" class="opcion-item">
                            <i class="bi bi-file-text-fill"></i> Carta de reverso
                        </a>

                        <!-- Reversar transacción -->
                        <a href="javascript:void(0)"
                           onclick="confirmarReverso(<?= $id ?>, '<?= $tipo ?>')"
                           class="opcion-item danger">
                            <i class="bi bi-arrow-counterclockwise"></i> Reversar transacción
                        </a>

                    </div>
                </div>

                <div class="reverso-info" id="detrev-aviso">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong> Importante:</strong> El reverso solo está disponible antes de la hora de corte del día. Una vez reversada la transacción, el dinero será devuelto automáticamente.
                </div>
            </div>
        </div>

    </div>

    <!-- Modal carta de reverso -->
    <div id="modalCarta" class="modal-overlay">
        <div class="modal-box">
            <h3>📄 Carta de Reverso</h3>
            <div class="modal-carta-body">
                <p>Señores PlaceToPay / Evertec:</p>
                <p>Por medio de la presente, el usuario <strong><?= htmlspecialchars($usuario) ?></strong> solicita formalmente el reverso de la transacción con los siguientes datos:</p>
                <br>
                <p>• <strong>ID Transacción:</strong> #<?= htmlspecialchars($trx['id']) ?></p>
                <p>• <strong>Producto:</strong> <?= htmlspecialchars($nombre) ?></p>
                <p>• <strong>Valor:</strong> $<?= number_format($trx['precio'], 0, ',', '.') ?> COP</p>
                <p>• <strong>Fecha:</strong> <?= htmlspecialchars($trx['created_at']) ?></p>
                <br>
                <p>Atentamente,<br><strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong></p>
            </div>
            <div class="modal-actions">
                <button onclick="window.print()" class="btn-modal-print">
                    🖨️ Imprimir
                </button>
                <button onclick="document.getElementById('modalCarta').classList.remove('show')" class="btn-modal-close">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleOpciones() {
            const menu    = document.getElementById('opcionesMenu');
            const chevron = document.getElementById('chevron');
            menu.classList.toggle('show');
            chevron.className = menu.classList.contains('show')
                ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
        }

        // Cerrar menú si click fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.opciones-wrap')) {
                document.getElementById('opcionesMenu').classList.remove('show');
                document.getElementById('chevron').className = 'bi bi-chevron-down';
            }
        });

        function imprimirComprobante() {
            window.print();
        }

        function cartaReverso() {
            document.getElementById('modalCarta').classList.add('show');
            document.getElementById('opcionesMenu').classList.remove('show');
        }

        function confirmarReverso(id, tipo) {
            document.getElementById('opcionesMenu').classList.remove('show');
            if (confirm('⚠️ ¿Estás seguro de reversar esta transacción?\n\nEsta acción devolverá el dinero al cliente y no se puede deshacer.')) {
                window.location.href = '../../php/procesar_reverso.php?id=' + id + '&tipo=' + tipo;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
    <script src="../../assets/js/components/driver-tours/tour-detalle-reverso.js"></script>
</body>
</html>
