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
    <title>Membresías y Verificados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    :root {
        --bg-base:        var(--pt-bg-base);
        --bg-surface:     var(--pt-bg-surface);
        --bg-card:        var(--pt-navbar);
        --bg-card-hover:  var(--pt-hover);
        --bg-selected:    rgba(77, 159, 255, 0.1);
        --border:         var(--pt-border);
        --accent:         #4d9fff;
        --accent-glow:    rgba(77,159,255,0.25);
        --accent-dark:    #2176d9;
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
    html { font-size: 16px; scroll-behavior: smooth; }
    body {
        background-color: var(--bg-base);
        color: var(--text-primary);
        font-family: var(--font-body);
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }
    a { color: var(--accent); text-decoration: none; }
    a:hover { text-decoration: underline; }

    .navbar {
        background-color: var(--pt-navbar) !important;
        backdrop-filter: blur(8px);
        border-bottom: 1px solid var(--border);
    }

    /* GAME BANNER */
    .game-banner {
        display: flex; align-items: center;
        justify-content: space-between;
        padding: 0.6rem 2rem;
        background: var(--bg-surface);
        border-bottom: 1px solid var(--border);
        gap: 1rem;
    }
    .game-banner__tag {
        display: flex; align-items: center; gap: 0.5rem;
        font-family: var(--font-body); font-weight: 700;
        font-size: 1rem; letter-spacing: 0.04em;
        color: var(--text-primary);
    }
    .recurring-badge {
        background: rgba(77,159,255,0.15);
        color: var(--accent);
        font-size: 0.72rem; font-weight: 700;
        padding: 0.2rem 0.6rem; border-radius: 20px;
        letter-spacing: 0.05em; font-family: var(--font-display);
    }

    /* MAIN LAYOUT */
    .shop-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
        max-width: 1200px;
        margin: 1.5rem auto;
        padding: 0 1.5rem 3rem;
        align-items: start;
    }

    .section-block { margin-bottom: 1.8rem; }
    .section-label {
        font-family: var(--font-display); font-size: 0.8rem;
        font-weight: 700; letter-spacing: 0.1em;
        text-transform: uppercase; color: var(--text-secondary);
        margin-bottom: 0.75rem;
    }

    /* PLATFORM HEADER */
    .platform-header {
        display: flex; align-items: center; gap: 0.6rem;
        margin-bottom: 0.75rem; padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border);
    }
    .platform-header span {
        font-family: var(--font-display); font-size: 1.1rem;
        font-weight: 800; letter-spacing: 0.04em; color: var(--text-primary);
    }

    /* PRODUCTS GRID */
    .products-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.65rem;
    }

    .product-card {
        position: relative;
        background: var(--bg-card); border: 1.5px solid var(--border);
        border-radius: var(--radius-md); padding: 1rem 0.85rem 0.9rem;
        cursor: pointer; transition: all 0.18s ease;
        display: flex; flex-direction: column; gap: 0.15rem; overflow: hidden;
    }
    .product-card:hover {
        background: var(--bg-card-hover);
        border-color: rgba(77,159,255,0.4);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.35);
    }
    .product-card.selected {
        background: var(--pt-border);
        border-color: var(--accent);
        box-shadow: 0 0 0 1px var(--accent), 0 4px 24px var(--accent-glow);
    }
    .product-card.selected::after {
        content: '✔';
        position: absolute; top: 0.5rem; right: 0.55rem;
        width: 18px; height: 18px;
        background: var(--accent); border-radius: 50%;
        color: #0d0e10; font-size: 0.65rem;
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
        font-family: var(--font-body); font-size: 1.0rem;
        font-weight: 500; color: var(--text-primary); line-height: 1.1;
    }
    .product-card__label {
        font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.3rem;
    }
    .product-card__price {
        font-family: var(--font-body); font-size: 1rem;
        font-weight: 600; color: var(--accent);
        display: flex; align-items: center; gap: 0.35rem;
        flex-wrap: wrap; margin-top: auto;
    }

    .recurrente-tag {
        background: rgba(77,159,255,0.12); color: var(--accent);
        font-size: 0.65rem; font-weight: 700;
        padding: 0.1rem 0.35rem; border-radius: 3px;
    }

    /* CHECKOUT PANEL */
    .checkout-panel {
        display: flex; flex-direction: column; gap: 1rem;
        position: sticky; top: 16px;
    }
    .checkout-summary, .delivery-instructions, .vendor-box {
        background: var(--bg-surface); border: 1px solid var(--border);
        border-radius: var(--radius-lg); padding: 1.2rem 1.3rem;
    }
    .checkout-product-name {
        font-family: var(--font-body); font-size: 1.0rem;
        font-weight: 700; color: var(--text-primary);
        margin-bottom: 1rem; letter-spacing: 0.02em; line-height: 1.2;
    }
    .checkout-row { 
        display: flex; justify-content: space-between;
        align-items: flex-start; margin-bottom: 0.65rem;
    }
    .checkout-label { font-size: 0.85rem; color: var(--text-secondary); }
    .checkout-delivery { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); }
    .checkout-divider { height: 1px; background: var(--border); margin: 0.8rem 0; }
    .checkout-total-row { align-items: flex-end; }
    .checkout-pricing { text-align: right; display: flex; flex-direction: column; gap: 0.15rem; }
    .checkout-final-price {
        font-family: var(--font-body); font-size: 1.3rem;
        font-weight: 500; color: var(--text-primary); line-height: 1;
    }

    /* Recurrencia info box */
    .recurring-info {
        background: rgba(77,159,255,0.08);
        border: 1px solid rgba(77,159,255,0.2);
        border-radius: 8px; padding: 0.75rem 1rem;
        margin-top: 0.8rem;
        font-size: 0.8rem; color: #7ab8ff;
        display: flex; gap: 0.5rem; align-items: flex-start;
    }
    .recurring-info i { flex-shrink: 0; margin-top: 0.1rem; }

    .btn-buy {
        width: 100%; margin-top: 1rem; padding: 0.85rem 1.2rem;
        background: var(--accent); border: none; border-radius: var(--radius-md);
        color: #0a0a0b; font-family: var(--font-body);
        font-size: 1.1rem; font-weight: 600; letter-spacing: 0.06em;
        text-transform: uppercase; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        gap: 0.6rem; transition: all 0.18s ease;
        position: relative; overflow: hidden;
    }
    .btn-buy::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.12), transparent);
        transform: translateX(-100%); transition: transform 0.5s ease;
    }
    .btn-buy:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }
    .btn-buy:hover::before { transform: translateX(100%); }
    .btn-buy:active { transform: translateY(0); }
    .btn-arrow { font-size: 1.1rem; transition: transform 0.2s; }
    .btn-buy:hover .btn-arrow { transform: translateX(4px); }

    .trust-badges { margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .trust-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--text-secondary); }

    .instruction-text { font-size: 0.83rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 0.75rem; }

    .btn-instructions {
        background: none; border: 1px solid var(--border);
        color: var(--text-secondary); font-size: 0.82rem;
        padding: 0.35rem 0.8rem; border-radius: var(--radius-sm);
        cursor: pointer; transition: all var(--transition); font-family: var(--font-body);
    }
    .btn-instructions:hover { border-color: var(--accent); color: var(--text-primary); }

    .vendor-info { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }
    .vendor-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: linear-gradient(135deg, #4d9fff, #2176d9);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-weight: 800;
        font-size: 0.85rem; color: #0d0e10; flex-shrink: 0;
    }
    .vendor-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
    .vendor-rating { font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.1rem; }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .products-panel { animation: fadeSlideIn 0.4s ease both; }
    .checkout-panel { animation: fadeSlideIn 0.4s 0.1s ease both; }

    @media (max-width: 900px) {
        .shop-layout { grid-template-columns: 1fr; }
        .checkout-panel { position: static; }
        .products-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .products-grid { grid-template-columns: 1fr; }
        .game-banner { flex-direction: column; align-items: flex-start; }
    }
