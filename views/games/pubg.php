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
    <title>PUBG — UC Points</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php $theme_seccion = 'juegos'; require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    :root {
        --bg-base:        #0d0e10;
        --bg-surface:     #16181c;
        --bg-card:        #1e2128;
        --bg-card-hover:  #252830;
        --bg-selected:    #0a1520;
        --border:         #2e3038;
        --accent:         hsl(38, 100%, 72%);
        --accent-glow:    rgba(245,158,11,0.25);
        --accent-dark:    rgb(255, 208, 0);
        --text-primary:   #f0f1f3;
        --text-secondary: #8a8d96;
        --text-muted:     #555860;
        --green:          #3ecf8e;
        --font-display:   'Calibri', sans-serif;
        --font-body:      'Calibri', sans-serif;
        --radius-sm:      6px;
        --radius-md:      10px;
        --radius-lg:      14px;
        --transition:     0.2s ease;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { background-color: var(--bg-base); color: var(--text-primary); font-family: var(--font-body); min-height: 100vh; -webkit-font-smoothing: antialiased; }
    .navbar { background-color: #0f0f0fa9 !important; backdrop-filter: blur(8px); border-bottom: 1px solid var(--pt-border); }

    .game-banner {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.6rem 2rem; background: var(--pt-th2);
        border-bottom: 1px solid var(--pt-border); gap: 1rem;
    }
        .game-banner__tag {
        display: flex; align-items: center; gap: 0.5rem;
        font-family: var(--font-display); font-weight: 700;
        font-size: 1rem; letter-spacing: 0.04em; color: var(--pt-text);
    }
    .gw-badge {
        background: rgba(245,158,11,0.15); color: var(--accent);
        font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.6rem;
        border-radius: 20px; letter-spacing: 0.05em; font-family: var(--font-display);
    }
    .card-img-top {
      border-radius: 15px 15px 0 0;
      height: 20px;
      width: 10%;
      object-fit: cover;
    }

    /* ID jugador en banner */
    .banner-player-id { display: flex; align-items: center; gap: 0.5rem; }
    .banner-player-id label { font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); white-space: nowrap; }
    .banner-player-id input {
        background: var(--pt-border); border: 1.5px solid var(--pt-border);
        border-radius: var(--radius-sm); color: var(--pt-text);
        font-family: var(--font-body); font-size: 0.85rem;
        padding: 0.35rem 0.75rem; outline: none;
        transition: border-color var(--transition); width: 180px;
    }
    .banner-player-id input::placeholder { color: var(--text-muted); }
    .banner-player-id input:focus { border-color: var(--accent); }

    .shop-layout {
        display: grid; grid-template-columns: 1fr 360px;
        gap: 1.5rem; max-width: 1200px;
        margin: 1.5rem auto; padding: 0 1.5rem 3rem; align-items: start;
    }

    .section-block { margin-bottom: 1.4rem; }
    .section-label {
        font-family: var(--font-display); font-size: 0.8rem;
        font-weight: 700; letter-spacing: 0.1em;
        text-transform: uppercase; color: var(--text-secondary); margin-bottom: 0.75rem;
    }

    .products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.65rem; }

    .product-card {
        position: relative; background: var(--pt-navbar);
        border: 1.5px solid var(--pt-border); border-radius: var(--radius-md);
        padding: 0.9rem 0.75rem 0.8rem; cursor: pointer;
        transition: all 0.18s ease; display: flex;
        flex-direction: column; gap: 0.1rem; overflow: hidden;
    }
    .product-card:hover { background: var(--pt-boxitem); border-color: rgba(245,158,11,0.4); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.35); }
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
    .product-card__img { font-size: 1.5rem; margin-bottom: 0.25rem; }
    .product-card__pts { font-family: var(--font-display); font-size: 1.35rem; font-weight: 800; color: var(--pt-text-sec); line-height: 1; }
    .product-card__label { font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.3rem; }
    .product-card__price-old { font-size: 0.72rem; color: var(--text-muted); text-decoration: line-through; }
    .product-card__price { font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--accent); display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; }
    .discount-tag { background: rgba(245,158,11,0.15); color: var(--accent); font-size: 0.68rem; font-weight: 700; padding: 0.1rem 0.3rem; border-radius: 3px; }

    /* CHECKOUT */
    .checkout-panel { display: flex; flex-direction: column; gap: 1rem; position: sticky; top: 16px; }
    .checkout-box { background: var(--pt-navbar); border: 1px solid var(--pt-border); border-radius: var(--radius-lg); padding: 1.2rem 1.3rem; }

    .checkout-product-name { font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: var(--pt-text); margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.6rem; }
    .checkout-product-name img { width: 32px; height: 32px; object-fit: contain; flex-shrink: 0; }
    .checkout-product-name img[src=""], .checkout-product-name img:not([src]) { display: none; }
    .checkout-price-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; }
    .checkout-price { font-family: var(--font-display); font-size: 1.6rem; font-weight: 800; color: var(--pt-text); }
    .checkout-divider { height: 1px; background: var(--pt-border); margin: 0.8rem 0; }

    /* Método de pago tabs */
    .payment-tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
    .payment-tab {
        flex: 1; padding: 0.5rem; border: 1.5px solid var(--pt-border);
        border-radius: var(--radius-sm); background: var(--pt-bg);
        color: var(--pt-text-sec); font-family: var(--font-body);
        font-size: 0.82rem; font-weight: 600; cursor: pointer;
        transition: all 0.2s; text-align: center;
        display: flex; align-items: center; justify-content: center; gap: 0.3rem;
    }
    .payment-tab:hover { border-color: var(--accent); color: var(--text-primary); }
    .payment-tab.active { border-color: var(--accent); background: rgba(245,158,11,0.1); color: var(--accent); }

    /* Form fields */
    .form-section { display: none; }
    .form-section.active { display: block; }

    .field-group { margin-bottom: 0.75rem; }
    .field-label { font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.3rem; display: block; }
    .field-input {
        width: 100%; background: var(--pt-border); border: 1.5px solid var(--pt-border);
        border-radius: 8px; color: var(--pt-text); font-family: var(--font-body);
        font-size: 0.85rem; padding: 0.45rem 0.75rem; outline: none;
        transition: border-color 0.2s;
    }
    .field-input:focus { border-color: var(--accent); }
    .field-input::placeholder { color: var(--text-muted); }
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }

    .btn-pagar {
        width: 100%; margin-top: 0.8rem; padding: 0.85rem;
        background: var(--accent); border: none; border-radius: var(--radius-md);
        color: #0a0a0b; font-family: var(--font-display); font-size: 1.1rem;
        font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase;
        cursor: pointer; transition: all 0.18s ease; display: flex;
        align-items: center; justify-content: center; gap: 0.5rem;
    }
    .btn-pagar:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }

    /* Modo de simulación */
    .sim-mode-wrap { margin-top: 0.9rem; }
    .sim-mode-label {
        font-family: var(--font-display); font-size: 0.72rem; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: var(--text-secondary); margin-bottom: 0.45rem; display: block;
    }
    .sim-mode-toggle {
        display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem;
        background: var(--pt-navbar); border: 1.5px solid var(--pt-border);
        border-radius: var(--radius-md); padding: 0.3rem;
    }
    .sim-mode-opt {
        border: none; background: transparent; cursor: pointer;
        padding: 0.55rem 0.4rem; border-radius: var(--radius-sm);
        font-family: var(--font-body); font-size: 0.8rem; font-weight: 600;
        color: var(--text-secondary); transition: all 0.18s ease;
        display: flex; align-items: center; justify-content: center; gap: 0.35rem;
    }
    .sim-mode-opt:hover {background-color: rgba(255, 194, 89, 0.12); color: var(--pt-text-sec); }
    .sim-mode-opt.active { background: rgba(245,158,11,0.12); color: var(--accent); box-shadow: inset 0 0 0 1.5px var(--accent); }
    .sim-mode-hint { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.4rem; line-height: 1.4; }

    .security-note {
        display: flex; align-items: center; gap: 0.4rem;
        font-size: 0.75rem; color: var(--text-muted);
        margin-top: 0.6rem; justify-content: center;
    }

    /* Vendor */
    .vendor-box{
    background: var(--pt-navbar);
    border: solid var(--pt-border);
    border-radius: 10px;
    padding: 1.2rem 1.3rem;

    }
    .vendor-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 0.5rem;
    
    }

    .vendor-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffdb87, #ffc02c);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 0.85rem;
    color: var(--pt-text);
    flex-shrink: 0;
    }

    .vendor-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--pt-text-sec);
    }

    .vendor-rating {
    font-size: 0.78rem;
    color: #6d6d6d;
    margin-top: 0.1rem;
    }

    .security-warning{background:rgba(224,82,82,0.08);border-left:4px solid #e05252;border-radius:0 8px 8px 0;padding:0.9rem 1.2rem;margin:1rem 2rem;display:flex;gap:0.8rem;align-items:flex-start;font-size:0.83rem; color:var(--pt-text);line-height:1.6;}
    .security-warning i{color:#e05252;font-size:1.2rem;flex-shrink:0;margin-top:0.1rem;}
    .security-warning strong{color:#e05252;}
    .security-warning-header{display:flex;align-items:center;justify-content:space-between;width:100%;cursor:pointer;user-select:none;}
    .security-warning-toggle{color: #e05252 ;font-size:1rem;transition:transform 0.3s ease;margin-left:auto;padding-left:1rem;}
    .security-warning-toggle.collapsed{transform:rotate(-90deg);}
    .security-warning-content{margin-top:0.5rem;overflow:hidden;max-height:500px;transition:max-height 0.3s ease,opacity 0.3s ease;opacity:1;}
    .security-warning-content.collapsed{max-height:0;opacity:0;margin-top:0;}

    @keyframes fadeSlideIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }
    .products-panel { animation: fadeSlideIn 0.4s ease both; }
    .checkout-panel { animation: fadeSlideIn 0.4s 0.1s ease both; }

    @media (max-width: 900px) { .shop-layout { grid-template-columns: 1fr; } .checkout-panel { position: static; } .products-grid { grid-template-columns: repeat(3,1fr); } }
    @media (max-width: 600px) { .products-grid { grid-template-columns: repeat(2,1fr); } .game-banner { flex-direction: column; align-items: flex-start; } }
</style>
<body>
    <?php
    $nav_back_url  = "juegos.php";
    $nav_back_text = "Atras";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>
 
  <!-- ═══ GAME BANNER ═══ -->
  <div class="game-banner">
    <div class="game-banner__tag">
      <img src="https://img.redbull.com/images/c_limit,w_1500,h_1000/f_auto,q_auto/redbullcom/2018/02/13/c3c16515-d639-45cd-8d7d-5fe26623130b/pubg" class="card-img-top" alt="" class="game-icon" />
      PUBG — UF Points
      <span class="gw-badge"> API Gateway</span>
    </div>
    
    
    <div class="banner-player-id">
      <label for="jugadorIdInput">ID de jugador</label>
      <input type="text" id="jugadorIdInput" placeholder="Ej: 123456789" autocomplete="off" />
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
            <div class="section-block">
                <p class="section-label">Elige el importe</p>
                <div class="products-grid">

                    <div class="product-card" data-id="1" data-pts="60" data-price="4900" data-original="" data-discount="">
                        <img src="https://martsbd.com/wp-content/uploads/2023/04/PUBG-Mobile-UC-Station.png" style="height: 40px; width: 40px" alt="">
                        <div class="product-card__pts">60 UC</div>
                        <div class="product-card__label">UC Points</div>
                        <div class="product-card__price">4.900 COP</div>
                    </div>

                    <div class="product-card popular-card" data-id="2" data-pts="325" data-price="21900" data-original="28000" data-discount="21">
                        <div class="badge-popular">★ Popular</div>
                        <img src="https://martsbd.com/wp-content/uploads/2023/04/PUBG-Mobile-UC-Station.png" style="height: 40px; width: 40px" alt="">
                        <div class="product-card__pts">325 UC</div>
                        <div class="product-card__label">UC Points</div>
                        <div class="product-card__price-old">28.000 COP</div>
                        <div class="product-card__price">21.900 COP <span class="discount-tag">-21%</span></div>
                    </div>

                    <div class="product-card" data-id="3" data-pts="660" data-price="39900" data-original="52000" data-discount="23">
                        <img src="https://martsbd.com/wp-content/uploads/2023/04/PUBG-Mobile-UC-Station.png" style="height: 40px; width: 40px" alt="">
                        <div class="product-card__pts">660 UC</div>
                        <div class="product-card__label">UC Points</div>
                        <div class="product-card__price-old">52.000 COP</div>
                        <div class="product-card__price">39.900 COP <span class="discount-tag">-23%</span></div>
                    </div>

                    <div class="product-card" data-id="4" data-pts="1800" data-price="99900" data-original="135000" data-discount="26">
                        <img src="https://martsbd.com/wp-content/uploads/2023/04/PUBG-Mobile-UC-Station.png" style="height: 40px; width: 40px" alt="">
                        <div class="product-card__pts">1800 UC</div>
                        <div class="product-card__label">UC Points</div>
                        <div class="product-card__price-old">135.000 COP</div>
                        <div class="product-card__price">99.900 COP <span class="discount-tag">-26%</span></div>
                    </div>

                    <div class="product-card" data-id="5" data-pts="3850" data-price="189900" data-original="260000" data-discount="26">
                        <img src="https://martsbd.com/wp-content/uploads/2023/04/PUBG-Mobile-UC-Station.png" style="height: 40px; width: 40px" alt="">
                        <div class="product-card__pts">3850 UC</div>
                        <div class="product-card__label">UC Points</div>
                        <div class="product-card__price-old">260.000 COP</div>
                        <div class="product-card__price">189.900 COP <span class="discount-tag">-26%</span></div>
                    </div>
                    <div class="product-card" data-id="6" data-pts="8100" data-price="369900" data-original="500000" data-discount="26">
                        <img src="https://martsbd.com/wp-content/uploads/2023/04/PUBG-Mobile-UC-Station.png" style="height: 40px; width: 40px" alt="">
                        <div class="product-card__pts">8100 UC</div>
                        <div class="product-card__label">UC Points</div>
                        <div class="product-card__price-old">500.000 COP</div>
                        <div class="product-card__price">369.900 COP <span class="discount-tag">-26%</span></div>
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
            </div>
        </section>

        <!-- CHECKOUT CON FORMULARIO DE PAGO -->
        <aside class="checkout-panel">
            <div class="checkout-box">
                <div class="checkout-product-name"><img id="checkoutImg" src="" alt="" /><span id="checkoutName">💎 325 UC Points</span></div>
                <div class="checkout-price-row">
                    <span style="font-size:0.85rem;color:var(--text-secondary);">Total</span>
                    <span class="checkout-price" id="checkoutPrice">21.900 COP</span>
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
                        <input type="text" class="field-input" id="cardName" placeholder="Como aparece en la tarjeta" value="<?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?>">
                    </div>
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">Tipo de documento</label>
                            <select class="field-input" id="cardTipoDoc">
                                <option value="TI">Tarjeta de Identidad</option> 
                                <option value="CC">Cédula</option>
                                <option value="PP">Pasaporte</option>       
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
                    <div class="field-group">
                        <label class="field-label">Correo electrónico</label>
                        <input type="email" class="field-input" id="cardCorreo" placeholder="correo@ejemplo.com"
                               value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Teléfono</label>
                        <input type="text" class="field-input" id="cardTelefono" placeholder="3001234567">
                    </div>
                </div>

                <!-- FORMULARIO CUENTA -->
                <div class="form-section" id="formCuenta">
                    <div class="field-group">
                        <label class="field-label">Banco</label>
                        <select class="field-input" id="cuentaBanco">
                            <option value="BANCOLOMBIA">Bancolombia</option>
                            <option value="NEQUI">Nequi</option>
                            <option value="DAVIVIENDA">Davivienda</option>
                            <option value="BBVA">BBVA</option>
                            <option value="BOGOTA">Banco de Bogotá</option>
                            <option value="OCCIDENTE">Banco de Occidente</option>
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
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label">Tipo de documento</label>
                            <select class="field-input" id="cuentaTipoDoc">
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="CC">Cédula</option>
                                <option value="PP">Pasaporte</option>  
                                <option value="CE">Cédula Extranjería</option>
                                <option value="NIT">NIT</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Número de documento</label>
                            <input type="text" class="field-input" id="cuentaNumDoc" placeholder="1234567890">
                        </div>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Nombre completo</label>
                        <input type="text" class="field-input" id="cuentaNombre" placeholder="Nombre y apellido">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Correo electrónico</label>
                        <input type="email" class="field-input" id="cuentaCorreo" placeholder="correo@ejemplo.com"
                               value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>">
                    </div>
                    <div class="field-group">
                        <label class="field-label">Teléfono</label>
                        <input type="text" class="field-input" id="cuentaTelefono" placeholder="3001234567">
                    </div>
                </div>

                <button class="btn-pagar" id="btnPagar">
                    <i class="bi bi-lock-fill"></i> Pagar ahora
                </button>

                <div class="security-note">
                    <i class="bi bi-shield-check"></i>
                    API Gateway · Evertec PlacetoPay
                </div>
                
            </div>
            <div class="vendor-box">
             <p class="section-label">Designer</p>
                <div class="vendor-info">
                <div class="vendor-avatar">JM</div>
                <div>
                    <div class="vendor-name">Jair ✅</div>
                    <div class="vendor-rating">👍 2026 · <a href="#" style="color: rgb(255, 225, 128);">Evertec Placetopay SAS</a></div>
                </div>
            </div>
        </div>
        </aside>
    </main>

    <input type="hidden" id="currentPayment" value="tarjeta">

    <script>
    (function() {
        const products = {
            1: { name: ' 60 UC Points',   price: '4.900 COP',   precio: 4900   },
            2: { name: ' 325 UC Points',  price: '21.900 COP',  precio: 21900  },
            3: { name: ' 660 UC Points',  price: '39.900 COP',  precio: 39900  },
            4: { name: ' 1800 UC Points', price: '99.900 COP',  precio: 99900  },
            5: { name: ' 3850 UC Points', price: '189.900 COP', precio: 189900 },
            6: { name: ' 8100 UC Points', price: '369.900 COP', precio: 369900 },
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

        window.setPayment = function(method) {
            document.getElementById('currentPayment').value = method;
            document.getElementById('tabTarjeta').classList.toggle('active', method === 'tarjeta');
            document.getElementById('tabCuenta').classList.toggle('active', method === 'cuenta');
            document.getElementById('formTarjeta').classList.toggle('active', method === 'tarjeta');
            document.getElementById('formCuenta').classList.toggle('active', method === 'cuenta');
        };

        // Formatear número de tarjeta
        document.getElementById('cardNumber').addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '').substring(0,16);
            this.value = v.replace(/(.{4})/g, '$1 ').trim();
        });

        // Formatear fecha
        document.getElementById('cardExpiry').addEventListener('input', function() {
            let v = this.value.replace(/\D/g, '').substring(0,4);
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
                ? 'Elige manualmente cómo termina la transacción.'
                : 'El estado se asigna automáticamente, como un pago real.';
        };
         let envioEnCurso = false;
        document.getElementById('btnPagar').addEventListener('click', function() {
            if (envioEnCurso) return; // ya se está procesando, ignorar clics repetidos
            const jugadorId = document.getElementById('jugadorIdInput').value.trim();
            if (!jugadorId) { alert('⚠️ Por favor ingresa tu ID de jugador.'); return; }

            const selectedCard = document.querySelector('.product-card.selected');
            if (!selectedCard) { alert('⚠️ Selecciona un producto.'); return; }

            const method  = document.getElementById('currentPayment').value;
            const producto = document.getElementById('checkoutName').textContent.trim();
            const precio   = document.getElementById('checkoutPrice').textContent.replace(/[^0-9]/g, '');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = (modoSimulacion === 'auto')
                ? '../../php/crear_pb_gateway.php'
                : '../../retorno/estados-gateway.php';

            const campos = [
                ['producto', producto], ['precio', precio],
                ['jugador_id', jugadorId], ['metodo', method]
            ];

            if (method === 'tarjeta') {
                const cardNum = document.getElementById('cardNumber').value.replace(/\s/g,'');
                const expiry  = document.getElementById('cardExpiry').value;
                const cvv     = document.getElementById('cardCvv').value;
                const name    = document.getElementById('cardName').value;
                const tipoDoc = document.getElementById('cardTipoDoc').value;
                const numDoc  = document.getElementById('cardNumDoc').value;
                const correo  = document.getElementById('cardCorreo').value;
                const tel     = document.getElementById('cardTelefono').value;

                if (!cardNum || !expiry || !cvv || !name || !numDoc || !correo || !tel) {
                    alert('⚠️ Por favor completa todos los campos de tarjeta.'); return;
                }
                campos.push(
                    ['card_number', cardNum], ['card_expiry', expiry],
                    ['card_cvv', cvv], ['card_name', name],
                    ['tipo_doc', tipoDoc], ['num_doc', numDoc],
                    ['correo', correo], ['telefono', tel]
                );
            } else {
                const banco   = document.getElementById('cuentaBanco').value;
                const tipo    = document.getElementById('cuentaTipo').value;
                const numero  = document.getElementById('cuentaNumero').value;
                const tipoDoc = document.getElementById('cuentaTipoDoc').value;
                const numDoc  = document.getElementById('cuentaNumDoc').value;
                const nombre  = document.getElementById('cuentaNombre').value;
                const correo  = document.getElementById('cuentaCorreo').value;
                const tel     = document.getElementById('cuentaTelefono').value;

                if (!numero || !numDoc || !nombre || !correo || !tel) {
                    alert('⚠️ Por favor completa todos los campos de cuenta.'); return;
                }
                campos.push(
                    ['banco', banco], ['tipo_cuenta', tipo],
                    ['num_cuenta', numero], ['tipo_doc', tipoDoc],
                    ['num_doc', numDoc], ['nombre', nombre],
                    ['correo', correo], ['telefono', tel],
                    ['cuenta_banco', banco]
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