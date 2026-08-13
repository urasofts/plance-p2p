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
    <title>Otras Plataformas — Suscripción</title>
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
        --bg-selected:    rgba(34, 197, 94, 0.1);
        --border:         var(--pt-border);
        --accent:         #22c55e;
        --accent-glow:    rgba(34,197,94,0.25);
        --accent-dark:    #16a34a;
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
    body { background-color: var(--bg-base); color: var(--text-primary); font-family: var(--font-body); min-height: 100vh; -webkit-font-smoothing: antialiased; }
    .navbar { background-color: var(--pt-navbar) !important; backdrop-filter: blur(8px); border-bottom: 1px solid var(--border); }

    .game-banner {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.6rem 2rem; background: var(--pt-th2);
        border-bottom: 1px solid var(--border); gap: 1rem;
    }
    .game-banner__tag {
        display: flex; align-items: center; gap: 0.5rem;
        font-family: var(--font-display); font-weight: 700;
        font-size: 1rem; letter-spacing: 0.04em; color: var(--text-primary);
    }
    .sub-badge {
        background: rgba(34,197,94,0.15); color: var(--accent);
        font-size: 0.72rem; font-weight: 700;
        padding: 0.2rem 0.6rem; border-radius: 20px;
        letter-spacing: 0.05em; font-family: var(--font-display);
    }

    .shop-layout {
        display: grid; grid-template-columns: 1fr 340px;
        gap: 1.5rem; max-width: 1200px;
        margin: 1.5rem auto; padding: 0 1.5rem 3rem; align-items: start;
    }

    .section-block { margin-bottom: 1.8rem; }
    .platform-header {
        display: flex; align-items: center; gap: 0.6rem;
        margin-bottom: 0.75rem; padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border);
    }
    .platform-header span {
        font-family: var(--font-display); font-size: 1.1rem;
        font-weight: 800; letter-spacing: 0.04em; color: var(--text-primary);
    }

    .products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.65rem; }

    .product-card {
        position: relative; background: var(--bg-card);
        border: 1.5px solid var(--border); border-radius: var(--radius-md);
        padding: 1rem 0.85rem 0.9rem; cursor: pointer;
        transition: all 0.18s ease; display: flex;
        flex-direction: column; gap: 0.15rem; overflow: hidden;
    }
    .product-card:hover { background: var(--bg-card-hover); border-color: rgba(34,197,94,0.4); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.35); }
    .product-card.selected { background: var(--pt-border); border-color: var(--accent); box-shadow: 0 0 0 1px var(--accent), 0 4px 24px var(--accent-glow); }
    .product-card.selected::after {
        content: '✔'; position: absolute; top: 0.5rem; right: 0.55rem;
        width: 18px; height: 18px; background: var(--accent); border-radius: 50%;
        color: #0d0e10; font-size: 0.65rem; display: flex; align-items: center;
        justify-content: center; font-weight: 900; line-height: 18px; text-align: center;
    }
    .badge-popular {
        position: absolute; top: -1px; left: -1px; background: var(--accent); color: #0d0e10;
        font-family: var(--font-display); font-size: 0.68rem; font-weight: 800;
        letter-spacing: 0.05em; padding: 0.15rem 0.5rem;
        border-radius: var(--radius-sm) 0 var(--radius-sm) 0;
    }
    .product-card__platform { font-size: 0.7rem; font-weight: 600; color: var(--accent); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.1rem; }
    .product-card__pts { font-family: var(--font-display); font-size: 1.2rem; font-weight: 800; color: var(--text-primary); line-height: 1.1; }
    .product-card__label { font-size: 0.72rem; color: var(--text-secondary); margin-bottom: 0.3rem; }
    .product-card__price { font-family: var(--font-display); font-size: 1rem; font-weight: 700; color: var(--accent); display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; margin-top: auto; }
    .sub-tag { background: rgba(34,197,94,0.12); color: var(--accent); font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.35rem; border-radius: 3px; }

    /* CHECKOUT */
    .checkout-panel { display: flex; flex-direction: column; gap: 1rem; position: sticky; top: 16px; }
    .checkout-summary, .delivery-instructions, .vendor-box { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.2rem 1.3rem; }
    .checkout-product-name { font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1rem; letter-spacing: 0.02em; line-height: 1.2; }
    .checkout-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.65rem; }
    .checkout-label { font-size: 0.85rem; color: var(--text-secondary); }
    .checkout-delivery { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); }
    .checkout-divider { height: 1px; background: var(--border); margin: 0.8rem 0; }
    .checkout-total-row { align-items: flex-end; }
    .checkout-pricing { text-align: right; }
    .checkout-final-price { font-family: var(--font-display); font-size: 1.6rem; font-weight: 800; color: var(--text-primary); line-height: 1; }
    .sub-info { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); border-radius: 8px; padding: 0.75rem 1rem; margin-top: 0.8rem; font-size: 0.8rem; color: #86efac; display: flex; gap: 0.5rem; align-items: flex-start; }
    .btn-buy { width: 100%; margin-top: 1rem; padding: 0.85rem 1.2rem; background: var(--accent); border: none; border-radius: var(--radius-md); color: #0a0a0b; font-family: var(--font-display); font-size: 1.1rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.6rem; transition: all 0.18s ease; }
    .btn-buy:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }
    .btn-arrow { font-size: 1.1rem; transition: transform 0.2s; }
    .btn-buy:hover .btn-arrow { transform: translateX(4px); }
    .trust-badges { margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .trust-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--text-secondary); }
    .instruction-text { font-size: 0.83rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 0.75rem; }
    .btn-instructions { background: none; border: 1px solid var(--border); color: var(--text-secondary); font-size: 0.82rem; padding: 0.35rem 0.8rem; border-radius: var(--radius-sm); cursor: pointer; transition: all var(--transition); font-family: var(--font-body); }
    .btn-instructions:hover { border-color: var(--accent); color: var(--text-primary); }
    .vendor-info { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }
    .vendor-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #22c55e, #16a34a); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-weight: 800; font-size: 0.85rem; color: #0d0e10; flex-shrink: 0; }
    .vendor-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
    .vendor-rating { font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.1rem; }

    @keyframes fadeSlideIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }
    .products-panel { animation: fadeSlideIn 0.4s ease both; }
    .checkout-panel { animation: fadeSlideIn 0.4s 0.1s ease both; }
    @media (max-width: 900px) { .shop-layout { grid-template-columns: 1fr; } .checkout-panel { position: static; } .products-grid { grid-template-columns: repeat(2,1fr); } }
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
            <i class="bi bi-tv-fill" style="color: #16a34a;"></i>Otras Plataformas — Suscripción Pura
            <span class="sub-badge"><i class="bi bi-shield-lock-fill"></i> Tokenización</span>
        </div>
        <div class="banner-correo" style="display:flex;align-items:center;gap:0.5rem;">
            <input type="email" id="usuarioIdInput" placeholder="Ingresa tu correo electrónico"
                   value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>"
                   style="width:100%;max-width:340px;background:var(--pt-navbar);border:1.5px solid var(--pt-border);border-radius:8px;color:var(--pt-text);font-family:inherit;font-size:0.85rem;padding:0.5rem 0.8rem;outline:none;">
        </div>
    </div>

    <main class="shop-layout">
        <section class="products-panel">

            <!-- AMAZON PRIME -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=primevideo.com&sz=32" alt="Amazon Prime" style="width:24px;height:24px;border-radius:4px;">
                    <span>Amazon Prime</span>
                </div>
                <div class="products-grid">
                    <div class="product-card popular-card" data-id="1" data-servicio="Amazon Prime" data-plan="Mensual" data-precio="9900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Amazon Prime</div>
                        <div class="product-card__pts">Mensual</div>
                        <div class="product-card__label">Video · Music · Envíos · 1 mes</div>
                        <div class="product-card__price">9.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="2" data-servicio="Amazon Prime" data-plan="Anual" data-precio="89900">
                        <div class="product-card__platform">Amazon Prime</div>
                        <div class="product-card__pts">Anual</div>
                        <div class="product-card__label">Video · Music · Envíos · 12 meses</div>
                        <div class="product-card__price">89.900 COP <span class="sub-tag">/ año</span></div>
                    </div>
                </div>
            </div>

            <!-- CRUNCHYROLL -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=crunchyroll.com&sz=32" alt="Crunchyroll" style="width:24px;height:24px;border-radius:4px;">
                    <span>Crunchyroll</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="3" data-servicio="Crunchyroll" data-plan="Fan" data-precio="12900">
                        <div class="product-card__platform">Crunchyroll</div>
                        <div class="product-card__pts">Fan</div>
                        <div class="product-card__label">Anime HD · Sin anuncios · 1 pantalla</div>
                        <div class="product-card__price">12.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="4" data-servicio="Crunchyroll" data-plan="Mega Fan" data-precio="19900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Crunchyroll</div>
                        <div class="product-card__pts">Mega Fan</div>
                        <div class="product-card__label">Anime 4K · Sin anuncios · 4 pantallas</div>
                        <div class="product-card__price">19.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

            <!-- STAR+ -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=starplus.com&sz=32" alt="Star+" style="width:24px;height:24px;border-radius:4px;">
                    <span>Star+</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="5" data-servicio="Star+" data-plan="Estándar" data-precio="19900">
                        <div class="product-card__platform">Star+</div>
                        <div class="product-card__pts">Estándar</div>
                        <div class="product-card__label">Series · Deportes · Full HD</div>
                        <div class="product-card__price">19.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="6" data-servicio="Star+" data-plan="Combo+" data-precio="29900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Star+</div>
                        <div class="product-card__pts">Combo+</div>
                        <div class="product-card__label">Star+ y Disney+ · 4K · 4 pantallas</div>
                        <div class="product-card__price">29.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

        </section>

        <!-- CHECKOUT -->
        <aside class="checkout-panel" id="checkoutPanel">
            <div class="checkout-summary">
                <div class="checkout-product-name" id="checkoutName">🛒 Amazon Prime — Mensual</div>
                <div class="checkout-row">
                    <span class="checkout-label">Tipo</span>
                    <span class="checkout-delivery">Suscripción</span>
                </div>
                <div class="checkout-row">
                    <span class="checkout-label">Cobro</span>
                    <span class="checkout-delivery">Primer mes + tokenización</span>
                </div>
                <div class="checkout-divider"></div>
                <div class="checkout-row checkout-total-row">
                    <span class="checkout-label">Total</span>
                    <div class="checkout-pricing">
                        <span class="checkout-final-price" id="checkoutPrice">9.900 COP</span>
                    </div>
                </div>
                <div class="sub-info">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Tu tarjeta será tokenizada de forma segura para futuros cobros. El primer mes se cobra al suscribirse.</span>
                </div>
                <button class="btn-buy" id="btnBuy">
                    <span>Suscribirse ahora</span>
                    <span class="btn-arrow">→</span>
                </button>
                <!-- <div class="trust-badges">
                    <div class="trust-item">🛡️ <span>Garantía de reembolso · P2P</span></div>
                    <div class="trust-item">🔐 <span>Tarjeta tokenizada de forma segura</span></div>
                    <div class="trust-item">💬 <span>Asistencia en directo 24/7</span></div>
                </div> -->
            </div>

            <div class="delivery-instructions">
                <p style="font-family:'Barlow Condensed',sans-serif;font-size:0.78rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--pt-text-sec);margin-bottom:0.75rem;">Instrucciones</p>
                <div class="instruction-text" id="instructionText">
                    Amazon Prime® | Plan Mensual 🛒<br>
                    <span>🌐</span> Acceso inmediato tras el pago<br>
                    <span>🔐</span> Tarjeta guardada para futuros cobros
                </div>
                <button class="btn-instructions">Ver todas las instrucciones ▾</button>
            </div>

            <div class="vendor-box">
                <p style="font-family:'Barlow Condensed',sans-serif;font-size:0.78rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--pt-text-sec);margin-bottom:0.5rem;">Designer</p>
                <div class="vendor-info">
                    <div class="vendor-avatar">JM</div>
                    <div>
                        <div class="vendor-name">Jair ✅</div>
                        <div class="vendor-rating">👍 2026 · <a href="#" style="color:#22c55e;">Evertec Placetopay SAS</a></div>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <script>
    (function() {
        const products = {
            1: { name: '🛒 Amazon Prime — Mensual', servicio: 'Amazon Prime',  plan: 'Mensual',  precio: 9900  },
            2: { name: '🛒 Amazon Prime — Anual',   servicio: 'Amazon Prime',  plan: 'Anual',    precio: 89900 },
            3: { name: '🍥 Crunchyroll — Fan',       servicio: 'Crunchyroll',   plan: 'Fan',      precio: 12900 },
            4: { name: '🍥 Crunchyroll — Mega Fan',  servicio: 'Crunchyroll',   plan: 'Mega Fan', precio: 19900 },
            5: { name: '⭐ Star+ — Estándar',        servicio: 'Star+',         plan: 'Estándar', precio: 19900 },
            6: { name: '⭐ Star+ — Combo+',          servicio: 'Star+',         plan: 'Combo+',   precio: 29900 },
        };

        function updateCheckout(id) {
            const p = products[id];
            if (!p) return;
            document.getElementById('checkoutName').textContent  = p.name;
            document.getElementById('checkoutPrice').textContent = p.precio.toLocaleString('es-CO') + ' COP';
            document.getElementById('instructionText').innerHTML =
                p.servicio + '\u00ae | Plan ' + p.plan + '<br>' +
                '<span>\uD83C\uDF10</span> Acceso inmediato tras el pago<br>' +
                '<span>\uD83D\uDD10</span> Tarjeta guardada para futuros cobros';
        }

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

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../php/crear_suscription.php';

            [['servicio', selectedCard.getAttribute('data-servicio')],
             ['plan',     selectedCard.getAttribute('data-plan')],
             ['precio',   selectedCard.getAttribute('data-precio')],
             ['usuario_id', usuarioId]].forEach(function(pair) {
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