</style>
<body>
    <?php
    $nav_back_url  = "suscripciones.php";
    $nav_back_text = "Atrás";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>

    <!-- GAME BANNER -->
    <div class="game-banner">
        <div class="game-banner__tag">
            <i class="fa-solid fa-globe" style="color: #4d9fff;"></i> Membresías y Verificados
            <span class="recurring-badge"><i class="bi bi-calendar-check-fill" style="color: #4d9fff;"></i> Pago Recurrente</span>
        </div>
    </div>

    <!-- MAIN LAYOUT -->
    <main class="shop-layout">

        <!-- LEFT: Products -->
        <section class="products-panel">

            <!-- YOUTUBE PREMIUM -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=youtube.com&sz=32" alt="YouTube" style="width:24px;height:24px;border-radius:4px;">
                    <span>YouTube Premium</span>
                </div>
                <div class="products-grid">
                    <div class="product-card popular-card" data-id="1" data-servicio="YouTube Premium" data-plan="Individual" data-precio="19900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">YouTube</div>
                        <div class="product-card__pts">Individual</div>
                        <div class="product-card__label">Sin anuncios · YT Music · 1 cuenta</div>
                        <div class="product-card__price">19.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="2" data-servicio="YouTube Premium" data-plan="Familiar" data-precio="29900">
                        <div class="product-card__platform">YouTube</div>
                        <div class="product-card__pts">Familiar</div>
                        <div class="product-card__label">Sin anuncios · YT Music · 5 cuentas</div>
                        <div class="product-card__price">29.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

            <!-- TWITTER/X VERIFICADO -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=x.com&sz=32" alt="Twitter X" style="width:24px;height:24px;border-radius:4px;">
                    <span>X (Twitter) Verificado</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="3" data-servicio="Twitter Verificado" data-plan="Basic" data-precio="14900">
                        <div class="product-card__platform">X · Twitter</div>
                        <div class="product-card__pts">Basic</div>
                        <div class="product-card__label">Verificado · Editar tweets</div>
                        <div class="product-card__price">14.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="4" data-servicio="Twitter Verificado" data-plan="Premium" data-precio="32900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">X · Twitter</div>
                        <div class="product-card__pts">Premium</div>
                        <div class="product-card__label">Verificado · Menos anuncios · Grok AI</div>
                        <div class="product-card__price">32.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="5" data-servicio="Twitter Verificado" data-plan="Premium+" data-precio="49900">
                        <div class="product-card__platform">X · Twitter</div>
                        <div class="product-card__pts">Premium+</div>
                        <div class="product-card__label">Sin anuncios · Grok AI avanzado</div>
                        <div class="product-card__price">49.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

            <!-- META VERIFIED -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=meta.com&sz=32" alt="Meta" style="width:24px;height:24px;border-radius:4px;">
                    <span>Meta Verified</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="6" data-servicio="Meta Verified" data-plan="Instagram" data-precio="24900">
                        <div class="product-card__platform">Meta · Instagram</div>
                        <div class="product-card__pts">Instagram</div>
                        <div class="product-card__label">✓ Verificado · Soporte prioritario</div>
                        <div class="product-card__price">24.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="7" data-servicio="Meta Verified" data-plan="Facebook" data-precio="24900">
                        <div class="product-card__platform">Meta · Facebook</div>
                        <div class="product-card__pts">Facebook</div>
                        <div class="product-card__label">✓ Verificado · Soporte prioritario</div>
                        <div class="product-card__price">24.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="8" data-servicio="Meta Verified" data-plan="Instagram + Facebook" data-precio="39900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Meta · Combo</div>
                        <div class="product-card__pts">IG + FB</div>
                        <div class="product-card__label">✓ Ambas plataformas</div>
                        <div class="product-card__price">39.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

        </section>

        <!-- RIGHT: Checkout -->
        <aside class="checkout-panel" id="checkoutPanel">
            <div class="checkout-summary">
                <div class="checkout-product-name" id="checkoutName">▶️ YouTube Premium — Individual</div>

                <div class="checkout-row">
                    <span class="checkout-label">Periodicidad</span>
                    <span class="checkout-delivery">Mensual</span>
                </div>
                <div class="checkout-row">
                    <span class="checkout-label">Duración</span>
                    <span class="checkout-delivery">12 meses</span>
                </div>

                <div class="checkout-divider"></div>

                <div class="checkout-row checkout-total-row">
                    <span class="checkout-label">Total / mes</span>
                    <div class="checkout-pricing">
                        <span class="checkout-final-price" id="checkoutPrice">19.900 COP</span>
                    </div>
                </div>

                <div class="recurring-info">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Este servicio se cobra automáticamente cada mes durante 12 meses. Puedes cancelar en cualquier momento.</span>
                </div>

                <div class="checkout-divider"></div>

                <div class="field-group" style="margin-bottom:0.6rem;">
                    <label class="field-label" style="font-size:0.73rem;font-weight:600;color:var(--text-secondary,#8a8d96);margin-bottom:0.25rem;display:block;">Correo electrónico</label>
                    <input type="email" id="usuarioIdInput" placeholder="tucorreo@ejemplo.com"
                           value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>"
                           style="width:100%;background:var(--pt-navbar);border:1.5px solid var(--border,#2e3038);border-radius:8px;color:var(--pt-text);font-family:inherit;font-size:0.85rem;padding:0.45rem 0.7rem;outline:none;">
                </div>

                <button class="btn-buy" id="btnBuy">
                    <span>Suscribirse ahora</span>
                    <span class="btn-arrow">→</span>
                </button>

                <!-- <div class="trust-badges">
                    <div class="trust-item">🛡️ <span>Garantía de reembolso · P2P</span></div>
                    <div class="trust-item">🔄 <span>Cobro recurrente automático mensual</span></div>
                    <div class="trust-item">💬 <span>Asistencia en directo 24/7</span></div>
                </div> -->
            </div>

            <div class="delivery-instructions">
                <p class="section-label">Instrucciones</p>
                <div class="instruction-text" id="instructionText">
                    YouTube Premium® | Plan Individual 🎬<br>
                    <span>🌐</span> Acceso inmediato tras el primer pago<br>
                    <span>🔄</span> Renovación automática cada mes
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

    <script>
    (function() {
        const products = {
            1: { name: '▶️ YouTube Premium — Individual', servicio: 'YouTube Premium', plan: 'Individual',          price: '19.900 COP', precio: 19900 },
            2: { name: '▶️ YouTube Premium — Familiar',  servicio: 'YouTube Premium', plan: 'Familiar',            price: '29.900 COP', precio: 29900 },
            3: { name: '𝕏 Twitter Verificado — Basic',   servicio: 'Twitter Verificado', plan: 'Basic',            price: '14.900 COP', precio: 14900 },
            4: { name: '𝕏 Twitter Verificado — Premium', servicio: 'Twitter Verificado', plan: 'Premium',          price: '32.900 COP', precio: 32900 },
            5: { name: '𝕏 Twitter Verificado — Premium+',servicio: 'Twitter Verificado', plan: 'Premium+',         price: '49.900 COP', precio: 49900 },
            6: { name: '✓ Meta Verified — Instagram',    servicio: 'Meta Verified',      plan: 'Instagram',        price: '24.900 COP', precio: 24900 },
            7: { name: '✓ Meta Verified — Facebook',     servicio: 'Meta Verified',      plan: 'Facebook',         price: '24.900 COP', precio: 24900 },
            8: { name: '✓ Meta Verified — IG + FB',      servicio: 'Meta Verified',      plan: 'Instagram + Facebook', price: '39.900 COP', precio: 39900 },
        };

        function updateCheckout(id) {
            const p = products[id];
            if (!p) return;
            document.getElementById('checkoutName').textContent  = p.name;
            document.getElementById('checkoutPrice').textContent = p.price;
            document.getElementById('instructionText').innerHTML =
                p.servicio + '\u00ae | ' + p.plan + '<br>' +
                '<span>\uD83C\uDF10</span> Acceso inmediato tras el primer pago<br>' +
                '<span>\uD83D\uDD04</span> Renovaci\u00f3n autom\u00e1tica cada mes';
        }

        function initCards() {
            const cards = document.querySelectorAll('.product-card');
            if (cards.length === 0) { setTimeout(initCards, 100); return; }

            cards.forEach(function(card) {
                card.addEventListener('click', function() {
                    cards.forEach(function(c) { c.classList.remove('selected'); });
                    card.classList.add('selected');
                    updateCheckout(parseInt(card.getAttribute('data-id')));
                });
            });

            // Selección por defecto
            var def = document.querySelector('.product-card[data-id="1"]');
            if (def) { def.classList.add('selected'); updateCheckout(1); }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCards);
        } else {
            initCards();
        }

        // Buy button
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

            var servicio = selectedCard.getAttribute('data-servicio');
            var plan     = selectedCard.getAttribute('data-plan');
            var precio   = selectedCard.getAttribute('data-precio');

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../php/crear_recurrencia.php';

            [['servicio', servicio], ['plan', plan], ['precio', precio], ['usuario_id', usuarioId]].forEach(function(pair) {
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
