<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

// Recibir datos del formulario de pubg/bloodstrike y guardar en sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['gw_pending'] = $_POST;
}

// Si no hay datos pendientes redirigir
if (empty($_SESSION['gw_pending'])) {
    header("Location: ../home.php");
    exit();
}

$producto = $_SESSION['gw_pending']['producto'] ?? 'Producto';
$precio   = $_SESSION['gw_pending']['precio']   ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de transacción | Plance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <?php require_once dirname(__DIR__) . '/php/theme.php'; ?>
    <style>
        :root {
            --bg-base:    var(--pt-bg-base);
            --bg-surface: var(--pt-bg-surface);
            --bg-card:    var(--pt-bg-card);
            --border:     var(--pt-border);
            --accent:     #f0b429;
            --accent-soft: rgba(240,180,41,0.1);
            --text-primary:   var(--pt-text);
            --text-secondary: var(--pt-text-sec);   
            --font-display: 'Barlow', sans-serif;
            --font-body:    'Barlow', sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 2rem;
        }

        .main-card {
            background: var(--pt-navbar);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 2.5rem 2rem;
            max-width: 520px; width: 100%;
            animation: fadeUp 0.4s ease both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* Header */
        .demo-banner {
            background: rgba(240,180,41,0.08);
            border: 1px solid rgba(240,180,41,0.25);
            border-radius: 10px; padding: 0.8rem 1rem;
            text-align: center; margin-bottom: 1.8rem;
            font-size: 0.88rem; color: var(--accent); font-weight: 600;
        }
        .demo-banner strong { display: block; font-size: 1rem; margin-bottom: 0.2rem; }

        .card-title-main {
            font-family: var(--font-display);
            font-size: 1.6rem; font-weight: 800;
            color: var(--text-primary); text-align: center;
            margin-bottom: 0.4rem; letter-spacing: 0.02em;
        }
        .card-subtitle {
            text-align: center; color: var(--text-secondary);
            font-size: 0.88rem; margin-bottom: 2rem; line-height: 1.5;
        }

        /* Producto info */
        .product-info {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; padding: 0.9rem 1.2rem;
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 1.8rem;
        }
        .product-info-name { font-weight: 600; font-size: 0.9rem; }
        .product-info-price { font-family: var(--font-display); font-size: 1.2rem; font-weight: 800; color: var(--accent); }

        /* Estado selector */
        .estado-label {
            font-family: var(--font-display); font-size: 0.75rem;
            font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--text-secondary); margin-bottom: 0.6rem; display: block;
        }

        .estados-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 0.7rem; margin-bottom: 1.5rem; }

        .estado-btn {
            border: 1.5px solid var(--border);
            background: var(--bg-card); border-radius: 12px;
            padding: 1rem 0.5rem; cursor: pointer;
            text-align: center; transition: all 0.2s; position: relative;
        }
        .estado-btn:hover { transform: translateY(-2px); }
        .estado-btn.selected { box-shadow: 0 0 0 2px currentColor; }

        .estado-btn.aprobada  { --c: #3ecf8e; }
        .estado-btn.pendiente { --c: #f0b429; }
        .estado-btn.rechazada { --c: #e05252; }

        .estado-btn.aprobada:hover,  .estado-btn.aprobada.selected  { border-color: #3ecf8e; background: rgba(62,207,142,0.08); }
        .estado-btn.pendiente:hover, .estado-btn.pendiente.selected { border-color: #f0b429; background: rgba(240,180,41,0.08); }
        .estado-btn.rechazada:hover, .estado-btn.rechazada.selected { border-color: #e05252; background: rgba(224,82,82,0.08); }

        .estado-btn .check {
            display: none; position: absolute; top: 0.4rem; right: 0.5rem;
            font-size: 0.7rem; font-weight: 900;
        }
        .estado-btn.selected .check { display: block; color: var(--c); }

        .estado-icon { font-size: 1.6rem; margin-bottom: 0.4rem; }
        .estado-name {
            font-family: var(--font-display); font-size: 0.95rem;
            font-weight: 800; letter-spacing: 0.04em;
        }
        .estado-btn.aprobada  .estado-name { color: #3ecf8e; }
        .estado-btn.pendiente .estado-name { color: #f0b429; }
        .estado-btn.rechazada .estado-name { color: #e05252; }

        /* Razón selector */
        .razon-wrap { margin-bottom: 1.5rem; }
        .razon-select {
            width: 100%; background: var(--bg-card);
            border: 1.5px solid var(--border); border-radius: 10px;
            color: var(--text-primary); font-family: var(--font-body);
            font-size: 0.88rem; padding: 0.6rem 0.9rem; outline: none;
            transition: border-color 0.2s; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%238a8d96'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 0.8rem center;
            padding-right: 2rem;
        }
        .razon-select:focus { border-color: var(--accent); }
        .razon-select option { background: #1e2128; }

        /* Botón procesar */
        .btn-procesar {
            width: 100%; padding: 0.9rem;
            background: var(--accent); border: none; border-radius: 10px;
            color: #0d0e10; font-family: var(--font-display);
            font-size: 1.1rem; font-weight: 800; letter-spacing: 0.06em;
            text-transform: uppercase; cursor: pointer;
            transition: all 0.2s; display: flex;
            align-items: center; justify-content: center; gap: 0.5rem;
        }
        .btn-procesar:hover { background: #c99010; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(240,180,41,0.3); }
        .btn-procesar:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

        .cancel-link {
            display: block; text-align: center; margin-top: 0.8rem;
            font-size: 0.82rem; color: var(--text-secondary); text-decoration: none;
            transition: color 0.2s;
        }
        .cancel-link:hover { color: #e05252; }
    </style>
</head>
<body>
    <div class="main-card">

        <div class="demo-banner">
            <strong>⚡ Simulación API Gateway</strong>
            Elige el estado que deseas simular para esta transacción
        </div>

        <div class="card-title-main">¡Transacción de prueba!</div>
        <div class="card-subtitle">
            Selecciona el resultado que quieres ver.<br>
            En producción real, PlacetoPay determina este estado automáticamente.
        </div>

        <!-- Info producto -->
        <div class="product-info">
            <span class="product-info-name">🎮 <?= htmlspecialchars($producto) ?></span>
            <span class="product-info-price">$<?= number_format((float)$precio, 0, ',', '.') ?> COP</span>
        </div>

        <!-- Selector de estado -->
        <span class="estado-label">Estado de la transacción</span>
        <div class="estados-grid">
            <div class="estado-btn aprobada selected" onclick="selectEstado('aprobada', this)">
                <span class="check">✔</span>
                <div class="estado-icon">✅</div>
                <div class="estado-name">Aprobada</div>
            </div>
            <div class="estado-btn pendiente" onclick="selectEstado('pendiente', this)">
                <span class="check">✔</span>
                <div class="estado-icon">⏳</div>
                <div class="estado-name">Pendiente</div>
            </div>
            <div class="estado-btn rechazada" onclick="selectEstado('rechazada', this)">
                <span class="check">✔</span>
                <div class="estado-icon">❌</div>
                <div class="estado-name">Rechazada</div>
            </div>
        </div>

        <!-- Selector de razón -->
        <div class="razon-wrap">
            <span class="estado-label">Razón</span>
            <select class="razon-select" id="razonSelect">
                <!-- Opciones aprobada -->
                <option value="APPROVED_TRANSACTION" data-estado="aprobada">APPROVED_TRANSACTION (00)</option>
                <!-- Opciones pendiente -->
                <option value="PENDING_TRANSACTION" data-estado="pendiente">PENDING_TRANSACTION (?-)</option>
                <option value="PENDING_VALIDATION" data-estado="pendiente">PENDING_VALIDATION (?V)</option>
                <!-- Opciones rechazada -->
                <option value="CANCELLED_TRANSACTION" data-estado="rechazada">CANCELLED_TRANSACTION (?C)</option>
                <option value="FAILED_TRANSACTION" data-estado="rechazada">FAILED_TRANSACTION (?F)</option>
                <option value="REJECTED_TRANSACTION" data-estado="rechazada">REJECTED_TRANSACTION (?R)</option>
            </select>
        </div>

        <!-- Formulario oculto -->
        <form method="POST" action="../php/crear_pb_gateway.php" id="estadoForm">
            <input type="hidden" name="estado_elegido" id="estadoElegido" value="aprobada">
            <input type="hidden" name="razon_elegida" id="razonElegida" value="APPROVED_TRANSACTION">
            <?php foreach ($_SESSION['gw_pending'] as $key => $value): ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>
        </form>

        <button class="btn-procesar" id="btnProcesar" onclick="procesar()">
            <i class="bi bi-play-circle-fill"></i> Procesar transacción
        </button>
        <a href="javascript:history.back()" class="cancel-link">← Cancelar y volver</a>

    </div>

    <script>
    let estadoActual = 'aprobada';

    const razonesPorEstado = {
        aprobada:  ['APPROVED_TRANSACTION (00)'],
        pendiente: ['PENDING_TRANSACTION (?-)', 'PENDING_VALIDATION (?V)'],
        rechazada: ['CANCELLED_TRANSACTION (?C)', 'FAILED_TRANSACTION (?F)', 'REJECTED_TRANSACTION (?R)']
    };

    const valoresPorEstado = {
        aprobada:  ['APPROVED_TRANSACTION'],
        pendiente: ['PENDING_TRANSACTION', 'PENDING_VALIDATION'],
        rechazada: ['CANCELLED_TRANSACTION', 'FAILED_TRANSACTION', 'REJECTED_TRANSACTION']
    };

    function selectEstado(estado, el) {
        estadoActual = estado;
        document.querySelectorAll('.estado-btn').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('estadoElegido').value = estado;

        // Actualizar razones
        const select = document.getElementById('razonSelect');
        select.innerHTML = '';
        razonesPorEstado[estado].forEach(function(label, i) {
            const opt = document.createElement('option');
            opt.value = valoresPorEstado[estado][i];
            opt.textContent = label;
            select.appendChild(opt);
        });
        actualizarRazon();
    }

    function actualizarRazon() {
        const select = document.getElementById('razonSelect');
        document.getElementById('razonElegida').value = select.value;
    }

    document.getElementById('razonSelect').addEventListener('change', actualizarRazon);

    function procesar() {
        actualizarRazon();
        document.getElementById('btnProcesar').disabled = true;
        document.getElementById('btnProcesar').innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
        document.getElementById('estadoForm').submit();
    }

    // Init
    selectEstado('aprobada', document.querySelector('.estado-btn.aprobada'));
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>