<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IA's — Planes de Inteligencia Artificial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/estilos.?v=<?php echo time(); ?>">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    :root {
        --bg-base:        var(--pt-bg-base);
        --bg-surface:     var(--pt-bg-surface);
        --bg-card:        var(--pt-navbar);
        --bg-card-hover:  var(--pt-hover);
        --bg-selected:    rgba(230, 115, 5, 0.1);
        --border:         var(--pt-border);
        --accent:         hsl(29, 99%, 45%);
        --accent-glow:    rgba(255, 226, 96, 0.25);
        --accent-dark:    rgb(255, 187, 0);
        --text-primary:   var(--pt-text);
        --text-secondary: var(--pt-text-sec);
        --text-muted:     var(--pt-text-sec);
        --green:          #3ecf8e;
        --font-display:   'Barlow', sans-serif;
        --font-body:      'Barlow', sans-serif;
        --radius-sm:      6px;
        --radius-md:      10px;
        --radius-lg:      14px;
        --transition:     0.2s ease;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        background-color: var(--bg-base);
        color: var(--text-primary);
        font-family: var(--font-body);
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }
    .navbar { background-color: var(--pt-navbar) !important; backdrop-filter: blur(8px); border-bottom: 1px solid var(--border); }

    .game-banner {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.6rem 2rem;
        background: var(--pt-th2); border-bottom: 1px solid var(--pt-border); gap: 1rem;
    }
    .game-banner__tag {
        display: flex; align-items: center; gap: 0.5rem;
        font-family: var(--font-display); font-weight: 700;
        font-size: 1rem; letter-spacing: 0.04em; color: var(--text-primary);
    }
    .rec-badge {
        background: rgba(246, 192, 92, 0.15); color: var(--accent);
        font-size: 0.72rem; font-weight: 700;
        padding: 0.2rem 0.6rem; border-radius: 20px;
        letter-spacing: 0.05em; font-family: var(--font-display);
    }

    /* Selector periodicidad */
    .period-selector {
        display: flex; gap: 0.4rem;
    }
    .period-btn {
        padding: 0.35rem 0.9rem;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        background: var(--bg-card);
        color: var(--text-secondary);
        font-family: var(--font-body); font-size: 0.85rem;
        font-weight: 600; cursor: pointer; transition: all var(--transition);
    }
    .period-btn.active {
        border-color: var(--accent);
        background: rgba(246, 184, 92, 0.1);
        color: var(--accent);
    }

    .shop-layout {
        display: grid; grid-template-columns: 1fr 340px;
        gap: 1.5rem; max-width: 1200px;
        margin: 1.5rem auto; padding: 0 1.5rem 3rem; align-items: start;
    }

    .section-block { margin-bottom: 1.8rem; }
    .section-label {
        font-family: var(--font-display); font-size: 0.8rem;
        font-weight: 700; letter-spacing: 0.1em;
        text-transform: uppercase; color: var(--text-secondary); margin-bottom: 0.75rem;
    }
    .platform-header {
        display: flex; align-items: center; gap: 0.6rem;
        margin-bottom: 0.75rem; padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border);
    }
    .platform-header span {
        font-family: var(--font-display); font-size: 1.1rem;
        font-weight: 800; letter-spacing: 0.04em; color: var(--text-primary);
    }

    .products-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.65rem; }

    .product-card {
        position: relative; background: var(--bg-card);
        border: 1.5px solid var(--border); border-radius: var(--radius-md);
        padding: 1rem 0.85rem 0.9rem; cursor: pointer;
        transition: all 0.18s ease; display: flex;
        flex-direction: column; gap: 0.15rem; overflow: hidden;
    }
    .product-card:hover {
        background: var(--bg-card-hover);
        border-color: rgba(246, 210, 92, 0.4);
        transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.35);
    }
    .product-card.selected {
        background: var(--pt-border); border-color: var(--accent);
        box-shadow: 0 0 0 1px var(--accent), 0 4px 24px var(--accent-glow);
    }
    .product-card.selected::after {
        content: '✔'; position: absolute; top: 0.5rem; right: 0.55rem;
        width: 18px; height: 18px; background: var(--accent);
        border-radius: 50%; color: #0d0e10; font-size: 0.65rem;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; line-height: 18px; text-align: center;
    }
    .badge-popular {
        position: absolute; top: -1px; left: -1px;
        background: var(--accent); color: #0d0e10;
        font-family: var(--font-display); font-size: 0.68rem;
        font-weight: 800; letter-spacing: 0.05em;
        padding: 0.15rem 0.5rem;
        border-radius: var(--radius-sm) 0 var(--radius-sm) 0;
    }
    .product-card__platform {
        font-size: 0.7rem; font-weight: 600; color: var(--accent);
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.1rem;
    }
    .product-card__pts {
        font-family: var(--font-display); font-size: 1.2rem;
        font-weight: 800; color: var(--text-primary); line-height: 1.1;
    }
    .product-card__label { font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.3rem; }
    .product-card__price {
        font-family: var(--font-display); font-size: 1rem;
        font-weight: 700; color: var(--accent);
        display: flex; align-items: center; gap: 0.35rem;
        flex-wrap: wrap; margin-top: auto;
    }
    .rec-tag {
        background: rgba(246, 161, 92, 0.12); color: var(--accent);
        font-size: 0.65rem; font-weight: 700;
        padding: 0.1rem 0.35rem; border-radius: 3px;
    }
    .price-anual { display: none; }

    /* CHECKOUT */
    .checkout-panel { display: flex; flex-direction: column; gap: 1rem; position: sticky; top: 16px; }
    .checkout-summary, .delivery-instructions, .vendor-box {
        background: var(--bg-surface); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 1.2rem 1.3rem;
    }
    .checkout-product-name {
        font-family: var(--font-display); font-size: 1.3rem;
        font-weight: 800; color: var(--text-primary);
        margin-bottom: 1rem; letter-spacing: 0.02em; line-height: 1.2;
    }
    .checkout-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.65rem; }
    .checkout-label { font-size: 0.85rem; color: var(--text-secondary); }
    .checkout-delivery { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); }
    .checkout-divider { height: 1px; background: var(--border); margin: 0.8rem 0; }
    .checkout-total-row { align-items: flex-end; }
    .checkout-pricing { text-align: right; display: flex; flex-direction: column; gap: 0.15rem; }
    .checkout-final-price {
        font-family: var(--font-display); font-size: 1.6rem;
        font-weight: 800; color: var(--text-primary); line-height: 1;
    }
    .recurring-info {
        background: rgba(246, 192, 92, 0.08); border: 1px solid rgba(246, 205, 92, 0.2);
        border-radius: 8px; padding: 0.75rem 1rem; margin-top: 0.8rem;
        font-size: 0.8rem; color: #fab78b;
        display: flex; gap: 0.5rem; align-items: flex-start;
    }
    .btn-buy {
        width: 100%; margin-top: 1rem; padding: 0.85rem 1.2rem;
        background: var(--accent); border: none; border-radius: var(--radius-md);
        color: #0a0a0b; font-family: var(--font-display); font-size: 1.1rem;
        font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase;
        cursor: pointer; display: flex; align-items: center;
        justify-content: center; gap: 0.6rem; transition: all 0.18s ease;
        position: relative; overflow: hidden;
    }
    .btn-buy:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }
    .btn-arrow { font-size: 1.1rem; transition: transform 0.2s; }
    .btn-buy:hover .btn-arrow { transform: translateX(4px); }
    .trust-badges { margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .trust-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--text-secondary); }
    .instruction-text { font-size: 0.83rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 0.75rem; }
    .btn-instructions {
        background: none; border: 1px solid var(--border); color: var(--text-secondary);
        font-size: 0.82rem; padding: 0.35rem 0.8rem; border-radius: var(--radius-sm);
        cursor: pointer; transition: all var(--transition); font-family: var(--font-body);
    }
    .btn-instructions:hover { border-color: var(--accent); color: var(--text-primary); }
    .vendor-info { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }
    .vendor-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: linear-gradient(135deg, #ffae00, #ff7b00);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-weight: 800; font-size: 0.85rem; color: #fff; flex-shrink: 0;
    }
    .vendor-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
    .vendor-rating { font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.1rem; }

    @keyframes fadeSlideIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }
    .products-panel { animation: fadeSlideIn 0.4s ease both; }
    .checkout-panel { animation: fadeSlideIn 0.4s 0.1s ease both; }

    @media (max-width: 900px) { .shop-layout { grid-template-columns: 1fr; } .checkout-panel { position: static; } }
    @media (max-width: 600px) { .products-grid { grid-template-columns: 1fr; } .game-banner { flex-direction: column; align-items: flex-start; } }
