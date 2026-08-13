<?php
session_start();
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) { header("Location: ../../index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Música — API Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    :root {
        --bg-base: var(--pt-bg-base); --bg-surface: var(--pt-bg-surface); --bg-card: var(--pt-navbar);
        --bg-card-hover: var(--pt-hover); --bg-selected: rgba(29, 185, 84, 0.1); --border: var(--pt-border);
        --accent: #1db954; --accent-glow:rgba(29,185,84,0.25); --accent-dark: #17a248;
        --text-primary: var(--pt-text); --text-secondary: var(--pt-text-sec); --text-muted: var(--pt-text-sec);
        --font-display:'Barlow',sans-serif; --font-body:'Barlow',sans-serif;
        --radius-sm:6px; --radius-md:10px; --radius-lg:14px;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{background-color:var(--bg-base);color:var(--text-primary);font-family:var(--font-body);min-height:100vh;-webkit-font-smoothing:antialiased;}
    .navbar{background-color:var(--pt-navbar)!important;backdrop-filter:blur(8px);border-bottom:1px solid var(--border);}

    .security-warning{background:rgba(224,82,82,0.08);border-left:4px solid #e05252;border-radius:0 8px 8px 0;padding:0.9rem 1.2rem;margin:1rem 2rem;display:flex;gap:0.8rem;align-items:flex-start;font-size:0.83rem;color:var(--text-primary);line-height:1.6;}
    .security-warning i{color:#e05252;font-size:1.2rem;flex-shrink:0;margin-top:0.1rem;}
    .security-warning strong{color:#e05252;}
    .security-warning-header{display:flex;align-items:center;justify-content:space-between;width:100%;cursor:pointer;user-select:none;}
    .security-warning-toggle{color:#e05252;font-size:1rem;transition:transform 0.3s ease;margin-left:auto;padding-left:1rem;}
    .security-warning-toggle.collapsed{transform:rotate(-90deg);}
    .security-warning-content{margin-top:0.5rem;overflow:hidden;max-height:500px;transition:max-height 0.3s ease,opacity 0.3s ease;opacity:1;}
    .security-warning-content.collapsed{max-height:0;opacity:0;margin-top:0;}

    .game-banner{display:flex;align-items:center;justify-content:space-between;padding:0.6rem 2rem;background:var(--pt-th2);border-bottom:1px solid var(--border);gap:1rem;}
    .game-banner__tag{display:flex;align-items:center;gap:0.5rem;font-family:var(--font-display);font-weight:700;font-size:1rem;letter-spacing:0.04em;color:var(--text-primary);}
    .gw-badge{background:rgba(29,185,84,0.15);color:var(--accent);font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}
    .sub-badge{background:rgba(29,185,84,0.12);color:var(--accent);font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}

    .shop-layout{display:grid;grid-template-columns:1fr 370px;gap:1.5rem;max-width:1200px;margin:1.5rem auto;padding:0 1.5rem 3rem;align-items:start;}

    .section-block{margin-bottom:1.8rem;}
    .platform-header{display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);}
    .platform-header span{font-family:var(--font-display);font-size:1.1rem;font-weight:800;letter-spacing:0.04em;color:var(--text-primary);}

    .products-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0.65rem;}
    .product-card{position:relative;background:var(--bg-card);border:1.5px solid var(--border);border-radius:var(--radius-md);padding:1rem 0.85rem 0.9rem;cursor:pointer;transition:all 0.18s ease;display:flex;flex-direction:column;gap:0.15rem;overflow:hidden;}
    .product-card:hover{background:var(--bg-card-hover);border-color:rgba(29,185,84,0.4);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.35);}
    .product-card.selected{background:var(--pt-border);border-color:var(--accent);box-shadow:0 0 0 1px var(--accent),0 4px 24px var(--accent-glow);}
    .product-card.selected::after{content:'✔';position:absolute;top:0.5rem;right:0.55rem;width:18px;height:18px;background:var(--accent);border-radius:50%;color:#0d0e10;font-size:0.65rem;display:flex;align-items:center;justify-content:center;font-weight:900;line-height:18px;text-align:center;}
    .badge-popular{position:absolute;top:-1px;left:-1px;background:var(--accent);color:#0d0e10;font-family:var(--font-display);font-size:0.68rem;font-weight:800;letter-spacing:0.05em;padding:0.15rem 0.5rem;border-radius:var(--radius-sm) 0 var(--radius-sm) 0;}
    .product-card__platform{font-size:0.7rem;font-weight:600;color:var(--accent);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.1rem;}
    .product-card__pts{font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--text-primary);line-height:1.1;}
    .product-card__label{font-size:0.72rem;color:var(--text-secondary);margin-bottom:0.3rem;}
    .product-card__price{font-family:var(--font-display);font-size:1rem;font-weight:700;color:var(--accent);display:flex;align-items:center;gap:0.35rem;flex-wrap:wrap;margin-top:auto;}
    .sub-tag{background:rgba(29,185,84,0.12);color:var(--accent);font-size:0.65rem;font-weight:700;padding:0.1rem 0.35rem;border-radius:3px;}

    .checkout-panel{display:flex;flex-direction:column;gap:1rem;position:sticky;top:16px;}
    .checkout-box{background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.2rem 1.3rem;}
    .checkout-product-name{font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--text-primary);margin-bottom:0.6rem;}
    .checkout-price-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;}
    .checkout-price{font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--text-primary);}
    .checkout-divider{height:1px;background:var(--border);margin:0.7rem 0;}

    .token-info{background:rgba(29,185,84,0.08);border:1px solid rgba(29,185,84,0.2);border-radius:8px;padding:0.7rem 1rem;margin-bottom:0.8rem;font-size:0.8rem;color:#86efac;display:flex;gap:0.5rem;align-items:flex-start;}

    .field-group{margin-bottom:0.65rem;}
    .field-label{font-size:0.73rem;font-weight:600;color:var(--text-secondary);margin-bottom:0.25rem;display:block;}
    .field-input{width:100%;background:var(--pt-border);border:1.5px solid var(--border);border-radius:8px;color:var(--text-primary);font-family:var(--font-body);font-size:0.83rem;padding:0.4rem 0.7rem;outline:none;transition:border-color 0.2s;}
    .field-input:focus{border-color:var(--accent);}
    .field-input::placeholder{color:var(--text-muted);}
    .field-row{display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;}
    .section-label-sm{font-family:var(--font-display);font-size:0.73rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:0.5rem;display:block;}

    .btn-pagar{width:100%;margin-top:0.8rem;padding:0.8rem;background:var(--accent);border:none;border-radius:var(--radius-md);color:#0a0a0b;font-family:var(--font-display);font-size:1rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;cursor:pointer;transition:all 0.18s ease;display:flex;align-items:center;justify-content:center;gap:0.5rem;}
    .btn-pagar:hover{background:var(--accent-dark);transform:translateY(-1px);box-shadow:0 6px 20px var(--accent-glow);}

    .sim-mode-wrap{margin-top:0.9rem;}
    .sim-mode-label{font-family:var(--font-display);font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:0.45rem;display:block;}
    .sim-mode-toggle{display:grid;grid-template-columns:1fr 1fr;gap:0.4rem;background:var(--bg-card);border:1.5px solid var(--border);border-radius:var(--radius-md);padding:0.3rem;}
    .sim-mode-opt{border:none;background:transparent;cursor:pointer;padding:0.55rem 0.4rem;border-radius:var(--radius-sm);font-family:var(--font-body);font-size:0.8rem;font-weight:600;color:var(--text-secondary);transition:all 0.18s ease;display:flex;align-items:center;justify-content:center;gap:0.35rem;}
    .sim-mode-opt:hover{background-color: rgba(178, 255, 89, 0.12); color:var(--text-primary);}
    .sim-mode-opt.active{background:rgba(66, 133, 77, 0.12);color:var(--accent);box-shadow:inset 0 0 0 1.5px var(--accent);}
    .sim-mode-hint{font-size:0.72rem;color:var(--text-muted);margin-top:0.4rem;line-height:1.4;}
    .security-note{display:flex;align-items:center;gap:0.4rem;font-size:0.73rem;color:var(--text-muted);margin-top:0.5rem;justify-content:center;}


    .tds-hint{font-size:0.75rem;color:var(--text-muted);margin-bottom:0.6rem;}
    .tds-hint span{color:var(--accent);font-weight:700;}
    .tds-status{font-size:0.82rem;font-weight:700;padding:0.4rem 0.8rem;border-radius:6px;display:none;margin-bottom:0.5rem;}
    .tds-status.ok{display:block;background:rgba(62,207,142,0.12);color:#3ecf8e;}
    .tds-status.err{display:block;background:rgba(224,82,82,0.12);color:#e05252;}

    @keyframes fadeSlideIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
    .products-panel{animation:fadeSlideIn 0.4s ease both;}
    .checkout-panel{animation:fadeSlideIn 0.4s 0.1s ease both;}
    @media(max-width:900px){.shop-layout{grid-template-columns:1fr;}.checkout-panel{position:static;}.products-grid{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:600px){.products-grid{grid-template-columns:1fr;}.game-banner{flex-direction:column;align-items:flex-start;}}
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
            <i class="bi bi-music-note-list"></i> Música — Suscripción Pura
            <span class="gw-badge"><i class="bi bi-lightning-charge-fill"></i> API Gateway</span>
            <span class="sub-badge"><i class="bi bi-shield-lock-fill"></i> Tokenización</span>
        </div>
    </div>

    <div class="security-warning">
        <i class="bi bi-shield-exclamation"></i>
        <div style="width: 100%;">
            <div class="security-warning-header" onclick="toggleWarning()">
                <strong>⚠️ Aviso para comercios</strong>
                <i class="bi bi-chevron-down security-warning-toggle" id="warningToggle"></i>
            </div>
            <div class="security-warning-content" id="warningContent">
                La integración con API Gateway implica el manejo directo de datos sensibles del usuario. Para operar en producción es <strong>obligatorio</strong> contar con certificación <strong>PCI-DSS</strong> y se recomienda implementar <strong>3D Secure (3DS)</strong> para reducir el riesgo de fraude. Esta demo es solo con fines ilustrativos.
                <br><br>
                La base de datos de esta web <strong>NO! Guarda datos sensibles </strong> como el <strong> Numero de tarjeta, Fecha y CVV</strong> o <strong>Numeros de cuenta</strong> esta es solo una demostracion del servicio.
            </div>
        </div>
    </div>

    <main class="shop-layout">
        <section class="products-panel">

            <!-- SPOTIFY -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=spotify.com&sz=32" alt="Spotify" style="width:24px;height:24px;border-radius:4px;">
                    <span>Spotify</span>
                </div>
                <div class="products-grid">
                    <div class="product-card popular-card" data-id="1" data-servicio="Spotify" data-plan="Individual" data-precio="14900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Spotify</div>
                        <div class="product-card__pts">Individual</div>
                        <div class="product-card__label">Sin anuncios · Descargas · 1 cuenta</div>
                        <div class="product-card__price">14.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="2" data-servicio="Spotify" data-plan="Duo" data-precio="19900">
                        <div class="product-card__platform">Spotify</div>
                        <div class="product-card__pts">Duo</div>
                        <div class="product-card__label">Sin anuncios · Descargas · 2 cuentas</div>
                        <div class="product-card__price">19.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="3" data-servicio="Spotify" data-plan="Familiar" data-precio="24900">
                        <div class="product-card__platform">Spotify</div>
                        <div class="product-card__pts">Familiar</div>
                        <div class="product-card__label">Sin anuncios · Descargas · 6 cuentas</div>
                        <div class="product-card__price">24.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

            <!-- DEEZER -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=deezer.com&sz=32" alt="Deezer" style="width:24px;height:24px;border-radius:4px;">
                    <span>Deezer</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="4" data-servicio="Deezer" data-plan="Premium" data-precio="12900">
                        <div class="product-card__platform">Deezer</div>
                        <div class="product-card__pts">Premium</div>
                        <div class="product-card__label">Sin anuncios · HD · 1 cuenta</div>
                        <div class="product-card__price">12.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="5" data-servicio="Deezer" data-plan="Familia" data-precio="19900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Deezer</div>
                        <div class="product-card__pts">Familia</div>
                        <div class="product-card__label">Sin anuncios · HD · 6 cuentas</div>
                        <div class="product-card__price">19.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                </div>
            </div>
            <!-- Modo de simulación -->
            <div class="sim-mode-wrap">
                <span class="sim-mode-label">Modo de simulación</span>
                <div class="sim-mode-toggle">
                        <button type="button" class="sim-mode-opt active" id="modoElegir" onclick="setModo('elegir')">
                            <i class="bi bi-sliders"></i> Elegir estado
                        </button>
                        <button type="button" class="sim-mode-opt" id="modoAuto" onclick="setModo('auto')">
                            <i class="bi bi-lightning-charge-fill"></i> Pago normal
                        </button>
                </div>
                <div class="sim-mode-hint" id="modoHint">Elige manualmente cómo termina la suscripción.</div>
            </div>

        </section>

        <!-- CHECKOUT -->
        <aside class="checkout-panel">
            <div class="checkout-box">
                <div class="checkout-product-name" id="checkoutName">🎵 Spotify — Individual</div>
                <div class="checkout-price-row">
                    <span style="font-size:0.85rem;color:var(--text-secondary);">Total / mes</span>
                    <span class="checkout-price" id="checkoutPrice">14.900 COP</span>
                </div>

                <div class="checkout-divider"></div>

                <div class="token-info">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Suscripción pura — tu tarjeta será tokenizada de forma segura para futuros cobros.</span>
                </div>

                <span class="section-label-sm">Datos de la tarjeta</span>
                <div class="field-group">
                    <label class="field-label">Número de tarjeta</label>
                    <input type="text" class="field-input" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19">
                </div>
                <div class="field-row">
                    <div class="field-group">
                        <label class="field-label">Vencimiento</label>
                        <input type="text" class="field-input" id="cardExpiry" placeholder="MM/AA" maxlength="5">
                    </div>
                    <div class="field-group">
                        <label class="field-label">CVV</label>
                        <input type="password" class="field-input" id="cardCvv" placeholder="123" maxlength="4" inputmode="numeric">
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">Nombre en la tarjeta</label>
                    <input type="text" class="field-input" id="cardNameOnCard" placeholder="Como aparece en la tarjeta">
                </div>

                <div class="checkout-divider"></div>
                <span class="section-label-sm">Datos del titular</span>
                <div class="field-group">
                    <label class="field-label">Correo electrónico</label>
                    <input type="email" class="field-input" id="gwCorreo"
                           value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>">
                </div>
                <div class="field-group">
                    <label class="field-label">Teléfono</label>
                    <input type="text" class="field-input" id="gwTelefono" placeholder="3001234567">
                </div>
                <div class="field-row">
                    <div class="field-group">
                        <label class="field-label">Tipo de documento</label>
                        <select class="field-input" id="gwTipoDoc">
                            <option value="CC">Cédula</option>
                            <option value="CE">Cédula Extranjería</option>
                            <option value="NIT">NIT</option>
                            <option value="PP">Pasaporte</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Número de documento</label>
                        <input type="text" class="field-input" id="gwNumDoc" placeholder="1234567890">
                    </div>
                </div>

                <button class="btn-pagar" id="btnPagar">
                    <i class="bi bi-lock-fill"></i> Registrar suscripción
                </button>
                <div class="security-note">
                    <i class="bi bi-shield-check"></i>
                    Pago seguro · API Gateway · Evertec PlacetoPay
                </div>
            </div>
        </aside>
    </main>

    <script>
    (function() {
        const products = {
            1:{name:' Spotify — Individual',servicio:'Spotify',plan:'Individual',precio:14900,price:'14.900 COP'},
            2:{name:' Spotify — Duo',       servicio:'Spotify',plan:'Duo',       precio:19900,price:'19.900 COP'},
            3:{name:' Spotify — Familiar',  servicio:'Spotify',plan:'Familiar',  precio:24900,price:'24.900 COP'},
            4:{name:' Deezer — Premium',    servicio:'Deezer', plan:'Premium',   precio:12900,price:'12.900 COP'},
            5:{name:' Deezer — Familia',    servicio:'Deezer', plan:'Familia',   precio:19900,price:'19.900 COP'},
        };

        function updateCheckout(id) {
            const p = products[id];
            if (!p) return;
            document.getElementById('checkoutName').textContent  = p.name;
            document.getElementById('checkoutPrice').textContent = p.price;
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

        // Formatear número de tarjeta
        document.getElementById('cardNumber').addEventListener('input', function() {
            let v = this.value.replace(/\D/g,'').substring(0,16);
            this.value = v.replace(/(.{4})/g,'$1 ').trim();
        });
        // Formatear fecha
        document.getElementById('cardExpiry').addEventListener('input', function() {
            let v = this.value.replace(/\D/g,'').substring(0,4);
            if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
            this.value = v;
        });

        // ── Modo de simulación ──
        let modoSimulacion = 'elegir';
        window.setModo = function(modo) {
            modoSimulacion = modo;
            document.getElementById('modoElegir').classList.toggle('active', modo === 'elegir');
            document.getElementById('modoAuto').classList.toggle('active', modo === 'auto');
            document.getElementById('modoHint').textContent = (modo === 'elegir')
                ? 'Elige manualmente cómo termina la suscripción.'
                : 'El estado se asigna automáticamente, como un pago real.';
        };

        let envioEnCurso = false;
        document.getElementById('btnPagar').addEventListener('click', function() {
            if (envioEnCurso) return;

            const selected = document.querySelector('.product-card.selected');
            if (!selected) { alert('⚠️ Selecciona un plan primero.'); return; }

            const cardNum  = document.getElementById('cardNumber').value.replace(/\s/g,'');
            const expiry   = document.getElementById('cardExpiry').value;
            const cvv      = document.getElementById('cardCvv').value;
            const cardName = document.getElementById('cardNameOnCard').value.trim();
            const nombre   = cardName; // nombre en tarjeta = nombre del titular
            const correo   = document.getElementById('gwCorreo').value.trim();
            const telefono = document.getElementById('gwTelefono').value.trim();
            const tipoDoc  = document.getElementById('gwTipoDoc').value;
            const numDoc   = document.getElementById('gwNumDoc').value.trim();

            if (!cardNum || cardNum.length < 15) { alert('⚠️ Ingresa un número de tarjeta válido.'); return; }
            if (!expiry)  { alert('⚠️ Ingresa la fecha de vencimiento.'); return; }
            if (!cvv)     { alert('⚠️ Ingresa el CVV.'); return; }
            if (!cardName){ alert('⚠️ Ingresa el nombre en la tarjeta.'); return; }
            if (!nombre || !correo || !numDoc || !telefono) {
                alert('⚠️ Por favor completa todos los campos del titular.'); return;
            }

            const id = parseInt(selected.getAttribute('data-id'));
            const p  = products[id];

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = (modoSimulacion === 'auto')
                ? '../../php/crear_suscription_gateway.php'
                : '../../retorno/estados-subs-gateway.php';

            const campos = [
                ['servicio', p.servicio], ['plan', p.plan], ['precio', p.precio],
                ['nombre', nombre], ['correo', correo], ['telefono', telefono],
                ['tipo_doc', tipoDoc], ['num_doc', numDoc],
                ['card_number', document.getElementById('cardNumber').value.replace(/\s/g,'')],
                ['card_expiry', document.getElementById('cardExpiry').value],
                ['card_cvv',    document.getElementById('cardCvv').value],
                ['card_name',   document.getElementById('cardNameOnCard').value]
            ];

            campos.forEach(function(pair) {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = pair[0]; input.value = pair[1];
                form.appendChild(input);
            });

            envioEnCurso = true;
            const btn = document.getElementById('btnPagar');
            btn.disabled = true;
            btn.style.opacity = '0.6';
            btn.style.cursor = 'not-allowed';
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';

            document.body.appendChild(form);
            form.submit();
        });
    })();

    // Toggle para el mensaje de aviso
    function toggleWarning() {
        const content = document.getElementById('warningContent');
        const toggle = document.getElementById('warningToggle');
        content.classList.toggle('collapsed');
        toggle.classList.toggle('collapsed');
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>