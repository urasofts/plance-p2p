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
    <title>Streaming — Suscripciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
    
</head>
<style>
    /* ════════════════════════════════════════
    STREAMING — Dark Theme · Púrpura/Rojo
    ════════════════════════════════════════ */

    :root {
    --bg-base:        var(--pt-bg-base);
    --bg-surface:     var(--pt-bg-surface);
    --bg-card:        var(--pt-navbar);
    --bg-card-hover:  var(--pt-hover);
    --bg-selected:    rgba(168, 85, 247, 0.1);
    --border:         var(--pt-border);
    --accent:         #a855f7;
    --accent-glow:    rgba(168, 85, 247, 0.25);
    --accent-dark:    #7c3aed;
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

    /* ── GAME BANNER ── */
    .game-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 2rem;
    background: var(--pt-th2);
    border-bottom: 1px solid var(--pt-border);
    gap: 1rem;
    }

    .game-banner__tag {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-display);
    font-weight: 700;
    font-size: 1rem;
    letter-spacing: 0.04em;
    color: var(--text-primary);
    }

    /* ── MAIN LAYOUT ── */
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
    font-family: var(--font-display);
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
    }

    /* ── PLATAFORMA HEADER ── */
    .platform-header {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
    }

    .platform-header img {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    object-fit: contain;
    }

    .platform-header span {
    font-family: var(--font-display);
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    color: var(--text-primary);
    }

    /* ── PRODUCTS GRID ── */
    .products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.65rem; 
    }

    .product-card {
    position: relative;
    background: var(--bg-card);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 1rem 0.85rem 0.9rem;
    cursor: pointer;
    transition: all 0.18s ease;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    overflow: hidden;
    }

    .product-card:hover {
    background: var(--pt-boxitem);
    border-color: rgba(168, 85, 247, 0.4);
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
    position: absolute;
    top: 0.5rem;
    right: 0.55rem;
    width: 18px;
    height: 18px;
    background: var(--accent);
    border-radius: 50%;
    color: #0d0e10;
    font-size: 0.65rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    line-height: 18px;
    text-align: center;
    }

    .badge-popular {
    position: absolute;
    top: -1px;
    left: -1px;
    background: var(--accent);
    color: #0d0e10;
    font-family: var(--font-body);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    padding: 0.15rem 0.5rem;
    border-radius: var(--radius-sm) 0 var(--radius-sm) 0;
    }

    .product-card__platform {
    font-size: 0.7rem;
    /*  */
    font-weight: 600;
    color: var(--accent);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.1rem;
    }

    .product-card__pts {
    font-family: var(--font-body);
    /* line-height: 1.1; */
    /*  */
    font-size: 1.2rem;
    /*  */
    color: var(--text-primary);
    font-weight: 800;
    }

    .product-card__label {
    font-size: 0.72rem;
    color: var(--text-secondary);
    margin-bottom: 0.3rem;
    }

    .product-card__price {
    font-family: var(--font-body);
    font-size: 1rem;
    font-weight: 700;
    color: var(--accent);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
    margin-top: auto;
    }

    /* ── CHECKOUT PANEL ── */
    .checkout-panel {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: sticky;
    top: 16px;
    }

    .checkout-summary,
    .delivery-instructions,
    .vendor-box {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.2rem 1.3rem;
    }

    .checkout-product-name {
    font-family: var(--font-body);
    font-size: 1.20rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
    letter-spacing: 0.02em;
    line-height: 1.2;
    }

    .checkout-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.65rem; }
    .checkout-label { font-size: 0.85rem; color: var(--text-secondary); }
    .checkout-delivery { font-size: 0.85rem; font-weight: 600; color: var(--text-primary); }
    .checkout-divider { height: 1px; background: var(--border); margin: 0.8rem 0; }
    .checkout-total-row { align-items: flex-end; }

    .checkout-pricing { text-align: right; display: flex; flex-direction: column; gap: 0.15rem; }

    .checkout-final-price {
    font-family: var(--font-body);
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    }

    .btn-buy {
    width: 100%;
    margin-top: 1rem;
    padding: 0.85rem 1.2rem;
    background: var(--accent);
    border: none;
    border-radius: var(--radius-md);
    color: #0a0a0b;
    font-family: var(--font-body);
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    transition: all 0.18s ease;
    position: relative;
    overflow: hidden;
    }

    .btn-buy::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, transparent, rgba(255,255,255,0.12), transparent);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
    }

    .btn-buy:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }
    .btn-buy:hover::before { transform: translateX(100%); }
    .btn-buy:active { transform: translateY(0); }
    .btn-arrow { font-size: 1.1rem; transition: transform 0.2s; }
    .btn-buy:hover .btn-arrow { transform: translateX(4px); }

    .trust-badges { margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .trust-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--text-secondary); }

    .card-required-notice {
        display: flex; gap: 0.5rem; align-items: flex-start;
        background: rgba(240,180,41,0.08);
        border: 1px solid rgba(240,180,41,0.25);
        border-radius: 8px; padding: 0.65rem 0.8rem;
        margin: 0.8rem 0; font-size: 0.78rem;
        color: #fbbf24; line-height: 1.5;
    }
    .card-required-notice i { font-size: 0.95rem; margin-top: 0.1rem; flex-shrink: 0; }
    .card-required-notice strong { color: #fde68a; }

    .instruction-text { font-size: 0.83rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 0.75rem; }

    .btn-instructions {
    background: none;
    border: 1px solid var(--border);
    color: var(--text-secondary);
    font-size: 0.82rem;
    padding: 0.35rem 0.8rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all var(--transition);
    font-family: var(--font-body);
    }
    .btn-instructions:hover { border-color: var(--accent); color: var(--text-primary); }

    .vendor-info { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }

    .vendor-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a855f7, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 0.85rem;
    color: #fff;
    flex-shrink: 0;
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

    <!-- NAVBAR -->
    <?php
        $nav_back_url  = "suscripciones.php";
        $nav_back_text = "Atras";
        $nav_base      = "../../";
        require_once '../../php/navbar.php';
    ?>

    <!-- GAME BANNER -->
    <div class="game-banner">
        <div class="game-banner__tag">
            <i class="bi bi-tv-fill" style="color: rgba(174, 0, 255, 0.96);"></i> Suscripciones Streaming
        </div>
        <!-- Correo del usuario (obligatorio para invitados) -->
        <div class="banner-correo" style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="max-width:1200px;padding: 5px 10px; border-radius: 5px; font-weight: bold; margin: 0px;">
                <input type="email" id="usuarioIdInput" placeholder="Ingresa tu correo electrónico para continuar"
                    value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>"
                    style="width:100%;max-width:340px;background:var(--pt-bg-card);border:1.5px solid var(--pt-border);border-radius:8px;color:var(--pt-text);font-family:inherit;font-size:0.85rem;padding:0.5rem 0.8rem;outline:none;">
            </div>
        </div>
    </div>


    <!-- MAIN LAYOUT -->
    <main class="shop-layout">

        <!-- LEFT: Products Panel -->
        <section class="products-panel">

            <!-- NETFLIX -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=netflix.com&sz=32" alt="Netflix">
                    <span>Netflix</span>
                </div>
                <div class="products-grid">

                    <div class="product-card" data-id="1" data-plataforma="Netflix" data-plan="Estándar con Anuncios" data-precio="17900">
                        <div class="product-card__platform">Netflix</div>
                        <div class="product-card__pts">Estándar con Anuncios</div>
                        <div class="product-card__label">1 mes · HD · 2 pantallas</div>
                        <div class="product-card__price">17.900 COP</div>
                    </div>

                    <div class="product-card popular-card" data-id="2" data-plataforma="Netflix" data-plan="Estándar" data-precio="26900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Netflix</div>
                        <div class="product-card__pts">Estándar</div>
                        <div class="product-card__label">1 mes · Full HD · 2 pantallas</div>
                        <div class="product-card__price">26.900 COP</div>
                    </div>

                    <div class="product-card" data-id="3" data-plataforma="Netflix" data-plan="Premium" data-precio="36900">
                        <div class="product-card__platform">Netflix</div>
                        <div class="product-card__pts">Premium</div>
                        <div class="product-card__label">1 mes · 4K · 4 pantallas</div>
                        <div class="product-card__price">36.900 COP</div>
                    </div>

                </div>
            </div>

            <!-- HBO MAX -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=max.com&sz=32" alt="HBO Max">
                    <span>HBO Max</span>
                </div>
                <div class="products-grid">

                    <div class="product-card" data-id="4" data-plataforma="HBO Max" data-plan="Básico" data-precio="19900">
                        <div class="product-card__platform">HBO Max</div>
                        <div class="product-card__pts">Básico</div>
                        <div class="product-card__label">1 mes · HD · 2 pantallas</div>
                        <div class="product-card__price">19.900 COP</div>
                    </div>

                    <div class="product-card" data-id="5" data-plataforma="HBO Max" data-plan="Estándar" data-precio="29900">
                        <div class="product-card__platform">HBO Max</div>
                        <div class="product-card__pts">Estándar</div>
                        <div class="product-card__label">1 mes · Full HD · 3 pantallas</div>
                        <div class="product-card__price">29.900 COP</div>
                    </div>

                    <div class="product-card" data-id="6" data-plataforma="HBO Max" data-plan="Ultimate" data-precio="39900">
                        <div class="product-card__platform">HBO Max</div>
                        <div class="product-card__pts">Ultimate</div>
                        <div class="product-card__label">1 mes · 4K · 4 pantallas</div>
                        <div class="product-card__price">39.900 COP</div>
                    </div>

                </div>
            </div>

            <!-- DISNEY+ -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=disneyplus.com&sz=32" alt="Disney+">
                    <span>Disney+</span>
                </div>
                <div class="products-grid">

                    <div class="product-card" data-id="7" data-plataforma="Disney+" data-plan="Estándar" data-precio="16900">
                        <div class="product-card__platform">Disney+</div>
                        <div class="product-card__pts">Estándar</div>
                        <div class="product-card__label">1 mes · Full HD · 2 pantallas</div>
                        <div class="product-card__price">16.900 COP</div>
                    </div>

                    <div class="product-card popular-card" data-id="8" data-plataforma="Disney+" data-plan="Premium" data-precio="28900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Disney+</div>
                        <div class="product-card__pts">Premium</div>
                        <div class="product-card__label">1 mes · 4K · 4 pantallas</div>
                        <div class="product-card__price">28.900 COP</div>
                    </div>

                    <div class="product-card" data-id="9" data-plataforma="Disney+" data-plan="Duo Premium" data-precio="38900">
                        <div class="product-card__platform">Disney+</div>
                        <div class="product-card__pts">Duo Premium</div>
                        <div class="product-card__label">1 mes · 4K · 4 pantallas</div>
                        <div class="product-card__price">38.900 COP</div>
                    </div>

                </div>
            </div>

        </section>

        <!-- RIGHT: Checkout Panel -->
        <aside class="checkout-panel" id="checkoutPanel">

            <div class="checkout-summary">
                <div class="checkout-product-name" id="checkoutName">📺 Netflix — Estándar</div>

                <div class="checkout-row">
                    <span class="checkout-label">Duración</span>
                    <span class="checkout-delivery">1 mes</span>
                </div>
                <div class="checkout-row">
                    <span class="checkout-label">Renovación</span>
                    <span class="checkout-delivery">Manual</span>
                </div>

                <div class="checkout-divider"></div>

                <div class="checkout-row checkout-total-row">
                    <span class="checkout-label">Total</span>
                    <div class="checkout-pricing">
                        <span class="checkout-final-price" id="checkoutPrice">26.900 COP</span>
                    </div>
                </div>

                <div class="card-required-notice">
                    <i class="bi bi-info-circle-fill"></i>
                    <span><strong>Solo tarjeta.</strong> Esta suscripción guarda tu método de pago para renovarla automáticamente — PSE no permite esto, por eso en la pasarela solo verás la opción de tarjeta (Visa / Mastercard).</span>
                </div>

                <button class="btn-buy" id="btnBuy">
                    <span>Suscribirse ahora</span>
                    <span class="btn-arrow">→</span>
                </button>

                <!-- <div class="trust-badges">
                    <div class="trust-item">🛡️ <span>Garantía de reembolso · P2P</span></div>
                    <div class="trust-item">⚡ <span>Pago rápido · Apple Pay / G Pay</span></div>
                    <div class="trust-item">💬 <span>Asistencia en directo 24/7</span></div>
                </div> -->
            </div>

            <div class="delivery-instructions">
                <p class="section-label">Instrucciones</p>
                <div class="instruction-text" id="instructionText">
                    Netflix® | Plan Estándar 📺<br>
                    <span>🌐</span> Acceso inmediato tras el pago<br>
                    <span>⛔</span> IMPORTANT NOTE BEFORE PURCHASE
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

    <!-- JS -->
    <script>
    (function() {

        const products = {
            1: { name: '📺 Netflix — Est. con Anuncios', plataforma: 'Netflix',  plan: 'Estándar con Anuncios', price: '17.900 COP', precio: 17900 },
            2: { name: '📺 Netflix — Estándar',          plataforma: 'Netflix',  plan: 'Estándar',              price: '26.900 COP', precio: 26900 },
            3: { name: '📺 Netflix — Premium',           plataforma: 'Netflix',  plan: 'Premium',               price: '36.900 COP', precio: 36900 },
            4: { name: '🎬 HBO Max — Básico',            plataforma: 'HBO Max',  plan: 'Básico',                price: '19.900 COP', precio: 19900 },
            5: { name: '🎬 HBO Max — Estándar',          plataforma: 'HBO Max',  plan: 'Estándar',              price: '29.900 COP', precio: 29900 },
            6: { name: '🎬 HBO Max — Ultimate',          plataforma: 'HBO Max',  plan: 'Ultimate',              price: '39.900 COP', precio: 39900 },
            7: { name: '✨ Disney+ — Estándar',          plataforma: 'Disney+',  plan: 'Estándar',              price: '16.900 COP', precio: 16900 },
            8: { name: '✨ Disney+ — Premium',           plataforma: 'Disney+',  plan: 'Premium',               price: '28.900 COP', precio: 28900 },
            9: { name: '✨ Disney+ — Duo Premium',       plataforma: 'Disney+',  plan: 'Duo Premium',           price: '38.900 COP', precio: 38900 },
        };

        function updateCheckout(id) {
            const p = products[id];
            if (!p) return;

            document.getElementById('checkoutName').textContent  = p.name;
            document.getElementById('checkoutPrice').textContent = p.price;

            document.getElementById('instructionText').innerHTML =
                p.plataforma + '\u00ae | Plan ' + p.plan + ' \uD83D\uDCFA<br>' +
                '<span>\uD83C\uDF10</span> Acceso inmediato tras el pago<br>' +
                '<span>\u26D4</span> IMPORTANT NOTE BEFORE PURCHASE';
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

            // Selección por defecto: Netflix Estándar
            var def = document.querySelector('.product-card[data-id="2"]');
            if (def) { def.classList.add('selected'); updateCheckout(2); }
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
            if (!usuarioId) {
                alert('⚠️ Por favor ingresa tu correo antes de continuar.');
                document.getElementById('usuarioIdInput').focus();
                return;
            }

            // Obtener datos de la tarjeta seleccionada
            var selectedCard = document.querySelector('.product-card.selected');
            if (!selectedCard) { alert('⚠️ Selecciona un plan primero.'); return; }

            var plataforma = selectedCard.getAttribute('data-plataforma');
            var plan       = selectedCard.getAttribute('data-plan');
            var precio     = selectedCard.getAttribute('data-precio');

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../php/crear_subs.php';

            [['plataforma', plataforma], ['plan', plan], ['precio', precio], ['usuario_id', usuarioId]].forEach(function(pair) {
                var input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = pair[0];
                input.value = pair[1];
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });

    })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validaciones.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>