<?php
session_start();
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) { header("Location: ../../index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Strike — Gold</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">



    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    :root {
        --bg-base:#0d0e10; --bg-surface:#16181c; --bg-card:#1e2128;
        --bg-card-hover:#252830; --bg-selected:#1a1400; --border:#2e3038;
        --accent:#f0b429; --accent-glow:rgba(240,180,41,0.25); --accent-dark:#c99010;
        --text-primary:#f0f1f3; --text-secondary:#8a8d96; --text-muted:#555860;
        --font-display:'Barlow',sans-serif; --font-body:'Barlow',sans-serif;
        --radius-sm:6px; --radius-md:10px; --radius-lg:14px;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{background-color:var(--bg-base);color:var(--text-primary);font-family:var(--font-body);min-height:100vh;-webkit-font-smoothing:antialiased;}
    .navbar{background-color:#0f0f0fa9!important;backdrop-filter:blur(8px);border-bottom:1px solid var(--pt-border);}

    .game-banner{display:flex;align-items:center;justify-content:space-between;padding:0.6rem 2rem;background:var(--pt-th2);border-bottom:1px solid var(--pt-border);gap:1rem;}
    .game-banner__tag{display:flex;align-items:center;gap:0.5rem;font-family:var(--font-display);font-weight:700;font-size:1rem;letter-spacing:0.04em;color:var(--pt-text);}
    .gw-badge{background:rgba(240,180,41,0.15);color:var(--accent);font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}
    .tds-badge{background:rgba(60, 255, 0, 0.15);color: #03ff03; font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}

    .banner-player-id{display:flex;align-items:center;gap:0.5rem;}
    .banner-player-id label{font-size:0.82rem;font-weight:600;color:var(--pt-text-sec);white-space:nowrap;}
    .banner-player-id input{background:var(--pt-navbar);border:1.5px solid var(--pt-border);border-radius:var(--radius-sm);color:var(--pt-text);font-family:var(--font-body);font-size:0.85rem;padding:0.35rem 0.75rem;outline:none;transition:border-color 0.2s;width:180px;}
    .banner-player-id input:focus{border-color:var(--accent);}
    .banner-player-id input::placeholder{color:var(--text-muted);}

    .shop-layout{display:grid;grid-template-columns:1fr 360px;gap:1.5rem;max-width:1200px;margin:1.5rem auto;padding:0 1.5rem 3rem;align-items:start;}

    .section-label{font-family:var(--font-display);font-size:0.8rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:0.75rem;}
    .products-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0.65rem;}

    .product-card{position:relative;background:var(--pt-navbar);border:1.5px solid var(--pt-border);border-radius:var(--radius-md);padding:0.9rem 0.75rem 0.8rem;cursor:pointer;transition:all 0.18s ease;display:flex;flex-direction:column;gap:0.1rem;overflow:hidden;}
    .product-card:hover{background:var(--pt-boxitem);border-color:rgba(240,180,41,0.4);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.35);}
    .product-card.selected{background:var(--bg-border);border-color:var(--accent);box-shadow:0 0 0 1px var(--accent),0 4px 24px var(--accent-glow);}
    .product-card.selected::after{content:'✔';position:absolute;top:0.5rem;right:0.55rem;width:18px;height:18px;background:var(--accent);border-radius:50%;color:#fff;font-size:0.65rem;display:flex;align-items:center;justify-content:center;font-weight:900;line-height:18px;text-align:center;}
    .badge-popular{position:absolute;top:-1px;left:-1px;background:var(--accent);color:#0d0e10;font-family:var(--font-display);font-size:0.68rem;font-weight:800;letter-spacing:0.05em;padding:0.15rem 0.5rem;border-radius:var(--radius-sm) 0 var(--radius-sm) 0;}
    .product-card__img{font-size:1.4rem;margin-bottom:0.2rem;}
    .product-card__pts{font-family:var(--font-display);font-size:1.2rem;font-weight:800;color:var(--pt-text);line-height:1;}
    .product-card__label{font-size:0.72rem;color:var(--pt-text-sec);margin-bottom:0.25rem;}
    .product-card__price{font-family:var(--font-display);font-size:0.95rem;font-weight:700;color:var(--accent);margin-top:auto;}
    .discount-tag{background:rgba(240,180,41,0.15);color:var(--accent);font-size:0.65rem;font-weight:700;padding:0.1rem 0.3rem;border-radius:3px;margin-left:0.2rem;}

    /* CHECKOUT */
    .checkout-panel{display:flex;flex-direction:column;gap:1rem;position:sticky;top:16px;}
    .checkout-box{background:var(--pt-navbar);border:1px solid var(--pt-border);border-radius:var(--radius-lg);padding:1.2rem 1.3rem;}
    .checkout-product-name{font-family:var(--font-display);font-size:1.2rem;font-weight:800;color:var(--pt-text);margin-bottom:0.8rem;display:flex;align-items:center;gap:0.6rem;}
    .checkout-product-name img{width:30px;height:30px;object-fit:contain;flex-shrink:0;}
    .checkout-product-name img[src=""],.checkout-product-name img:not([src]){display:none;}
    .checkout-price-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:0.8rem;}
    .checkout-price{font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:var(--pt-text);}
    .checkout-divider{height:1px;background:var(--pt-border);margin:0.8rem 0;}
    .section-label-sm{font-family:var(--font-display);font-size:0.73rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--pt-text-sec);margin-bottom:0.5rem;display:block;}

    .field-group{margin-bottom:0.65rem;}
    .field-label{font-size:0.73rem;font-weight:600;color:var(--pt-text-sec);margin-bottom:0.25rem;display:block;}
    .field-input{width:100%;background:var(--pt-border);border:1.5px solid var(--pt-border);border-radius:8px;color:var(--pt-text);font-family:var(--font-body);font-size:0.83rem;padding:0.4rem 0.7rem;outline:none;transition:border-color 0.2s;}
    .field-input:focus{border-color:var(--accent);}
    .field-input::placeholder{color:var(--text-muted);}
    .field-row{display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;}

    .btn-pagar{width:100%;margin-top:0.8rem;padding:0.8rem;background:var(--accent);border:none;border-radius:var(--radius-md);color:#0d0e10;font-family:var(--font-display);font-size:1rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;cursor:pointer;transition:all 0.18s ease;display:flex;align-items:center;justify-content:center;gap:0.5rem;}
    .btn-pagar:hover{background:var(--accent-dark);transform:translateY(-1px);box-shadow:0 6px 20px var(--accent-glow);}
    .sim-mode-wrap{margin-top:0.9rem;}
    .sim-mode-label{font-family:var(--font-display);font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--pt-text);margin-bottom:0.45rem;display:block;}
    .sim-mode-toggle{display:grid;grid-template-columns:1fr 1fr;gap:0.4rem;background:var(--pt-navbar);border:1.5px solid var(--pt-border);border-radius:var(--radius-md);padding:0.3rem;}
    .sim-mode-opt{border:none;background:transparent;cursor:pointer;padding:0.55rem 0.4rem;border-radius:var(--radius-sm);font-family:var(--font-body);font-size:0.8rem;font-weight:600;color:var(--text-secondary);transition:all 0.18s ease;display:flex;align-items:center;justify-content:center;gap:0.35rem;}
    .sim-mode-opt:hover{background:rgba(248, 175, 49, 0.12); color:var(--pt-text-sec);}
    .sim-mode-opt.active{background:rgba(248, 175, 49, 0.12);color:var(--accent);box-shadow:inset 0 0 0 1.5px var(--accent);}
    .sim-mode-hint{font-size:0.72rem;color:var(--text-muted);margin-top:0.4rem;line-height:1.4;}
    .security-note{display:flex;align-items:center;gap:0.4rem;font-size:0.73rem;color:var(--text-muted);margin-top:0.5rem;justify-content:center;}

    /* Tabs metodo pago */
    .payment-tabs{display:flex;gap:0.5rem;margin-bottom:0.8rem;}
    .payment-tab{flex:1;padding:0.45rem;border:1.5px solid var(--pt-border);border-radius:var(--radius-sm);background:var(--pt-boxitem);color:var(--pt-text-sec);font-family:var(--font-body);font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;text-align:center;display:flex;align-items:center;justify-content:center;gap:0.3rem;}
    .payment-tab:hover{border-color:var(--accent);color:var(--pt-text-sec);}
    .payment-tab.active{border-color:var(--accent);background: var(--pt-boxitem);color:var(--accent);}
    .form-section{display:none;}
    .form-section.active{display:block;}
    .security-warning{background:rgba(224,82,82,0.08);border-left:4px solid #e05252;border-radius:0 8px 8px 0;padding:0.9rem 1.2rem;margin:1rem 2rem;display:flex;gap:0.8rem;align-items:flex-start;font-size:0.83rem;color:var(--pt-text);line-height:1.6;}
    .security-warning i{color:#e05252;font-size:1.2rem;flex-shrink:0;margin-top:0.1rem;}
    .security-warning strong{color:#e05252;}
    .security-warning-header{display:flex;align-items:center;justify-content:space-between;width:100%;cursor:pointer;user-select:none;}
    .security-warning-toggle{color:#e05252;font-size:1rem;transition:transform 0.3s ease;margin-left:auto;padding-left:1rem;}
    .security-warning-toggle.collapsed{transform:rotate(-90deg);}
    .security-warning-content{margin-top:0.5rem;overflow:hidden;max-height:500px;transition:max-height 0.3s ease,opacity 0.3s ease;opacity:1;}
    .security-warning-content.collapsed{max-height:0;opacity:0;margin-top:0;}


    /* MODAL 3DS */
    .tds-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
    .tds-overlay.show{display:flex;}
    .tds-modal{background:var(--bg-surface);border:1.5px solid rgba(240,180,41,0.3);border-radius:16px;padding:2rem 1.8rem;max-width:380px;width:90%;text-align:center;animation:fadeUp 0.3s ease;}
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
    .tds-modal-icon{font-size:2.5rem;margin-bottom:0.8rem;}
    .tds-modal-title{font-family:var(--font-display);font-size:1.5rem;font-weight:800;color:#f0b429;letter-spacing:0.04em;margin-bottom:0.3rem;}
    .tds-modal-sub{font-size:0.83rem;color:var(--text-secondary);line-height:1.6;margin-bottom:1.2rem;}
    .tds-inputs{display:flex;gap:0.45rem;justify-content:center;margin-bottom:0.8rem;}
    .tds-digit{width:46px;height:52px;background:var(--bg-card);border:1.5px solid var(--border);border-radius:8px;color:var(--text-primary);font-size:1.4rem;font-weight:800;text-align:center;outline:none;transition:border-color 0.2s;font-family:var(--font-display);}
    .tds-digit:focus{border-color:#f0b429;}
    .tds-digit.error{border-color:#e05252;animation:shake 0.3s ease;}
    .tds-digit.success{border-color:#3ecf8e;}
    @keyframes shake{0%,100%{transform:translateX(0);}25%{transform:translateX(-4px);}75%{transform:translateX(4px);}}
    .tds-hint{font-size:0.75rem;color:var(--text-muted);margin-bottom:0.8rem;}
    .tds-hint span{color:#f0b429;font-weight:700;}
    .tds-status{font-size:0.82rem;font-weight:700;padding:0.45rem 0.9rem;border-radius:6px;display:none;margin-bottom:0.8rem;}
    .tds-status.ok{display:block;background:rgba(62,207,142,0.12);color:#3ecf8e;}
    .tds-status.err{display:block;background:rgba(224,82,82,0.12);color:#e05252;}
    .btn-tds-cancel{background:transparent;border:1px solid var(--border);color:var(--text-secondary);border-radius:8px;padding:0.5rem 1.2rem;font-family:var(--font-body);font-size:0.85rem;cursor:pointer;transition:all 0.2s;margin-top:0.4rem;}
    .btn-tds-cancel:hover{border-color:#e05252;color:#e05252;}

    @keyframes fadeSlideIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
    .products-panel{animation:fadeSlideIn 0.4s ease both;}
    .checkout-panel{animation:fadeSlideIn 0.4s 0.1s ease both;}
    @media(max-width:900px){.shop-layout{grid-template-columns:1fr;}.checkout-panel{position:static;}.products-grid{grid-template-columns:repeat(3,1fr);}}
    @media(max-width:600px){.products-grid{grid-template-columns:repeat(2,1fr);}.game-banner{flex-direction:column;align-items:flex-start;}}
</style>
<body>
    <?php
    $nav_back_url  = "juegos.php";
    $nav_back_text = "Atras";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>

    <div class="game-banner">
        <div class="game-banner__tag">
            🩸 Blood Strike — Gold
            <span class="gw-badge">⚡ API Gateway</span>
            <!--<span class="tds-badge"><i class="bi bi-shield-lock-fill"></i> 3DS Obligatorio</span -->
        </div>
        <div class="banner-player-id">
            <label for="jugadorIdInput">🎯 ID de jugador</label>
            <input type="text" id="jugadorIdInput" placeholder="Ej: 512345678" autocomplete="off">
        </div>

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
            <p class="section-label">Elige el importe de Gold</p>
            <div class="products-grid">

                <div class="product-card" data-id="1" data-price="4900" data-pts="80">
                    <div class="product-card__img">
                        <img src="https://cdn.gameboost.com/games/blood-strike/gold/gold.webp" style="height: 40px; width: 40px" alt="">
                    </div>
                    <div class="product-card__pts">80 Gold</div>
                    <div class="product-card__label">Blood Strike</div>
                    <div class="product-card__price">4.900 COP</div>
                </div>

                <div class="product-card" data-id="2" data-price="9900" data-pts="170">
                    <div class="product-card__img">
                        <img src="https://cdn.gameboost.com/games/blood-strike/gold/gold.webp" style="height: 40px; width: 40px" alt="">
                    </div>
                    <div class="product-card__pts">170 Gold</div>
                    <div class="product-card__label">Blood Strike</div>
                    <div class="product-card__price">9.900 COP</div>
                </div>

                <div class="product-card popular-card" data-id="3" data-price="19900" data-pts="360">
                    <div class="badge-popular">★ Popular</div>
                    <div class="product-card__img">
                        <img src="https://cdn.gameboost.com/games/blood-strike/gold/gold.webp" style="height: 40px; width: 40px" alt="">
                    </div>
                    <div class="product-card__pts">360 Gold</div>
                    <div class="product-card__label">Blood Strike</div>
                    <div class="product-card__price">19.900 COP <span class="discount-tag">+20 extra</span></div>
                </div>

                <div class="product-card" data-id="4" data-price="34900" data-pts="660">
                    <div class="product-card__img">
                        <img src="https://cdn.gameboost.com/games/blood-strike/gold/gold.webp" style="height: 40px; width: 40px" alt="">

                    </div>
                    <div class="product-card__pts">660 Gold</div>
                    <div class="product-card__label">Blood Strike</div>
                    <div class="product-card__price">34.900 COP <span class="discount-tag">+40 extra</span></div>
                </div>

                <div class="product-card" data-id="5" data-price="54900" data-pts="1120">
                    <div class="product-card__img">
                        <img src="https://cdn.gameboost.com/games/blood-strike/gold/gold.webp" style="height: 40px; width: 40px" alt="">
                    </div>
                    <div class="product-card__pts">1120 Gold</div>
                    <div class="product-card__label">Blood Strike</div>
                    <div class="product-card__price">54.900 COP <span class="discount-tag">+80 extra</span></div>
                </div>

                <div class="product-card popular-card" data-id="6" data-price="99900" data-pts="2240">
                    <div class="badge-popular">🔥 Mejor valor</div>
                    <div class="product-card__img">
                        <img src="https://cdn.gameboost.com/games/blood-strike/gold/gold.webp" style="height: 40px; width: 40px" alt="">
                    </div>
                    <div class="product-card__pts">2240 Gold</div>
                    <div class="product-card__label">Blood Strike</div>
                    <div class="product-card__price">99.900 COP <span class="discount-tag">+200 extra</span></div>
                </div>

                <div class="product-card" data-id="7" data-price="179900" data-pts="4480">
                    <div class="product-card__img">
                        <img src="https://cdn.gameboost.com/games/blood-strike/gold/gold.webp" style="height: 40px; width: 40px" alt="">
                    </div>
                    <div class="product-card__pts">4480 Gold</div>
                    <div class="product-card__label">Blood Strike</div>
                    <div class="product-card__price">179.900 COP <span class="discount-tag">+480 extra</span></div>
                </div>

                <div class="product-card" data-id="8" data-price="299900" data-pts="8960">
                    <div class="product-card__img">
                        <img src="https://cdn.gameboost.com/games/blood-strike/gold/gold.webp" style="height: 40px; width: 40px" alt="">
                    </div>
                    <div class="product-card__pts">8960 Gold</div>
                    <div class="product-card__label">Blood Strike</div>
                    <div class="product-card__price">299.900 COP <span class="discount-tag">+960 extra</span></div>
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
                <div class="sim-mode-hint" id="modoHint">Elige manualmente cómo termina la transacción.</div>
            </div>
        </section>

        <!-- CHECKOUT -->
        <aside class="checkout-panel">
            <div class="checkout-box">
                <div class="checkout-product-name"><img id="checkoutImg" src="" alt="" /><span id="checkoutName">🥇 360 Gold</span></div>
                <div class="checkout-price-row">
                    <span style="font-size:0.85rem;color:var(--text-secondary);">Total</span>
                    <span class="checkout-price" id="checkoutPrice">19.900 COP</span>
                </div>

                <div class="checkout-divider"></div>

                <!-- Tabs método de pago -->
                <div class="payment-tabs">
                    <button class="payment-tab active" id="tabTarjeta" onclick="setPayment('tarjeta')">
                        <i class="bi bi-credit-card-fill"></i> Tarjeta
                    </button>
                    <button class="payment-tab" id="tabCuenta" onclick="setPayment('cuenta')">
                        <i class="bi bi-bank2"></i> Cuenta
                    </button>
                </div>

                <!-- FORMULARIO TARJETA -->
                <div class="form-section active" id="formTarjeta">
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
                        <input type="text" class="field-input" id="cardName" placeholder="Como aparece en la tarjeta">
                    </div>
                    <div class="checkout-divider"></div>
                    <span class="section-label-sm">Datos del titular</span>
                    <div class="field-group">
                        <label class="field-label">Correo electrónico</label>
                        <input type="email" class="field-input" id="bsCorreo" value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Teléfono</label>
                        <input type="text" class="field-input" id="bsTelefono" placeholder="3001234567">
                    </div>
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">Tipo de documento</label>
                            <select class="field-input" id="bsTipoDoc">
                                <option value="CC">Cédula</option>
                                <option value="CE">Cédula Extranjería</option>
                                <option value="NIT">NIT</option>
                                <option value="PP">Pasaporte</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Número de documento</label>
                            <input type="text" class="field-input" id="bsNumDoc" placeholder="1234567890">
                        </div>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Nombre completo</label>
                        <input type="text" class="field-input" id="bsNombre" placeholder="Nombre y apellido">
                    </div>
                </div>

                <!-- FORMULARIO CUENTA -->
                <div class="form-section" id="formCuenta">
                    <span class="section-label-sm">Datos bancarios</span>
                    <div class="field-group">
                        <label class="field-label">Banco</label>
                        <select class="field-input" id="cuentaBanco">
                            <option value="BANCOLOMBIA">Bancolombia</option>
                            <option value="NEQUI">Nequi</option>
                            <option value="DAVIVIENDA">Davivienda</option>
                            <option value="BBVA">BBVA</option>
                            <option value="BOGOTA">Banco de Bogotá</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Tipo de cuenta</label>
                        <select class="field-input" id="cuentaTipo">
                            <option value="AHORROS">Ahorros</option>
                            <option value="CORRIENTE">Corriente</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Número de cuenta</label>
                        <input type="text" class="field-input" id="cuentaNumero" placeholder="0000000000">
                    </div>
                    <div class="checkout-divider"></div>
                    <span class="section-label-sm">Datos del titular</span>
                    <div class="field-group">
                        <label class="field-label">Nombre completo</label>
                        <input type="text" class="field-input" id="cuentaNombre" placeholder="Nombre y apellido">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Correo electrónico</label>
                        <input type="email" class="field-input" id="cuentaCorreo" value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Teléfono</label>
                        <input type="text" class="field-input" id="cuentaTelefono" placeholder="3001234567">
                    </div>
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">Tipo de documento</label>
                            <select class="field-input" id="cuentaTipoDoc">
                                <option value="CC">Cédula</option>
                                <option value="CE">Cédula Extranjería</option>
                                <option value="NIT">NIT</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Número de documento</label>
                            <input type="text" class="field-input" id="cuentaNumDoc" placeholder="1234567890">
                        </div>
                    </div>
                </div>

                <button class="btn-pagar" id="btnPagar">
                     Pagar ahora
                </button>
                <div class="security-note">
                    <i class="bi bi-shield-check"></i>
                     API Gateway · Evertec
                </div>
            </div>
        </aside>
    </main>

    <input type="hidden" id="usuarioIdInput" value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>">
    <input type="hidden" id="currentPayment" value="tarjeta">

    <script>

        
    (function() {
        const products = {
            1:{name:' 80 Gold',   precio:4900,  price:'4.900 COP'},
            2:{name:' 170 Gold',  precio:9900,  price:'9.900 COP'},
            3:{name:' 360 Gold',  precio:19900, price:'19.900 COP'},
            4:{name:' 660 Gold',  precio:34900, price:'34.900 COP'},
            5:{name:' 1120 Gold', precio:54900, price:'54.900 COP'},
            6:{name:' 2240 Gold', precio:99900, price:'99.900 COP'},
            7:{name:' 4480 Gold', precio:179900,price:'179.900 COP'},
            8:{name:' 8960 Gold', precio:299900,price:'299.900 COP'},
        };

        function updateCheckout(id) {
            const p = products[id];
            if (!p) return;
            document.getElementById('checkoutName').textContent  = p.name;
            document.getElementById('checkoutPrice').textContent = p.price;

            const imgEl   = document.getElementById('checkoutImg');
            const cardImg = document.querySelector('.product-card[data-id="' + id + '"] img');
            if (imgEl && cardImg) {
                imgEl.src = cardImg.getAttribute('src');
                imgEl.style.display = '';
            } else if (imgEl) {
                imgEl.style.display = 'none';
            }
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
            var def = document.querySelector('.product-card[data-id="3"]');
            if (def) { def.classList.add('selected'); updateCheckout(3); }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCards);
        } else { initCards(); }

        // ── Tabs método de pago ──
        window.setPayment = function(method) {
            document.getElementById('currentPayment').value = method;
            document.getElementById('tabTarjeta').classList.toggle('active', method === 'tarjeta');
            document.getElementById('tabCuenta').classList.toggle('active', method === 'cuenta');
            document.getElementById('formTarjeta').classList.toggle('active', method === 'tarjeta');
            document.getElementById('formCuenta').classList.toggle('active', method === 'cuenta');
        };

        // Formatear tarjeta
        document.getElementById('cardNumber').addEventListener('input', function() {
            let v = this.value.replace(/\D/g,'').substring(0,16);
            this.value = v.replace(/(.{4})/g,'$1 ').trim();
        });
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
                ? 'Elige manualmente cómo termina la transacción.'
                : 'El estado se asigna automáticamente, como un pago real.';
        };
        let envioEnCurso = false;
        document.getElementById('btnPagar').addEventListener('click', function() {
            if (envioEnCurso) return; // ya se está procesando, ignorar clics repetidos
            const jugadorId = document.getElementById('jugadorIdInput').value.trim();
            if (!jugadorId) { alert('⚠️ Por favor ingresa tu ID de jugador.'); return; }

            const selected = document.querySelector('.product-card.selected');
            if (!selected) { alert('⚠️ Selecciona un producto.'); return; }

            const method = document.getElementById('currentPayment').value;
            let nombre, correo, telefono, tipoDoc, numDoc;

            if (method === 'tarjeta') {
                const cardNum = document.getElementById('cardNumber').value.replace(/\s/g,'');
                const cvv     = document.getElementById('cardCvv').value;
                const expiry  = document.getElementById('cardExpiry').value;
                nombre   = document.getElementById('cardName').value.trim();
                correo   = document.getElementById('bsCorreo').value.trim();
                telefono = document.getElementById('bsTelefono').value.trim();
                tipoDoc  = document.getElementById('bsTipoDoc').value;
                numDoc   = document.getElementById('bsNumDoc').value.trim();
                if (!cardNum || cardNum.length < 15) { alert('⚠️ Ingresa un número de tarjeta válido.'); return; }
                if (!expiry) { alert('⚠️ Ingresa la fecha de vencimiento.'); return; }
                if (!cvv)    { alert('⚠️ Ingresa el CVV.'); return; }
            } else {
                nombre   = document.getElementById('cuentaNombre').value.trim();
                correo   = document.getElementById('cuentaCorreo').value.trim();
                telefono = document.getElementById('cuentaTelefono').value.trim();
                tipoDoc  = document.getElementById('cuentaTipoDoc').value;
                numDoc   = document.getElementById('cuentaNumDoc').value.trim();
            }

            if (!nombre || !correo || !telefono || !numDoc) {
                alert('⚠️ Por favor completa todos los campos del titular.'); return;
            }

            const id = parseInt(selected.getAttribute('data-id'));
            const p  = products[id];

            // Armar formulario pero NO enviarlo aún
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = (modoSimulacion === 'auto')
                ? '../../php/crear_pb_gateway.php'
                : '../../retorno/estados-gateway.php';

            const campos = [
                ['producto', p.name], ['precio', p.precio],
                ['jugador_id', jugadorId], ['metodo', method],
                ['card_name', nombre], ['correo', correo],
                ['telefono', telefono], ['tipo_doc', tipoDoc],
                ['num_doc', numDoc]
            ];

            // Si es tarjeta, agregar datos de tarjeta
            if (method === 'tarjeta') {
                campos.push(
                    ['card_number', document.getElementById('cardNumber').value.replace(/\s/g,'')],
                    ['card_cvv',    document.getElementById('cardCvv').value],
                    ['card_expiry', document.getElementById('cardExpiry').value]
                );
            } else {
                campos.push(
                    ['num_cuenta',   document.getElementById('cuentaNumero').value],
                    ['cuenta_banco', document.getElementById('cuentaBanco').value]
                );
            }

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

            pendingForm = form;
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