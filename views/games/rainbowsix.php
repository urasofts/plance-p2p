<?php
session_start();
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) { header("Location: ../../index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rainbow Six Siege Mobile — Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    :root {
        --bg-base:#0d0e10; --bg-surface:#16181c; --bg-card:#1e2128;
        --bg-card-hover:#252830; --bg-selected:#0a0f1a; --border:#2e3038;
        --accent:#f0b429; --accent-glow:rgba(240,180,41,0.25); --accent-dark:#c99010;
        --accent2:#3b82f6; --accent2-glow:rgba(59,130,246,0.2);
        --text-primary:#f0f1f3; --text-secondary:#8a8d96; --text-muted:#555860;
        --font-display:'Calibri',sans-serif; --font-body:'Calibri',sans-serif;
        --radius-md:10px; --radius-lg:14px;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{background-color:var(--bg-base);color:var(--text-primary);font-family:var(--font-body);min-height:100vh;-webkit-font-smoothing:antialiased;}
    .navbar{background-color:#0f0f0fa9!important;backdrop-filter:blur(8px);border-bottom:1px solid var(--pt-border);}

    .game-banner{display:flex;align-items:center;justify-content:space-between;padding:0.6rem 2rem; background: var(--pt-th2); border-bottom:1px solid var(--pt-border);gap:1rem;flex-wrap:wrap;}
    .game-banner__tag{display:flex;align-items:center; gap:0.5rem;font-family:var(--font-display);font-weight:700;font-size:1rem;letter-spacing:0.04em;}
    .wc-badge{background:rgba(240,180,41,0.15);color:var(--accent);font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}
    .mixto-badge{background:rgba(59,130,246,0.15);color:var(--accent2);font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}

    .shop-layout{display:grid;grid-template-columns:1fr 370px;gap:1.5rem;max-width:1200px;margin:1.5rem auto;padding:0 1.5rem 3rem;align-items:start;}

    .section-label{font-family:var(--font-display);font-size:0.78rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:0.75rem;}

    /* GRID PLATINUM */
    .products-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0.65rem;margin-bottom:1.5rem;}

    .product-card{position:relative; background:var(--pt-navbar);border:1.5px solid var(--pt-border);border-radius:var(--radius-md);padding:1rem 0.85rem 0.9rem;cursor:pointer;transition:all 0.18s ease;display:flex;flex-direction:row;align-items:center;gap:0.75rem;overflow:hidden;user-select:none;}
    .product-card:hover{background:var(--pt-boxitem);border-color:rgba(240,180,41,0.4);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.35);}
    .product-card.selected{background:var(--pt-border);border-color:var(--accent);box-shadow:0 0 0 1px var(--accent),0 4px 24px var(--accent-glow);}
    .product-card.in-cart{border-color:var(--accent2);background:rgba(59,130,246,0.05);}
    .product-card.in-cart::after{content:'✔';position:absolute;top:0.5rem;right:0.55rem;width:18px;height:18px;background:var(--accent2);border-radius:50%;color:#fff;font-size:0.65rem;display:flex;align-items:center;justify-content:center;font-weight:900;line-height:18px;text-align:center;}
    .badge-popular{position:absolute;top:-1px;left:-1px;background:var(--accent);color:#0d0e10;font-family:var(--font-display);font-size:0.68rem;font-weight:800;letter-spacing:0.05em;padding:0.15rem 0.5rem;border-radius:6px 0 6px 0;}
    .product-card__icon{width:44px;height:44px;object-fit:contain;flex-shrink:0;}
    .product-card__info{display:flex;flex-direction:column;gap:0.1rem;min-width:0;}
    .product-card__pts{font-family:var(--font-display);font-size:1.15rem;font-weight:800;color:var(--pt-text-sec);line-height:1.1;}
    .product-card__label{font-size:0.72rem;color:var(--pt-text);margin-bottom:0.2rem;}
    .product-card__price{font-family:var(--font-display);font-size:1rem;font-weight:700;color:var(--accent);}

    /* PASES */
    .pases-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:0.65rem;margin-bottom:1.5rem;}
    .pase-card{position:relative; background: var(--pt-boxitem);  border:1.5px solid rgba(59,130,246,0.2);border-radius:var(--radius-md);padding:1.2rem;cursor:pointer;transition:all 0.18s ease;overflow:hidden;}
    .pase-card:hover{ border-color:rgba(59,130,246,0.5);transform:translateY(-2px);box-shadow:0 6px 20px rgba(59,130,246,0.15);}
    .pase-card.selected{background:var(--pt-border); border-color:var(--accent2);box-shadow:0 0 0 1px var(--accent2),0 4px 24px var(--accent2-glow);}
    .pase-card.in-cart{border-color:var(--accent);background:rgba(240,180,41,0.05);}
    .pase-card.in-cart::after{content:'✔';position:absolute;top:0.5rem;right:0.55rem;width:18px;height:18px;background:var(--accent);border-radius:50%;color:#0d0e10;font-size:0.65rem;display:flex;align-items:center;justify-content:center;font-weight:900;line-height:18px;text-align:center;}
    .pase-tag{display:inline-block;background:rgba(59,130,246,0.15);color:var(--accent2);font-size:0.68rem;font-weight:700;padding:0.15rem 0.5rem;border-radius:4px;margin-bottom:0.5rem;font-family:var(--font-display);letter-spacing:0.05em;}
    .pase-head{display:flex;align-items:center;gap:0.6rem;margin-bottom:0.3rem;}
    .pase-icon{width:42px;height:42px;object-fit:contain;flex-shrink:0;}
    .pase-name{font-family:var(--font-display);font-size:1.1rem;font-weight:800;}
    .pase-desc{font-size:0.78rem;color:var(--text-secondary);line-height:1.5;margin-bottom:0.6rem;}
    .pase-price{font-family:var(--font-display);font-size:1.1rem;font-weight:800; color:var(--accent2);}

    /* CHECKOUT */
    .checkout-panel{display:flex;flex-direction:column;gap:1rem;position:sticky;top:16px;}
    .checkout-box{background: var(--pt-navbar); border:1px solid var(--pt-border);border-radius:var(--radius-lg);padding:1.3rem;}
    .section-label-sm{font-family:var(--font-display);font-size:0.73rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:0.5rem;display:block;}

    /* Resumen carrito */
    .cart-items{margin-bottom:0.8rem;}
    .cart-empty{text-align:center;padding:1.2rem;color:var(--text-muted);font-size:0.85rem;}
    .cart-item{display:flex;justify-content:space-between;align-items:center;padding:0.45rem 0;border-bottom:1px solid var(--pt-border);font-size:0.85rem;}
    .cart-item:last-child{border-bottom:none;}
    .cart-item-name{color:var(--pt-text);font-weight:600;}
    .cart-item-price{color:var(--accent);font-family:var(--font-display);font-weight:700;}

    .total-row{display:flex;justify-content:space-between;align-items:center;padding:0.6rem 0;border-top:1px solid var(--pt-border);margin-top:0.3rem;}
    .total-label{font-family:var(--font-display);font-size:0.85rem;font-weight:700;color:var(--pt-text-sec);text-transform:uppercase;letter-spacing:0.05em;}
    .total-price{font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:var(--pt-text);}

    /* Checkbox multi-producto y pago parcial */
    .check-wrap{display:flex;align-items:flex-start;gap:0.5rem;padding:0.65rem 0.8rem;border-radius:8px;cursor:pointer;transition:background 0.2s;margin-bottom:0.5rem;}
    .check-wrap.multi{background:rgba(59,130,246,0.06);border:1.5px solid rgba(59,130,246,0.2);}
    .check-wrap.parcial{background:rgba(240,180,41,0.06);border:1.5px solid rgba(240,180,41,0.2);}
    .check-wrap input[type="checkbox"]{width:16px;height:16px;flex-shrink:0;margin-top:2px;cursor:pointer;}
    .check-wrap.multi  input{accent-color:var(--accent2);}
    .check-wrap.parcial input{accent-color:var(--accent);}
    .check-label{font-size:0.81rem;line-height:1.4;}
    .check-wrap.multi   .check-label{color:#93c5fd;}
    .check-wrap.parcial .check-label{color:#fbbf24;}
    .check-label strong{display:block;margin-bottom:0.1rem;}

    /* Pago parcial slider */
    .parcial-panel{display:none;background:rgba(240,180,41,0.05);border:1px solid rgba(240,180,41,0.2);border-radius:8px;padding:0.8rem;margin-bottom:0.6rem;}
    .parcial-panel.show{display:block;}
    .parcial-slider{width:100%;accent-color:var(--accent);margin:0.5rem 0;}
    .parcial-amounts{display:flex;justify-content:space-between;font-size:0.78rem;color:var(--text-secondary);margin-bottom:0.3rem;}
    .parcial-now{font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--accent);text-align:center;}
    .parcial-rest{font-size:0.75rem;color:var(--text-muted);text-align:center;margin-top:0.2rem;}

    .field-group{margin-bottom:0.65rem;}
    .field-label{font-size:0.73rem;font-weight:600;color:var(--pt-text); margin-bottom:0.25rem; display:block;}
    .field-input{width:100%;background:var(--pt-border); border:0.5px solid var(--pt-border);  border-radius:8px; color:var(--pt-text-sec); font-family:var(--font-body); font-size:0.83rem;padding:0.4rem 0.7rem;}
    .field-input:focus{border-color:var(--accent);}
    .field-input::placeholder{color:var(--text-muted);}

    .btn-pagar{width:100%;margin-top:0.8rem;padding:0.85rem;background:var(--accent);border:none;border-radius:var(--radius-md);color:#0a0a0b;font-family:var(--font-display);font-size:1.05rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;cursor:pointer;transition:all 0.18s ease;display:flex;align-items:center;justify-content:center;gap:0.5rem;}
    .btn-pagar:hover{background:var(--accent-dark);transform:translateY(-1px);box-shadow:0 6px 20px var(--accent-glow);}
    .security-note{display:flex;align-items:center;gap:0.4rem;font-size:0.73rem;color:var(--text-muted);margin-top:0.5rem;justify-content:center;}

    @keyframes fadeSlideIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
    .products-panel{animation:fadeSlideIn 0.4s ease both;}
    .checkout-panel{animation:fadeSlideIn 0.4s 0.1s ease both;}
    @media(max-width:900px){.shop-layout{grid-template-columns:1fr;}.checkout-panel{position:static;}.products-grid,.pases-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:600px){.products-grid{grid-template-columns:repeat(2,1fr);}.pases-grid{grid-template-columns:1fr;}.game-banner{flex-direction:column;align-items:flex-start;}}
</style>
<body>
    <?php
    $nav_back_url  = "juegos.php";
    $nav_back_text = "Atras";
    $nav_base      = "../../";
    $nav_back_text_cod = "cod.php";
    require_once '../../php/navbar.php';
    ?>

    <div class="game-banner">
        <div class="game-banner__tag">
            🎯 Rainbow Six Siege Mobile — Tienda
            <span class="wc-badge">🖥️ Web Checkout</span>
            <span class="mixto-badge">🔀 Pago Mixto</span>
        </div>
    </div>

    <main class="shop-layout">
        <section class="products-panel">

            <!-- PLATINUM -->
            <p class="section-label">💎 Platinum — Moneda del juego</p>
            <div class="products-grid">
                <div class="product-card" data-id="p1" data-nombre="60 Platinum" data-precio="4900">
                    <img class="product-card__icon" src="https://cdn.donatov.net/cover/pack-14533-1771960014.webp" alt="">
                    <div class="product-card__info">
                        <div class="product-card__pts">60 Platinum</div>
                        <div class="product-card__label">Rainbow Six Siege Mobile</div>
                        <div class="product-card__price">4.900 COP</div>
                    </div>
                </div>
                <div class="product-card" data-id="p2" data-nombre="120 Platinum" data-precio="9900">
                    <img class="product-card__icon" src="https://cdn.donatov.net/cover/pack-14533-1771960014.webp" alt="">
                    <div class="product-card__info">
                        <div class="product-card__pts">120 Platinum</div>
                        <div class="product-card__label">Rainbow Six Siege Mobile</div>
                        <div class="product-card__price">9.900 COP</div>
                    </div>
                </div>
                <div class="product-card popular-card" data-id="p3" data-nombre="300 Platinum" data-precio="24900">
                    <div class="badge-popular">★ Popular</div>
                    <img class="product-card__icon" src="https://cdn.donatov.net/cover/pack-14533-1771960014.webp" alt="">
                    <div class="product-card__info">
                        <div class="product-card__pts">300 Platinum</div>
                        <div class="product-card__label">Rainbow Six Siege Mobile</div>
                        <div class="product-card__price">24.900 COP</div>
                    </div>
                </div>
                <div class="product-card" data-id="p4" data-nombre="600 Platinum" data-precio="49900">
                    <img class="product-card__icon" src="https://cdn.donatov.net/cover/pack-14533-1771960014.webp" alt="">
                    <div class="product-card__info">
                        <div class="product-card__pts">600 Platinum</div>
                        <div class="product-card__label">Rainbow Six Siege Mobile</div>
                        <div class="product-card__price">49.900 COP</div>
                    </div>
                </div>

                <div class="product-card popular-card" data-id="p5" data-nombre="1200 Platinum" data-precio="99900">
                    <div class="badge-popular">Mejor valor</div>
                    <img class="product-card__icon" src="https://cdn.donatov.net/cover/pack-14533-1771960014.webp" alt="">
                    <div class="product-card__info">
                        <div class="product-card__pts">1200 Platinum</div>
                        <div class="product-card__label">Rainbow Six Siege Mobile</div>
                        <div class="product-card__price">99.900 COP</div>
                    </div>
                </div>
                <div class="product-card" data-id="p6" data-nombre="2400 Platinum" data-precio="189900">
                    <img class="product-card__icon" src="https://cdn.donatov.net/cover/pack-14533-1771960014.webp" alt="">
                    <div class="product-card__info">
                        <div class="product-card__pts">2400 Platinum</div>
                        <div class="product-card__label">Rainbow Six Siege Mobile</div>
                        <div class="product-card__price">189.900 COP</div>
                    </div>
                </div>
            </div>

            <!-- PASES -->
            <p class="section-label">🎖️ Pases de batalla</p>
            <div class="pases-grid">
                <div class="pase-card" data-id="b1" data-nombre="Pase Premium Fantasma Gris" data-precio="39900">
                    <span class="pase-tag">PASE DE BATALLA</span>
                    <div class="pase-head">
                        <img class="pase-icon" src="https://cdn1.codashop.com/images/7930_ce805d3c-62c8-4fd5-9c2c-80f230892257_79233c04adf11386399b9827c345a658_image/0eb60010fc1c69b1b9ef65d53829b521.webp" alt="">
                        <div class="pase-name">Pase Premium<br>Fantasma Gris</div>
                    </div>
                    <div class="pase-desc">Acceso al pase de temporada con recompensas exclusivas del operador Fantasma Gris.</div>
                    <div class="pase-price">39.900 COP</div>
                </div>
                <div class="pase-card" data-id="b2" data-nombre="Pase Elite Fantasma Gris" data-precio="79900">
                    <span class="pase-tag">PASE ELITE</span>
                    <div class="pase-head">
                        <img class="pase-icon" src="https://cdn1.codashop.com/images/7930_ce805d3c-62c8-4fd5-9c2c-80f230892257_79233c04adf11386399b9827c345a658_image/d7406d15935e21ab8ecb5ad05ee788d2.webp" alt="">
                        <div class="pase-name">Pase Elite<br>Fantasma Gris</div>
                    </div>
                    <div class="pase-desc">Incluye todo el Pase Premium más contenido exclusivo elite y recompensas adicionales.</div>
                    <div class="pase-price">79.900 COP</div>
                </div>
            </div>

        </section>

        <!-- CHECKOUT -->
        <aside class="checkout-panel">
            <div class="checkout-box">

                <!-- Multi-producto checkbox -->
                <label class="check-wrap multi" style="margin-bottom:0.8rem;">
                    <input type="checkbox" id="multiCheck" onchange="toggleMulti()">
                    <span class="check-label">
                        <strong>🛒 Selección múltiple</strong>
                        Agrega varios productos al carrito y págalos juntos.
                    </span>
                </label>

                <!-- Carrito / resumen -->
                <span class="section-label-sm">Resumen</span>
                <div class="cart-items" id="cartItems">
                    <div class="cart-empty" id="cartEmpty">Selecciona un producto para comenzar</div>
                </div>
                <div class="total-row">
                    <span class="total-label">Total</span>
                    <span class="total-price" id="totalPrice">$0 COP</span>
                </div>

                <!-- Pago parcial checkbox 
                <label class="check-wrap parcial">
                    <input type="checkbox" id="parcialCheck" onchange="toggleParcial()">
                    <span class="check-label">
                        <strong>🔀 Pago parcial (Mixto)</strong>
                        Paga solo una parte ahora y el resto después.
                    </span>
                </label>-->
                

                <!-- Panel pago parcial -->
                <div class="parcial-panel" id="parcialPanel">
                    <div class="parcial-amounts">
                        <span>Mínimo: <strong id="minAmount">0</strong></span>
                        <span>Total: <strong id="maxAmount">0</strong></span>
                    </div>
                    <input type="range" class="parcial-slider" id="parcialSlider" min="0" max="100" value="50" oninput="updateSlider()">
                    <div class="parcial-now" id="parcialNow">$0 COP</div>
                    <div class="parcial-rest" id="parcialRest">Restante: $0 COP</div>
                </div>

                <div style="height:0.8rem;"></div>
                <span class="section-label-sm">Datos del jugador</span>
                <div class="field-group">
                    <label class="field-label"><i class="bi bi-person-vcard-fill fs-6 text-warning"></i> ID de jugador</label>
                    <input type="text" class="field-input" id="jugadorId" placeholder="Ej: R6M-123456">
                </div>

                <button class="btn-pagar" id="btnPagar" onclick="pagar()">
                    <i class="bi bi-lock-fill"></i> Pagar ahora
                </button>
                <div class="security-note">
                    <i class="bi bi-shield-check"></i>
                    Web Checkout · PlacetoPay · Evertec
                </div>
            </div>
        </aside>
    </main>

    <script>
    (function() {
        let cart      = {};   // { id: { nombre, precio } }
        let multiMode = false;

        function fmt(n) { return '$' + n.toLocaleString('es-CO') + ' COP'; }

        function getTotal() { return Object.values(cart).reduce((s,p)=>s+p.precio,0); }

        function renderCart() {
            const items  = Object.values(cart);
            const total  = getTotal();
            const empty  = document.getElementById('cartEmpty');
            const cont   = document.getElementById('cartItems');
            const tprice = document.getElementById('totalPrice');

            // Limpiar items previos (excepto empty)
            cont.querySelectorAll('.cart-item').forEach(e=>e.remove());

            if (items.length === 0) {
                empty.style.display = 'block';
            } else {
                empty.style.display = 'none';
                items.forEach(function(p) {
                    const row = document.createElement('div');
                    row.className = 'cart-item';
                    row.innerHTML = `<span class="cart-item-name">${p.nombre}</span><span class="cart-item-price">${fmt(p.precio)}</span>`;
                    cont.appendChild(row);
                });
            }

            tprice.textContent = fmt(total);
        }

        window.toggleMulti = function() {
            multiMode = document.getElementById('multiCheck').checked;
            if (!multiMode) {
                // En modo single dejamos solo el primero del carrito
                const keys = Object.keys(cart);
                if (keys.length > 1) {
                    const first = keys[0];
                    const keep  = cart[first];
                    cart = { [first]: keep };
                }
                document.querySelectorAll('.product-card.in-cart, .pase-card.in-cart').forEach(c=>{
                    if (c.getAttribute('data-id') !== Object.keys(cart)[0]) c.classList.remove('in-cart');
                });
            }
            renderCart();
        };

        window.toggleParcial = function() {
            // Función deshabilitada - pago parcial removido
        };

        function toggleCard(card_el) {
            const id     = card_el.getAttribute('data-id');
            const nombre = card_el.getAttribute('data-nombre');
            const precio = parseInt(card_el.getAttribute('data-precio'));

            if (multiMode) {
                if (cart[id]) {
                    delete cart[id];
                    card_el.classList.remove('in-cart');
                } else {
                    cart[id] = { nombre, precio };
                    card_el.classList.add('in-cart');
                }
            } else {
                // Single mode — deseleccionar todo y seleccionar este
                document.querySelectorAll('.product-card, .pase-card').forEach(c=>{
                    c.classList.remove('selected','in-cart');
                });
                cart = {};
                cart[id] = { nombre, precio };
                card_el.classList.add('selected');
            }
            renderCart();
        }

        // Bind cards
        document.querySelectorAll('.product-card, .pase-card').forEach(function(card_el) {
            card_el.addEventListener('click', function() { toggleCard(this); });
        });

        window.pagar = function() {
            const jugadorId = document.getElementById('jugadorId').value.trim();
            if (!jugadorId) { alert('⚠️ Por favor ingresa tu ID de jugador.'); return; }

            const items = Object.values(cart);
            if (items.length === 0) { alert('⚠️ Selecciona al menos un producto.'); return; }

            const total = getTotal();

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../php/crear_pago_mixto.php';

            const productos = items.map(p=>p.nombre).join(' + ');

            [
                ['jugador_id',    jugadorId],
                ['productos',     productos],
                ['total',         total],
                ['monto_parcial', total],
                ['allow_partial', '1'],  // Siempre activar pago mixto
                ['items_json',    JSON.stringify(items)]
            ].forEach(function(pair) {
                const input = document.createElement('input');
                input.type='hidden'; input.name=pair[0]; input.value=pair[1];
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        };

        renderCart();
    })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>