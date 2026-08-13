<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['gw_subs_pending'] = $_POST;
}

if (empty($_SESSION['gw_subs_pending'])) {
    header("Location: ../home.php");
    exit();
}

$data     = $_SESSION['gw_subs_pending'];
$servicio = $data['servicio'] ?? 'Servicio';
$plan     = $data['plan']     ?? '';
$precio   = $data['precio']   ?? 0;
$tipo     = isset($data['guardar_tarjeta']) ? 'suscripciones' : 'suscription';
$es_pago_sub = ($tipo === 'suscripciones'); // streaming = pago+sub, music = pura
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de suscripción | Plance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base:#0d0e10; --bg-surface:#16181c; --bg-card:#1e2128;
            --border:#2e3038; --accent:#f0b429; --accent-soft:rgba(240,180,41,0.1);
            --text-primary:#f0f1f3; --text-secondary:#8a8d96;
            --font-display:'Barlow Condensed',sans-serif; --font-body:'Barlow',sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background-color:var(--bg-base);color:var(--text-primary);font-family:var(--font-body);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;}

        .main-card{background:var(--bg-surface);border:1px solid var(--border);border-radius:18px;padding:2.5rem 2rem;max-width:560px;width:100%;animation:fadeUp 0.4s ease both;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}

        .demo-banner{background:rgba(240,180,41,0.08);border:1px solid rgba(240,180,41,0.25);border-radius:10px;padding:0.8rem 1rem;text-align:center;margin-bottom:1.8rem;font-size:0.88rem;color:var(--accent);font-weight:600;}
        .demo-banner strong{display:block;font-size:1rem;margin-bottom:0.2rem;}

        .card-title-main{font-family:var(--font-display);font-size:1.6rem;font-weight:800;color:var(--text-primary);text-align:center;margin-bottom:0.4rem;letter-spacing:0.02em;}
        .card-subtitle{text-align:center;color:var(--text-secondary);font-size:0.88rem;margin-bottom:2rem;line-height:1.5;}

        .product-info{background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:0.9rem 1.2rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:1.8rem;}
        .product-info-name{font-weight:600;font-size:0.9rem;}
        .product-info-price{font-family:var(--font-display);font-size:1.2rem;font-weight:800;color:var(--accent);}

        .estado-label{font-family:var(--font-display);font-size:0.75rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:0.6rem;display:block;}

        /* 4 estados en grid 2x2 */
        .estados-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:0.7rem;margin-bottom:1.5rem;}

        .estado-btn{border:1.5px solid var(--border);background:var(--bg-card);border-radius:12px;padding:1rem 0.7rem;cursor:pointer;text-align:center;transition:all 0.2s;position:relative;}
        .estado-btn:hover{transform:translateY(-2px);}
        .estado-btn .check{display:none;position:absolute;top:0.4rem;right:0.5rem;font-size:0.7rem;font-weight:900;}
        .estado-btn.selected .check{display:block;}

        /* Colores por estado */
        .estado-btn.aprobada-token { color: #3ecf8e; }
        .estado-btn.aprobada-token:hover, .estado-btn.aprobada-token.selected { border-color:#3ecf8e; background:rgba(62,207,142,0.08); }
        .estado-btn.aprobada-token .check { color:#3ecf8e; }

        .estado-btn.aprobada-sin { color: #22c55e; }
        .estado-btn.aprobada-sin:hover, .estado-btn.aprobada-sin.selected { border-color:#22c55e; background:rgba(34,197,94,0.08); }
        .estado-btn.aprobada-sin .check { color:#22c55e; }

        .estado-btn.pendiente { color:#f0b429; }
        .estado-btn.pendiente:hover, .estado-btn.pendiente.selected { border-color:#f0b429; background:rgba(240,180,41,0.08); }
        .estado-btn.pendiente .check { color:#f0b429; }

        .estado-btn.rechazada { color:#e05252; }
        .estado-btn.rechazada:hover, .estado-btn.rechazada.selected { border-color:#e05252; background:rgba(224,82,82,0.08); }
        .estado-btn.rechazada .check { color:#e05252; }

        .estado-icon{font-size:1.5rem;margin-bottom:0.35rem;}
        .estado-name{font-family:var(--font-display);font-size:0.9rem;font-weight:800;letter-spacing:0.03em;}
        .estado-desc{font-size:0.72rem;color:var(--text-secondary);margin-top:0.2rem;line-height:1.3;}

        .razon-wrap{margin-bottom:1.5rem;}
        .razon-select{width:100%;background:var(--bg-card);border:1.5px solid var(--border);border-radius:10px;color:var(--text-primary);font-family:var(--font-body);font-size:0.88rem;padding:0.6rem 0.9rem;outline:none;transition:border-color 0.2s;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='%238a8d96'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 0.8rem center;padding-right:2rem;}
        .razon-select:focus{border-color:var(--accent);}
        .razon-select option{background:#1e2128;}

        .btn-procesar{width:100%;padding:0.9rem;background:var(--accent);border:none;border-radius:10px;color:#0d0e10;font-family:var(--font-display);font-size:1.1rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:0.5rem;}
        .btn-procesar:hover{background:#c99010;transform:translateY(-1px);box-shadow:0 6px 20px rgba(240,180,41,0.3);}
        .btn-procesar:disabled{opacity:0.4;cursor:not-allowed;transform:none;}
        .cancel-link{display:block;text-align:center;margin-top:0.8rem;font-size:0.82rem;color:var(--text-secondary);text-decoration:none;transition:color 0.2s;}
        .cancel-link:hover{color:#e05252;}
    </style>
</head>
<body>
    <div class="main-card">

        <div class="demo-banner">
            <strong>⚡ Simulación API Gateway — Suscripción</strong>
            Elige el estado que deseas simular para esta transacción
        </div>

        <div class="card-title-main">¡Transacción de prueba!</div>
        <div class="card-subtitle">
            Selecciona el resultado que quieres ver.<br>
            En producción real, PlacetoPay determina este estado automáticamente.
        </div>

        <div class="product-info">
            <span class="product-info-name">
                <?= $es_pago_sub ? '📺' : '🎵' ?>
                <?= htmlspecialchars($servicio) ?> — <?= htmlspecialchars($plan) ?>
            </span>
            <span class="product-info-price">$<?= number_format((float)$precio, 0, ',', '.') ?> COP</span>
        </div>

        <span class="estado-label">Estado de la transacción</span>
        <div class="estados-grid">
            <div class="estado-btn aprobada-token selected" onclick="selectEstado('aprobada-token', this)">
                <span class="check">✔</span>
                <div class="estado-icon">🔐</div>
                <div class="estado-name">Aprobada + Token</div>
                <div class="estado-desc">Pago exitoso y tarjeta guardada</div>
            </div>
            <div class="estado-btn aprobada-sin" onclick="selectEstado('aprobada-sin', this)">
                <span class="check">✔</span>
                <div class="estado-icon">✅</div>
                <div class="estado-name">Aprobada</div>
                <div class="estado-desc">Pago exitoso sin tokenizar</div>
            </div>
            <div class="estado-btn pendiente" onclick="selectEstado('pendiente', this)">
                <span class="check">✔</span>
                <div class="estado-icon">⏳</div>
                <div class="estado-name">Pendiente</div>
                <div class="estado-desc">En proceso de verificación</div>
            </div>
            <div class="estado-btn rechazada" onclick="selectEstado('rechazada', this)">
                <span class="check">✔</span>
                <div class="estado-icon">❌</div>
                <div class="estado-name">Rechazada</div>
                <div class="estado-desc">No se pudo procesar</div>
            </div>
        </div>

        <div class="razon-wrap">
            <span class="estado-label">Razón</span>
            <select class="razon-select" id="razonSelect">
                <option value="APPROVED_TRANSACTION">APPROVED_TRANSACTION (00) — Con token</option>
            </select>
        </div>

        <form method="POST" action="../php/<?= $es_pago_sub ? 'crear_suscripciones_gateway' : 'crear_suscription_gateway' ?>.php" id="estadoForm">
            <input type="hidden" name="estado_elegido"   id="estadoElegido"   value="aprobada-token">
            <input type="hidden" name="razon_elegida"    id="razonElegida"    value="APPROVED_TRANSACTION">
            <?php foreach ($data as $key => $value): ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>
        </form>

        <button class="btn-procesar" id="btnProcesar" onclick="procesar()">
            <i class="bi bi-play-circle-fill"></i> Procesar transacción
        </button>
        <a href="javascript:history.back()" class="cancel-link">← Cancelar y volver</a>
    </div>

    <script>
    const razones = {
        'aprobada-token': [{ v:'APPROVED_TRANSACTION', l:'APPROVED_TRANSACTION (00) — Con token' }],
        'aprobada-sin':   [{ v:'APPROVED_TRANSACTION', l:'APPROVED_TRANSACTION (00) — Sin token' }],
        'pendiente':      [{ v:'PENDING_TRANSACTION', l:'PENDING_TRANSACTION (?-)' }, { v:'PENDING_VALIDATION', l:'PENDING_VALIDATION (?V)' }],
        'rechazada':      [{ v:'CANCELLED_TRANSACTION', l:'CANCELLED_TRANSACTION (?C)' }, { v:'FAILED_TRANSACTION', l:'FAILED_TRANSACTION (?F)' }, { v:'REJECTED_TRANSACTION', l:'REJECTED_TRANSACTION (?R)' }]
    };

    function selectEstado(estado, el) {
        document.querySelectorAll('.estado-btn').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('estadoElegido').value = estado;

        const select = document.getElementById('razonSelect');
        select.innerHTML = '';
        razones[estado].forEach(function(r) {
            const opt = document.createElement('option');
            opt.value = r.v; opt.textContent = r.l;
            select.appendChild(opt);
        });
        actualizarRazon();
    }

    function actualizarRazon() {
        document.getElementById('razonElegida').value = document.getElementById('razonSelect').value;
    }

    document.getElementById('razonSelect').addEventListener('change', actualizarRazon);

    function procesar() {
        actualizarRazon();
        document.getElementById('btnProcesar').disabled = true;
        document.getElementById('btnProcesar').innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
        document.getElementById('estadoForm').submit();
    }

    selectEstado('aprobada-token', document.querySelector('.estado-btn.aprobada-token'));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>