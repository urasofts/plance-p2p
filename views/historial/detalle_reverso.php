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
</head>
<style>
    :root {
        --bg-base: #0d0e10; --bg-surface: #16181c;
        --bg-card: #1e2128; --border: #2e3038;
        --text-primary: #f0f1f3; --text-secondary: #8a8d96;
        --font-display: 'Barlow ', sans-serif;
        --font-body: 'Barlow', sans-serif;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        background-color: var(--bg-base);
        color: var(--text-primary);
        font-family: var(--font-body);
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }
    .navbar { background-color: #0f0f0fa9 !important; backdrop-filter: blur(8px); border-bottom: 1px solid var(--border); }

    .detalle-layout {
        max-width: 900px; margin: 2rem auto;
        padding: 0 1.2rem 3rem;
        display: grid; grid-template-columns: 1fr 300px;
        gap: 1.2rem; align-items: start;
    }

    .pcard {
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 14px; padding: 1.4rem 1.6rem;
    }
    .pcard-title {
        font-family: var(--font-display); font-size: 0.78rem;
        font-weight: 700; letter-spacing: 0.1em;
        text-transform: uppercase; color: var(--text-secondary);
        margin-bottom: 1rem; padding-bottom: 0.6rem;
        border-bottom: 1px solid var(--border);
    }
    .info-row {
        display: flex; justify-content: space-between;
        padding: 0.5rem 0; font-size: 0.875rem;
        border-bottom: 1px solid var(--border);
    }
    .info-row:last-child { border-bottom: none; }
    .info-row span:first-child { color: var(--text-secondary); }
    .info-row span:last-child  { font-weight: 600; color: var(--text-primary); text-align: right; max-width: 60%; }

    .estado-badge {
        display: inline-block; padding: 0.2rem 0.2rem;
        border-radius: 4px; font-size: 0.78rem; font-weight: 400;
        font-family: var(--font-display); letter-spacing: 0.05em;
        background: rgba(62,207,142,0.15); color: #3ecf8e;
        margin: 10px;
    }

    /* Botón opciones */
    .opciones-wrap { position: relative; }
    .btn-opciones {
        width: 100%; padding: 0.75rem 1rem;
        background: #e05252; color: #fff;
        border: none; border-radius: 8px;
        font-family: var(--font-display); font-size: 1rem;
        font-weight: 800; letter-spacing: 0.05em;
        text-transform: uppercase; cursor: pointer;
        display: flex; align-items: center;
        justify-content: space-between;
        transition: background 0.2s;
    }
    .btn-opciones:hover { background: #c93d3d; }

    .opciones-menu {
        display: none;
        position: absolute; top: calc(100% + 6px);
        left: 0; right: 0;
        background: var(--bg-surface);
        border: 1px solid var(--border);
        border-radius: 10px; overflow: hidden;
        z-index: 99;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        animation: dropFade 0.15s ease;
    }
    @keyframes dropFade {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .opciones-menu.show { display: block; }

    .opcion-item {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.75rem 1rem; font-size: 0.88rem;
        color: var(--text-primary); cursor: pointer;
        transition: background 0.15s; text-decoration: none;
        border-bottom: 1px solid var(--border);
    }
    .opcion-item:last-child { border-bottom: none; }
    .opcion-item:hover { background: rgba(255,255,255,0.05); text-decoration: none; }
    .opcion-item.danger { color: #e05252; }
    .opcion-item.danger:hover { background: rgba(224,82,82,0.1); }

    /* Info box reverso */
    .reverso-info {
        background: rgba(224,82,82,0.08);
        border: 1px solid rgba(224,82,82,0.2);
        border-radius: 8px; padding: 0.75rem 1rem;
        margin-top: 1rem; font-size: 0.82rem;
        color: #e05252; line-height: 1.5;
    }

    .alert-success-custom { background: rgba(62,207,142,0.12); color: #3ecf8e; border: 1px solid rgba(62,207,142,0.3); border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.88rem; }
    .alert-error-custom   { background: rgba(224,82,82,0.12);  color: #e05252; border: 1px solid rgba(224,82,82,0.3);  border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.88rem; }

    @media (max-width: 700px) {
        .detalle-layout { grid-template-columns: 1fr; }
    }
</style>
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
            <div class="pcard" style="margin-bottom:1.2rem;">
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

            <div class="pcard">
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

                <div class="opciones-wrap">
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

                <div class="reverso-info">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong> Importante:</strong> El reverso solo está disponible antes de la hora de corte del día. Una vez reversada la transacción, el dinero será devuelto automáticamente.
                </div>
            </div>
        </div>

    </div>

    <!-- Modal carta de reverso -->
    <div id="modalCarta" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:999; align-items:center; justify-content:center;">
        <div style="background:#16181c; border:1px solid #2e3038; border-radius:14px; padding:2rem; max-width:500px; width:90%; font-family:'Barlow',sans-serif;">
            <h3 style="font-family:'Barlow',sans-serif; font-size:1.3rem; font-weight:800; color:#f0f1f3; margin-bottom:1rem;">
                📄 Carta de Reverso
            </h3>
            <div style="background:#1e2128; border-radius:8px; padding:1.2rem; font-size:0.85rem; color:#8a8d96; line-height:1.7;">
                <p style="color:#f0f1f3; font-weight:600; margin-bottom:0.5rem;">Señores PlaceToPay / Evertec:</p>
                <p>Por medio de la presente, el usuario <strong style="color:#f0f1f3;"><?= htmlspecialchars($usuario) ?></strong> solicita formalmente el reverso de la transacción con los siguientes datos:</p>
                <br>
                <p>• <strong style="color:#f0f1f3;">ID Transacción:</strong> #<?= htmlspecialchars($trx['id']) ?></p>
                <p>• <strong style="color:#f0f1f3;">Producto:</strong> <?= htmlspecialchars($nombre) ?></p>
                <p>• <strong style="color:#f0f1f3;">Valor:</strong> $<?= number_format($trx['precio'], 0, ',', '.') ?> COP</p>
                <p>• <strong style="color:#f0f1f3;">Fecha:</strong> <?= htmlspecialchars($trx['created_at']) ?></p>
                <br>
                <p>Atentamente,<br><strong style="color:#f0f1f3;"><?= htmlspecialchars($_SESSION['usuario']) ?></strong></p>
            </div>
            <div style="display:flex; gap:0.8rem; margin-top:1.2rem;">
                <button onclick="window.print()" style="flex:1; padding:0.65rem; background:#f0b429; color:#0d0e10; border:none; border-radius:8px; font-family:'Barlow',sans-serif; font-weight:800; font-size:0.95rem; text-transform:uppercase; cursor:pointer;">
                    🖨️ Imprimir
                </button>
                <button onclick="document.getElementById('modalCarta').style.display='none'" style="flex:1; padding:0.65rem; background:transparent; color:#8a8d96; border:1px solid #2e3038; border-radius:8px; font-family:'Barlow',sans-serif; font-weight:700; font-size:0.95rem; text-transform:uppercase; cursor:pointer;">
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
            document.getElementById('modalCarta').style.display = 'flex';
            document.getElementById('opcionesMenu').classList.remove('show');
        }

        function confirmarReverso(id, tipo) {
            document.getElementById('opcionesMenu').classList.remove('show');
            if (confirm('⚠️ ¿Estás seguro de reversar esta transacción?\n\nEsta acción devolverá el dinero al cliente y no se puede deshacer.')) {
                window.location.href = '../../php/procesar_reverso.php?id=' + id + '&tipo=' + tipo;
            }
        }
    </script>
</body>
</html>