</style>
<body>
    <?php
    $nav_back_url  = "suscripciones.php";
    $nav_back_text = "Atras";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>

    <div class="game-banner">
        <div class="game-banner__tag">
            <i class="bi bi-robot" style="color:hsl(29, 99%, 45%) ;"></i> IA's — Planes de Inteligencia Artificial
            <span class="rec-badge"><i class="fa-solid fa-globe" style="color:hsl(29, 99%, 45%) ;"></i> Suscripcion Recurrente</span>
        </div>
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <input type="email" id="usuarioIdInput" placeholder="Tu correo electronico"
                   value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>"
                   style="background:var(--pt-navbar);border:1.5px solid var(--pt-border);border-radius:8px;color:var(--pt-text);font-family:inherit;font-size:0.85rem;padding:0.45rem 0.8rem;outline:none;width:260px;">
            <div class="period-selector">
                <button class="period-btn active" id="btnMensual" onclick="setPeriod('mensual')">Mensual</button>
                <button class="period-btn" id="btnAnual" onclick="setPeriod('anual')">Anual</button>
            </div>
        </div>
    </div>

    <main class="shop-layout">
        <section class="products-panel">

            <!-- CLAUDE -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=claude.ai&sz=32" alt="Claude" style="width:24px;height:24px;border-radius:4px;">
                    <span>Claude</span>
                </div>
                <div class="products-grid">
                    <div class="product-card popular-card"
                         data-id="1" data-servicio="Claude" data-plan="Pro"
                         data-precio-mensual="22900" data-precio-anual="229000">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Claude</div>
                        <div class="product-card__pts">Pro</div>
                        <div class="product-card__label">Uso extendido · Proyectos · Prioridad</div>
                        <div class="product-card__price price-mensual">22.900 COP <span class="rec-tag">/ mes</span></div>
                        <div class="product-card__price price-anual">229.000 COP <span class="rec-tag">/ año</span></div>
                    </div>
                    <div class="product-card"
                         data-id="2" data-servicio="Claude" data-plan="Max"
                         data-precio-mensual="109000" data-precio-anual="1090000">
                        <div class="product-card__platform">Claude</div>
                        <div class="product-card__pts">Max</div>
                        <div class="product-card__label">5x más uso · Acceso anticipado</div>
                        <div class="product-card__price price-mensual">109.000 COP <span class="rec-tag">/ mes</span></div>
                        <div class="product-card__price price-anual">1.090.000 COP <span class="rec-tag">/ año</span></div>
                    </div>
                </div>
            </div>

            <!-- CHATGPT -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=chatgpt.com&sz=32" alt="ChatGPT" style="width:24px;height:24px;border-radius:4px;">
                    <span>ChatGPT</span>
                </div>
                <div class="products-grid">
                    <div class="product-card"
                         data-id="3" data-servicio="ChatGPT" data-plan="Go"
                         data-precio-mensual="8900" data-precio-anual="89000">
                        <div class="product-card__platform">ChatGPT</div>
                        <div class="product-card__pts">Go</div>
                        <div class="product-card__label">Acceso básico · GPT-4o mini</div>
                        <div class="product-card__price price-mensual">8.900 COP <span class="rec-tag">/ mes</span></div>
                        <div class="product-card__price price-anual">89.000 COP <span class="rec-tag">/ año</span></div>
                    </div>
                    <div class="product-card popular-card"
                         data-id="4" data-servicio="ChatGPT" data-plan="Plus"
                         data-precio-mensual="22900" data-precio-anual="229000">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">ChatGPT</div>
                        <div class="product-card__pts">Plus</div>
                        <div class="product-card__label">GPT-4o · DALL·E · Plugins</div>
                        <div class="product-card__price price-mensual">22.900 COP <span class="rec-tag">/ mes</span></div>
                        <div class="product-card__price price-anual">229.000 COP <span class="rec-tag">/ año</span></div>
                    </div>
                    <div class="product-card"
                         data-id="5" data-servicio="ChatGPT" data-plan="Pro"
                         data-precio-mensual="219000" data-precio-anual="2190000">
                        <div class="product-card__platform">ChatGPT</div>
                        <div class="product-card__pts">Pro</div>
                        <div class="product-card__label">Uso ilimitado · o1 Pro · Acceso total</div>
                        <div class="product-card__price price-mensual">219.000 COP <span class="rec-tag">/ mes</span></div>
                        <div class="product-card__price price-anual">2.190.000 COP <span class="rec-tag">/ año</span></div>
                    </div>
                </div>
            </div>

        </section>

        <!-- CHECKOUT -->
        <aside class="checkout-panel" id="checkoutPanel">
            <div class="checkout-summary">
                <div class="checkout-product-name" id="checkoutName">🤖 Claude — Pro</div>
                <div class="checkout-row">
                    <span class="checkout-label">Periodicidad</span>
                    <span class="checkout-delivery" id="checkoutPeriod">Mensual</span>
                </div>
                <div class="checkout-row">
                    <span class="checkout-label">Duración</span>
                    <span class="checkout-delivery" id="checkoutDuration">12 meses</span>
                </div>
                <div class="checkout-divider"></div>
                <div class="checkout-row checkout-total-row">
                    <span class="checkout-label">Total</span>
                    <div class="checkout-pricing">
                        <span class="checkout-final-price" id="checkoutPrice">22.900 COP</span>
                    </div>
                </div>
                <div class="recurring-info">
                    <i class="bi bi-arrow-repeat"></i>
                    <span id="recurringMsg">Cobro automático mensual durante 12 meses.</span>
                </div>
                <button class="btn-buy" id="btnBuy">
                    <span>Suscribirse ahora</span>
                    <span class="btn-arrow">→</span>
                </button>
                <!-- <div class="trust-badges">
                    <div class="trust-item">🛡️ <span>Garantía de reembolso · P2P</span></div>
                    <div class="trust-item">🔄 <span>Cobro recurrente automático</span></div>
                    <div class="trust-item">💬 <span>Asistencia en directo 24/7</span></div>
                </div> -->
            </div>

            <div class="delivery-instructions">
                <p class="section-label">Instrucciones</p>
                <div class="instruction-text" id="instructionText">
                    Claude® | Plan Pro 🤖<br>
                    <span>🌐</span> Acceso inmediato tras el primer pago<br>
                    <span>🔄</span> Renovación automática
                </div>
                <button class="btn-instructions">Ver todas las instrucciones ▾</button>
            </div>

            <div class="vendor-box">
                <p class="section-label">Designer</p>
                <div class="vendor-info">
                    <div class="vendor-avatar">JM</div>
                    <div>
                        <div class="vendor-name">Jair ✅</div>
                        <div class="vendor-rating">👍 2026 · <a href="#">Evertec Placetopay SAS</a></div>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <input type="hidden" id="currentPeriod" value="mensual">

    <script>
    (function() {
        const products = {
            1: { name: '🤖 Claude — Pro',     servicio: 'Claude',   plan: 'Pro',   precioM: 22900,   precioA: 229000 },
            2: { name: '🤖 Claude — Max',     servicio: 'Claude',   plan: 'Max',   precioM: 109000,  precioA: 1090000 },
            3: { name: '💬 ChatGPT — Go',     servicio: 'ChatGPT',  plan: 'Go',    precioM: 8900,    precioA: 89000 },
            4: { name: '💬 ChatGPT — Plus',   servicio: 'ChatGPT',  plan: 'Plus',  precioM: 22900,   precioA: 229000 },
            5: { name: '💬 ChatGPT — Pro',    servicio: 'ChatGPT',  plan: 'Pro',   precioM: 219000,  precioA: 2190000 },
        };

        function fmt(n) {
            return n.toLocaleString('es-CO') + ' COP';
        }

        function updateCheckout(id) {
            const p = products[id];
            if (!p) return;
            const period = document.getElementById('currentPeriod').value;
            const precio = period === 'mensual' ? p.precioM : p.precioA;
            const tag    = period === 'mensual' ? '/ mes' : '/ año';
            const dur    = period === 'mensual' ? '12 meses' : '1 año';

            document.getElementById('checkoutName').textContent    = p.name;
            document.getElementById('checkoutPrice').textContent   = fmt(precio);
            document.getElementById('checkoutPeriod').textContent  = period === 'mensual' ? 'Mensual' : 'Anual';
            document.getElementById('checkoutDuration').textContent = dur;
            document.getElementById('recurringMsg').textContent    = 'Cobro automático ' + (period === 'mensual' ? 'mensual durante 12 meses.' : 'anual por 1 año.');
            document.getElementById('instructionText').innerHTML   =
                p.servicio + '\u00ae | Plan ' + p.plan + ' \uD83E\uDD16<br>' +
                '<span>\uD83C\uDF10</span> Acceso inmediato tras el primer pago<br>' +
                '<span>\uD83D\uDD04</span> Renovaci\u00f3n autom\u00e1tica ' + (period === 'mensual' ? 'mensual' : 'anual');
        }

        window.setPeriod = function(period) {
            document.getElementById('currentPeriod').value = period;
            document.getElementById('btnMensual').classList.toggle('active', period === 'mensual');
            document.getElementById('btnAnual').classList.toggle('active', period === 'anual');

            document.querySelectorAll('.price-mensual').forEach(el => el.style.display = period === 'mensual' ? 'flex' : 'none');
            document.querySelectorAll('.price-anual').forEach(el => el.style.display = period === 'anual' ? 'flex' : 'none');

            const sel = document.querySelector('.product-card.selected');
            if (sel) updateCheckout(parseInt(sel.getAttribute('data-id')));
        };

        function initCards() {
            const cards = document.querySelectorAll('.product-card');
            if (cards.length === 0) { setTimeout(initCards, 100); return; }

            cards.forEach(function(card) {
                card.addEventListener('click', function() {
                    cards.forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    updateCheckout(parseInt(card.getAttribute('data-id')));
                });
            });

            var def = document.querySelector('.product-card[data-id="1"]');
            if (def) { def.classList.add('selected'); updateCheckout(1); }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCards);
        } else { initCards(); }

        document.addEventListener('click', function(e) {
            var btn = e.target.closest('#btnBuy');
            if (!btn) return;

            var usuarioId = document.getElementById('usuarioIdInput').value.trim();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!usuarioId || !emailRegex.test(usuarioId)) {
                alert('⚠️ Por favor ingresa un correo electrónico válido.');
                document.getElementById('usuarioIdInput').focus();
                return;
            }

            var selectedCard = document.querySelector('.product-card.selected');
            if (!selectedCard) { alert('⚠️ Selecciona un plan primero.'); return; }

            var period  = document.getElementById('currentPeriod').value;
            var id      = parseInt(selectedCard.getAttribute('data-id'));
            var p       = <?= json_encode([
                1 => ['servicio'=>'Claude','plan'=>'Pro','precioM'=>22900,'precioA'=>229000],
                2 => ['servicio'=>'Claude','plan'=>'Max','precioM'=>109000,'precioA'=>1090000],
                3 => ['servicio'=>'ChatGPT','plan'=>'Go','precioM'=>8900,'precioA'=>89000],
                4 => ['servicio'=>'ChatGPT','plan'=>'Plus','precioM'=>22900,'precioA'=>229000],
                5 => ['servicio'=>'ChatGPT','plan'=>'Pro','precioM'=>219000,'precioA'=>2190000],
            ]) ?>[id];

            var precio = period === 'mensual' ? p.precioM : p.precioA;
            var periodicidad = period === 'mensual' ? 'M' : 'Y';

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../php/crear_suscription_rec.php';

            [['servicio', p.servicio], ['plan', p.plan], ['precio', precio],
             ['usuario_id', usuarioId], ['periodicidad', periodicidad]].forEach(function(pair) {
                var input = document.createElement('input');
                input.type = 'hidden'; input.name = pair[0]; input.value = pair[1];
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>