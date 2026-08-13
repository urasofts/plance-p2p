<?php
session_start();
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) { header("Location: ../../index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streaming — API Gateway</title>
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
        --bg-base:var(--pt-bg-base); --bg-surface:var(--pt-bg-surface); --bg-card:var(--pt-navbar);
        --bg-card-hover:var(--pt-hover); --bg-selected:rgba(245, 158, 11, 0.1); --border:var(--pt-border);
        --accent:#f59e0b; --accent-glow:rgba(245,158,11,0.25); --accent-dark:#d97706;
        --text-primary:var(--pt-text); --text-secondary:var(--pt-text-sec); --text-muted:var(--pt-text-sec);
        --font-display:'Barlow',sans-serif; --font-body:'Barlow',sans-serif;
        --radius-sm:6px; --radius-md:10px; --radius-lg:14px;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{background-color:var(--bg-base);color:var(--text-primary);font-family:var(--font-body);min-height:100vh;-webkit-font-smoothing:antialiased;}
    .navbar{background-color:var(--pt-navbar)!important;backdrop-filter:blur(8px);border-bottom:1px solid var(--border);}

    /* AVISO SEGURIDAD */
    .security-warning {
        background: rgba(224,82,82,0.08);
        border-left: 4px solid #e05252;
        border-radius: 0 8px 8px 0;
        padding: 0.9rem 1.2rem;
        margin: 1rem 2rem;
        display: flex; gap: 0.8rem; align-items: flex-start;
        font-size: 0.83rem; color: var(--text-primary); line-height: 1.6;
    }
    .security-warning i { color: #e05252; font-size: 1.2rem; flex-shrink: 0; margin-top: 0.1rem; }
    .security-warning strong { color: #e05252; }
    .security-warning-header{display:flex;align-items:center;justify-content:space-between;width:100%;cursor:pointer;user-select:none;}
    .security-warning-toggle{color:#e05252;font-size:1rem;transition:transform 0.3s ease;margin-left:auto;padding-left:1rem;}
    .security-warning-toggle.collapsed{transform:rotate(-90deg);}
    .security-warning-content{margin-top:0.5rem;overflow:hidden;max-height:500px;transition:max-height 0.3s ease,opacity 0.3s ease;opacity:1;}
    .security-warning-content.collapsed{max-height:0;opacity:0;margin-top:0;}

    .game-banner{display:flex;align-items:center;justify-content:space-between;padding:0.6rem 2rem;background:var(--bg-surface);border-bottom:1px solid var(--border);gap:1rem;}
    .game-banner__tag{display:flex;align-items:center;gap:0.5rem;font-family:var(--font-display);font-weight:700;font-size:1rem;letter-spacing:0.04em;color:var(--text-primary);}
    .gw-badge{background:rgba(245,158,11,0.15);color:var(--accent);font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}

    .shop-layout{display:grid;grid-template-columns:1fr 370px;gap:1.5rem;max-width:1200px;margin:1.5rem auto;padding:0 1.5rem 3rem;align-items:start;}

    .section-block{margin-bottom:1.8rem;}
    .platform-header{display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border);}
    .platform-header span{font-family:var(--font-display);font-size:1.1rem;font-weight:800;letter-spacing:0.04em;color:var(--text-primary);}

    .products-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0.65rem;}
    .product-card{position:relative;background:var(--bg-card);border:1.5px solid var(--border);border-radius:var(--radius-md);padding:1rem 0.85rem 0.9rem;cursor:pointer;transition:all 0.18s ease;display:flex;flex-direction:column;gap:0.15rem;overflow:hidden;}
    .product-card:hover{background:var(--bg-card-hover);border-color:rgba(245,158,11,0.4);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.35);}
    .product-card.selected{background:var(--pt-border);border-color:var(--accent);box-shadow:0 0 0 1px var(--accent),0 4px 24px var(--accent-glow);}
    .product-card.selected::after{content:'✔';position:absolute;top:0.5rem;right:0.55rem;width:18px;height:18px;background:var(--accent);border-radius:50%;color:#0d0e10;font-size:0.65rem;display:flex;align-items:center;justify-content:center;font-weight:900;line-height:18px;text-align:center;}
    .badge-popular{position:absolute;top:-1px;left:-1px;background:var(--accent);color:#0d0e10;font-family:var(--font-display);font-size:0.68rem;font-weight:800;letter-spacing:0.05em;padding:0.15rem 0.5rem;border-radius:var(--radius-sm) 0 var(--radius-sm) 0;}
    .product-card__platform{font-size:0.7rem;font-weight:600;color:var(--accent);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.1rem;}
    .product-card__pts{font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--text-primary);line-height:1.1;}
    .product-card__label{font-size:0.72rem;color:var(--text-secondary);margin-bottom:0.3rem;}
    .product-card__price{font-family:var(--font-display);font-size:1rem;font-weight:700;color:var(--accent);display:flex;align-items:center;gap:0.35rem;flex-wrap:wrap;margin-top:auto;}
    .sub-tag{background:rgba(245,158,11,0.12);color:var(--accent);font-size:0.65rem;font-weight:700;padding:0.1rem 0.35rem;border-radius:3px;}

    /* CHECKOUT */
    .checkout-panel{display:flex;flex-direction:column;gap:1rem;position:sticky;top:16px;}
    .checkout-box{background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.2rem 1.3rem;}
    .checkout-product-name{font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--text-primary);margin-bottom:0.6rem;}
    .checkout-price-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;}
    .checkout-price{font-family:var(--font-display);font-size:1.4rem;font-weight:800;color:var(--text-primary);}
    .checkout-divider{height:1px;background:var(--border);margin:0.7rem 0;}

    /* Tabs método pago */
    .payment-tabs{display:flex;gap:0.5rem;margin-bottom:0.8rem;}
    .payment-tab{flex:1;padding:0.45rem;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-secondary);font-family:var(--font-body);font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;text-align:center;display:flex;align-items:center;justify-content:center;gap:0.3rem;}
    .payment-tab:hover{border-color:var(--accent);color:var(--text-primary);}
    .payment-tab.active{border-color:var(--accent);background:rgba(245,158,11,0.1);color:var(--accent);}

    .form-section{display:none;}
    .form-section.active{display:block;}

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
    .sim-mode-opt:hover{background-color: rgba(255, 194, 89, 0.12); ;color:var(--text-primary);}
    .sim-mode-opt.active{background:rgba(245,158,11,0.12);color:var(--accent);box-shadow:inset 0 0 0 1.5px var(--accent);}
    .sim-mode-hint{font-size:0.72rem;color:var(--text-muted);margin-top:0.4rem;line-height:1.4;}
    .security-note{display:flex;align-items:center;gap:0.4rem;font-size:0.73rem;color:var(--text-muted);margin-top:0.5rem;justify-content:center;}

    /* 3DS */
    .tds-badge{background:rgba(60, 255, 0, 0.15);color: #03ff03; font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}
    .tds-check-wrap{display:flex;align-items:flex-start;gap:0.5rem;padding:0.7rem 0.8rem;margin-top:0.8rem;background:rgba(245,158,11,0.06);border:1.5px solid rgba(245,158,11,0.2);border-radius:8px;cursor:pointer;}
    .tds-check-wrap input[type="checkbox"]{width:16px;height:16px;accent-color:var(--accent);flex-shrink:0;margin-top:2px;}
    .tds-check-label{font-size:0.81rem;color:#c99010;line-height:1.4;}
    .tds-check-label strong{color:var(--accent);}
    .tds-panel{display:none;background:rgba(245,158,11,0.05);border:1.5px solid rgba(245,158,11,0.25);border-radius:10px;padding:1rem;margin-top:0.8rem;text-align:center;animation:fadeSlideIn 0.25s ease;}
    .tds-panel.show{display:block;}
    .tds-panel-title{font-family:var(--font-display);font-size:1rem;font-weight:800;color:var(--accent);letter-spacing:0.04em;margin-bottom:0.3rem;}
    .tds-panel-sub{font-size:0.78rem;color:var(--text-secondary);margin-bottom:0.8rem;line-height:1.5;}
    .tds-inputs{display:flex;gap:0.4rem;justify-content:center;margin-bottom:0.8rem;}
    .tds-digit{width:42px;height:48px;background:var(--bg-card);border:1.5px solid var(--border);border-radius:8px;color:var(--text-primary);font-size:1.3rem;font-weight:800;text-align:center;outline:none;transition:border-color 0.2s;font-family:var(--font-display);}
    .tds-digit:focus{border-color:var(--accent);}
    .tds-digit.error{border-color:#e05252;}
    .tds-digit.success{border-color:#3ecf8e;}
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
            <i class="bi bi-tv-fill"></i> Streaming — Pago + Suscripción
            <span class="gw-badge"><i class="bi bi-lightning-charge-fill"></i> API Gateway</span>
        </div>
    </div>

    <!-- AVISO DE SEGURIDAD PARA COMERCIOS -->
    <div class="security-warning">
        <i class="bi bi-shield-exclamation"></i>
        <div style="width: 100%;">
            <div class="security-warning-header" onclick="toggleWarning()">
                <strong>⚠️ Aviso para comercios</strong>
                <i class="bi bi-chevron-down security-warning-toggle" id="warningToggle"></i>
            </div>
            <div class="security-warning-content" id="warningContent">
                La integración con API Gateway implica el manejo directo de datos sensibles del usuario (número de tarjeta, CVV, datos bancarios). Para operar en producción es <strong>obligatorio</strong> contar con certificación <strong>PCI-DSS</strong> y se recomienda implementar autenticación <strong>3D Secure (3DS)</strong> para reducir el riesgo de fraude. Esta demo es solo con fines ilustrativos.
                <br><br>
                La base de datos de esta web <strong>NO! Guarda datos sensibles </strong> como el <strong> Numero de tarjeta, Fecha y CVV</strong> o <strong>Numeros de cuenta</strong> esta es solo una demostracion del servicio.
            </div>
        </div>
    </div>

    <main class="shop-layout">
        <section class="products-panel">

            <!-- NETFLIX -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=netflix.com&sz=32" alt="Netflix" style="width:24px;height:24px;border-radius:4px;">
                    <span>Netflix</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="1" data-servicio="Netflix" data-plan="Estándar con anuncios" data-precio="14900">
                        <div class="product-card__platform">Netflix</div>
                        <div class="product-card__pts">Estándar</div>
                        <div class="product-card__label">Con anuncios · Full HD · 2 pantallas</div>
                        <div class="product-card__price">14.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="2" data-servicio="Netflix" data-plan="Estándar" data-precio="26900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Netflix</div>
                        <div class="product-card__pts">Estándar</div>
                        <div class="product-card__label">Sin anuncios · Full HD · 2 pantallas</div>
                        <div class="product-card__price">26.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="3" data-servicio="Netflix" data-plan="Premium" data-precio="36900">
                        <div class="product-card__platform">Netflix</div>
                        <div class="product-card__pts">Premium</div>
                        <div class="product-card__label">4K Ultra HD · 4 pantallas · Dolby</div>
                        <div class="product-card__price">36.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

            <!-- PARAMOUNT+ -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=paramountplus.com&sz=32" alt="Paramount+" style="width:24px;height:24px;border-radius:4px;">
                    <span>Paramount+</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="4" data-servicio="Paramount+" data-plan="Essential" data-precio="12900">
                        <div class="product-card__platform">Paramount+</div>
                        <div class="product-card__pts">Essential</div>
                        <div class="product-card__label">Con anuncios · HD · 3 pantallas</div>
                        <div class="product-card__price">12.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="5" data-servicio="Paramount+" data-plan="Showtime" data-precio="22900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Paramount+</div>
                        <div class="product-card__pts">Showtime</div>
                        <div class="product-card__label">Sin anuncios · 4K · Showtime incluido</div>
                        <div class="product-card__price">22.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

            <!-- DAZN -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=dazn.com&sz=32" alt="DAZN" style="width:24px;height:24px;border-radius:4px;">
                    <span>DAZN</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="6" data-servicio="DAZN" data-plan="Estándar" data-precio="19900">
                        <div class="product-card__platform">DAZN</div>
                        <div class="product-card__pts">Estándar</div>
                        <div class="product-card__label">Deportes en vivo · HD · 1 pantalla</div>
                        <div class="product-card__price">19.900 COP <span class="sub-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="7" data-servicio="DAZN" data-plan="Premium" data-precio="34900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">DAZN</div>
                        <div class="product-card__pts">Premium</div>
                        <div class="product-card__label">Deportes en vivo · 4K · 4 pantallas</div>
                        <div class="product-card__price">34.900 COP <span class="sub-tag">/ mes</span></div>
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

            </div>

        </section>

        <!-- CHECKOUT -->
        <aside class="checkout-panel">
            <div class="checkout-box">
                <div class="checkout-product-name" id="checkoutName">📺 Netflix — Estándar</div>
                <div class="checkout-price-row">
                    <span style="font-size:0.85rem;color:var(--text-secondary);">Total / mes</span>
                    <span class="checkout-price" id="checkoutPrice">26.900 COP</span>
                </div>

                <div class="checkout-divider"></div>

                <!-- Tabs método pago -->
                <div class="payment-tabs">
                    <button class="payment-tab active" id="tabTarjeta" onclick="setPayment('tarjeta')">
                        <i class="bi bi-credit-card-fill"></i> Tarjeta
                    </button>
                    <button class="payment-tab" id="tabPSE" onclick="setPayment('pse')">
                        <i class="bi bi-bank2"></i> PSE
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
                        <input type="email" class="field-input" id="cardCorreo" value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Teléfono</label>
                        <input type="text" class="field-input" id="cardTelefono" placeholder="3001234567">
                    </div>
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">Tipo de documento</label>
                            <select class="field-input" id="cardTipoDoc">
                                <option value="CC">Cédula</option>
                                <option value="CE">Cédula Extranjería</option>
                                <option value="NIT">NIT</option>
                                <option value="PP">Pasaporte</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Número de documento</label>
                            <input type="text" class="field-input" id="cardNumDoc" placeholder="1234567890">
                        </div>
                    </div>
                    <!-- Checkbox guardar tarjeta -->
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.8rem;padding:0.7rem 0.8rem;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:8px;">
                        <input type="checkbox" id="guardarTarjeta" style="width:16px;height:16px;accent-color:#f59e0b;cursor:pointer;flex-shrink:0;">
                        <label for="guardarTarjeta" style="font-size:0.82rem;color:#fbbf24;cursor:pointer;line-height:1.4;margin:0;">
                            🔐 Guardar tarjeta para futuros cobros automáticos
                        </label>
                    </div>
                </div>

                <!-- FORMULARIO PSE -->
                <div class="form-section" id="formPSE">
                    <div style="display:flex;gap:0.5rem;align-items:flex-start;background:rgba(240,180,41,0.08);border:1px solid rgba(240,180,41,0.25);border-radius:8px;padding:0.65rem 0.8rem;margin-bottom:0.8rem;font-size:0.78rem;color:#fbbf24;line-height:1.5;">
                        <i class="bi bi-info-circle-fill" style="margin-top:0.1rem;flex-shrink:0;"></i>
                        <span><strong>PSE no guarda tu método de pago.</strong> Los cobros automáticos del mes siguiente requieren tarjeta. Si pagas con PSE, deberás renovar manualmente cada mes.</span>
                    </div>
                    <span class="section-label-sm">Datos bancarios (PSE)</span>
                    <div class="field-group">
                        <label class="field-label">Banco</label>
                        <select class="field-input" id="pseBanco">
                            <option value="BANCOLOMBIA">Bancolombia</option>
                            <option value="NEQUI">Nequi</option>
                            <option value="DAVIVIENDA">Davivienda</option>
                            <option value="BBVA">BBVA</option>
                            <option value="BOGOTA">Banco de Bogotá</option>
                            <option value="OCCIDENTE">Banco de Occidente</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Tipo de persona</label>
                        <select class="field-input" id="pseTipoPersona">
                            <option value="N">Natural</option>
                            <option value="J">Jurídica</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Nombre completo</label>
                        <input type="text" class="field-input" id="pseNombre" placeholder="Nombre y apellido">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Correo electrónico</label>
                        <input type="email" class="field-input" id="pseCorreo" value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Teléfono</label>
                        <input type="text" class="field-input" id="pseTelefono" placeholder="3001234567">
                    </div>
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">Tipo de documento</label>
                            <select class="field-input" id="pseTipoDoc">
                                <option value="CC">Cédula</option>
                                <option value="CE">Cédula Extranjería</option>
                                <option value="NIT">NIT</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Número de documento</label>
                            <input type="text" class="field-input" id="pseNumDoc" placeholder="1234567890">
                        </div>
                    </div>
                </div>

                <button class="btn-pagar" id="btnPagar">
                    <i class="bi bi-lock-fill"></i> Suscribirse ahora
                </button>
                <div class="security-note">
                    <i class="bi bi-shield-check"></i>
                    Pago seguro · API Gateway · Evertec PlacetoPay
                </div>
            </div>
        </aside>
    </main>

    <input type="hidden" id="currentPayment" value="tarjeta">

    <script>
    (function() {
        const products = {
            1:{name:' Netflix — Estándar con anuncios',servicio:'Netflix',plan:'Estándar con anuncios',precio:14900,price:'14.900 COP'},
            2:{name:' Netflix — Estándar',servicio:'Netflix',plan:'Estándar',precio:26900,price:'26.900 COP'},
            3:{name:' Netflix — Premium',servicio:'Netflix',plan:'Premium',precio:36900,price:'36.900 COP'},
            4:{name:' Paramount+ — Essential',servicio:'Paramount+',plan:'Essential',precio:12900,price:'12.900 COP'},
            5:{name:' Paramount+ — Showtime',servicio:'Paramount+',plan:'Showtime',precio:22900,price:'22.900 COP'},
            6:{name:' DAZN — Estándar',servicio:'DAZN',plan:'Estándar',precio:19900,price:'19.900 COP'},
            7:{name:' DAZN — Premium',servicio:'DAZN',plan:'Premium',precio:34900,price:'34.900 COP'},
        };

        function updateCheckout(id) {
            const p = products[id];
            if (!p) return;
            document.getElementById('checkoutName').textContent  = p.name;
            document.getElementById('checkoutPrice').textContent = p.price;
        }

        window.setPayment = function(method) {
            document.getElementById('currentPayment').value = method;
            document.getElementById('tabTarjeta').classList.toggle('active', method === 'tarjeta');
            document.getElementById('tabPSE').classList.toggle('active', method === 'pse');
            document.getElementById('formTarjeta').classList.toggle('active', method === 'tarjeta');
            document.getElementById('formPSE').classList.toggle('active', method === 'pse');
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
            var def = document.querySelector('.product-card[data-id="2"]');
            if (def) { def.classList.add('selected'); updateCheckout(2); }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCards);
        } else { initCards(); }

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
            if (envioEnCurso) return; // ya se está procesando, ignorar clics repetidos

            const selected = document.querySelector('.product-card.selected');
            if (!selected) { alert('⚠️ Selecciona un plan primero.'); return; }

            const method = document.getElementById('currentPayment').value;
            const id = parseInt(selected.getAttribute('data-id'));
            const p  = products[id];

            let nombre, correo, telefono, tipoDoc, numDoc;

            if (method === 'tarjeta') {
                nombre   = document.getElementById('cardName').value.trim();
                correo   = document.getElementById('cardCorreo').value.trim();
                telefono = document.getElementById('cardTelefono').value.trim();
                tipoDoc  = document.getElementById('cardTipoDoc').value;
                numDoc   = document.getElementById('cardNumDoc').value.trim();
                const cardNum = document.getElementById('cardNumber').value.replace(/\s/g,'');
                const cvv     = document.getElementById('cardCvv').value;
                if (!nombre || !correo || !numDoc || !telefono || !cardNum || !cvv) {
                    alert('⚠️ Por favor completa todos los campos de tarjeta.'); return;
                }
            } else {
                nombre   = document.getElementById('pseNombre').value.trim();
                correo   = document.getElementById('pseCorreo').value.trim();
                telefono = document.getElementById('pseTelefono').value.trim();
                tipoDoc  = document.getElementById('pseTipoDoc').value;
                numDoc   = document.getElementById('pseNumDoc').value.trim();
                if (!nombre || !correo || !numDoc || !telefono) {
                    alert('⚠️ Por favor completa todos los campos de PSE.'); return;
                }
            }

            const form = document.createElement("form");
            form.method = 'POST';
            form.action = (modoSimulacion === 'auto')
                ? '../../php/crear_suscripciones_gateway.php'
                : '../../retorno/estados-subs-gateway.php';

            const campos = [
                ['servicio', p.servicio], ['plan', p.plan], ['precio', p.precio],
                ['nombre', nombre], ['correo', correo], ['telefono', telefono],
                ['tipo_doc', tipoDoc], ['num_doc', numDoc], ['metodo', method],
                ['guardar_tarjeta', document.getElementById('guardarTarjeta').checked ? '1' : '0']
            ];

            if (method === 'tarjeta') {
                campos.push(
                    ['card_number', document.getElementById('cardNumber').value.replace(/\s/g,'')],
                    ['card_expiry', document.getElementById('cardExpiry').value],
                    ['card_cvv',    document.getElementById('cardCvv').value],
                    ['card_name',   document.getElementById('cardName').value]
                );
            } else {
                campos.push(
                    ['cuenta_banco',    document.getElementById('pseBanco').value],
                    ['tipo_persona',    document.getElementById('pseTipoPersona').value]
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