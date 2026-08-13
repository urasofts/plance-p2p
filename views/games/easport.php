<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EA FC Productos</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Tu CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
        /* ════════════════════════════════════════
    DEMO STORE — styles.css
    Dark Gaming Theme · Barlow Condensed
    ════════════════════════════════════════ */

    :root {
    --bg-base:        #18191b;
    --bg-surface:     #16181c;
    --bg-card:        #1e2128;
    --bg-card-hover:  #252830;
    --bg-selected:    #08282a;
    --border:         #2e3038;
    --border-active:  #c3ffdeb2;
    --accent:         rgb(0, 255, 106);
    --accent-glow:    rgba(41, 240, 197, 0.25);
    --accent-dark:    #00af43;
    --text-primary:   #f0f1f3;
    --text-secondary: #8a8d96;
    --text-muted:     #555860;
    --green:          #97eaff;
    --red-badge:      #e05252;
    --font-display:   'Calibri',  sans-serif;
    --font-body:      'Calibri', sans-serif;
    --radius-sm:      6px;
    --radius-md:      10px;
    --radius-lg:      14px;
    --transition:     0.2s ease;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html { font-size: 16px; scroll-behavior: smooth; }

    body {
    /*  background-image: url(../assets/images/bg25.jpg); */
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
    color: var(--pt-text);
    }
    .game-icon { width: 20px; height: 20px; border-radius: 4px; }



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

    /* ── SECTION HELPERS ── */
    .section-block { margin-bottom: 1.4rem; }
    .section-label {
    font-family: var(--font-display);
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--pt-text-sec);
    margin-bottom: 0.75rem;
    }



    /* ── PRODUCTS GRID ── */
    .products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.65rem;
    }

    .product-card {
    position: relative;
    background: var(--pt-navbar);
    border: 1.5px solid var(--pt-border);
    border-radius: var(--radius-md);
    padding: 0.9rem 0.75rem 0.8rem;
    cursor: pointer;
    transition: all 0.18s ease;
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    overflow: hidden;
    }

    .card-img-top {
      border-radius: 15px 15px 0 0;
      height: 20px;
      width: 10%;
      object-fit: cover;
    }

    .product-card:hover {
    background: var(--pt-boxitem);
    border-color: rgba(240, 180, 41, 0.4);
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
    color: var(--pt-text);
    font-family: var(--font-display);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    padding: 0.15rem 0.5rem;
    border-radius: var(--radius-sm) 0 var(--radius-sm) 0;
    }

    .product-card__img {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
    }

    .product-card__pts {
    font-family: var(--font-display);
    font-size: 1.00rem;
    font-weight: 700;
    color: var(--pt-text);
    line-height: 1;
    }

    .product-card__label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 0.3rem;
    }

    .product-card__price-old {
    font-size: 0.72rem;
    color: var(--text-muted);
    text-decoration: line-through;
    }

    .product-card__price {
    font-family: var(--font-body);
    font-size: 1.0rem;
    font-weight: 700;
    color: var(--accent);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
    }

    .discount-tag {
    background: rgba(240, 180, 41, 0.15);
    color: var(--accent);
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.1rem 0.3rem;
    border-radius: 3px;
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
    background: var(--pt-navbar);
    border: 1px solid var(--pt-border);
    border-radius: var(--radius-lg);
    padding: 1.2rem 1.3rem;
    }

    .checkout-product-name {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--pt-text);
    margin-bottom: 1rem;
    letter-spacing: 0.02em;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    }

    .checkout-product-name img {
    width: 34px;
    height: 34px;
    object-fit: contain;
    flex-shrink: 0;
    }
    .checkout-product-name img[src=""],
    .checkout-product-name img:not([src]) { display: none; }

    .checkout-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.65rem;
    }

    .checkout-label {
    font-size: 0.85rem;
    color: var(--pt-text);
    }

    .checkout-delivery {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--pt-text-sec);
    }

    .checkout-divider {
    height: 1px;
    background: var(--pt-border);
    margin: 0.8rem 0;
    }

    .checkout-total-row {
    align-items: flex-end;
    }

    .checkout-pricing {
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    }

    .checkout-original {
    font-size: 0.8rem;
    color: var(--pt-text);
    text-decoration: line-through;
    }

    .checkout-final-row {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    }

    .checkout-badge {
    background: rgba(62, 207, 142, 0.15);
    color: var(--green);
    font-size: 0.75rem;
    font-weight: 700;
    font-family: var(--font-display);
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    }

    .checkout-final-price {
    font-family: var(--font-display);
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--pt-text-sec);
    line-height: 1;
    }

    /* Buy Button */
    .btn-buy {
    width: 100%;
    margin-top: 1rem;
    padding: 0.85rem 1.2rem;
    background: var(--accent);
    border: none;
    border-radius: var(--radius-md);
    color: #0a0a0b;
    font-family: var(--font-display);
    font-size: 1.1rem;
    font-weight: 800;
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

    .btn-buy:hover {
    background: var(--accent-dark);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px var(--accent-glow);
    }
    .btn-buy:hover::before { transform: translateX(100%); }
    .btn-buy:active { transform: translateY(0); }

    .btn-arrow {
    font-size: 1.1rem;
    transition: transform 0.2s;
    }
    .btn-buy:hover .btn-arrow { transform: translateX(4px); }

    /* Trust badges */
    .trust-badges {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    }

    .trust-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--text-secondary);
    }

    /* Delivery instructions */
    .instruction-text {
    font-size: 0.83rem;
    color: var(--text-secondary);
    line-height: 1.7;
    margin-bottom: 0.75rem;
    }

    .flag { margin-right: 0.2rem; }

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
    .btn-instructions:hover {
    border-color: var(--accent);
    color: var(--text-primary);
    }

    /* Vendor */
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
    background: linear-gradient(135deg, #f0b429, #c99010);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 0.85rem;
    color: #0d0e10;
    flex-shrink: 0;
    }

    .vendor-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--text-primary);
    }

    .vendor-rating {
    font-size: 0.78rem;
    color: var(--text-secondary);
    margin-top: 0.1rem;
    }

    /* ── BANNER PLAYER ID ── */
    .banner-player-id {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    }

    .banner-player-id label {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text-secondary);
    white-space: nowrap;
    }

    .banner-player-id input {
    background: var(--bg-card);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-family: var(--font-body);
    font-size: 0.85rem;
    padding: 0.35rem 0.75rem;
    outline: none;
    transition: border-color var(--transition);
    width: 180px;
    }

    .banner-player-id input::placeholder { color: var(--text-muted); }
    .banner-player-id input:focus { border-color: var(--accent); }

    /* ── ANIMATIONS ── */
    @keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
    }

    .products-panel { animation: fadeSlideIn 0.4s ease both; }
    .checkout-panel { animation: fadeSlideIn 0.4s 0.1s ease both; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
    .shop-layout {
        grid-template-columns: 1fr;
    }
    .checkout-panel {
        position: static;
    }
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    }

    @media (max-width: 600px) {
    .products-grid { grid-template-columns: repeat(2, 1fr); }
    .navbar__links { display: none; }
    .game-banner { flex-direction: column; align-items: flex-start; }
    }
    
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
      <img src="https://media.es.wired.com/photos/64dad651532fc59e0e8d53a4/16:9/w_1280,c_limit/EA%20Sports.jpg" class="card-img-top" alt="" class="game-icon">
      EA Sports FC Points
    </div>
    <div class="banner-player-id">
      <label for="jugadorIdInput"><i class="bi bi-person-vcard-fill fs-6 text-warning"></i> ID de jugador</label>
      <input type="text" id="jugadorIdInput" placeholder="Ej: 0011122224444555" autocomplete="off" />
    </div>
  </div>

  <!-- ═══ MAIN LAYOUT ═══ -->
  <main class="shop-layout">

    <!-- LEFT: Products Panel -->
    <section class="products-panel">

      <!-- Products Grid -->
      <div class="section-block">
        <p class="section-label">Elige el importe</p>
        <div class="products-grid" id="productsGrid">

          <div class="product-card" data-id="1" data-pts="200" data-price="6500" data-original="" data-discount="">
            <img src="https://pbs.twimg.com/media/F07sn5UWYBQZ4jB.png" class="moneda1" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">100</div>
            <div class="product-card__label">Puntos FC</div>
            <div class="product-card__price">6500 COP</div>
          </div>

          <div class="product-card popular-card" data-id="2" data-pts="500" data-price="14000" data-original="18000" data-discount="32">
            <div class="badge-popular">★ Popular</div>
            <img src="https://cdn1.codashop.com/images/826_219b1202-df20-4fe8-bfcb-d2689d381d31_EA%20SPORTS%20FC%20Mobile_category/1705583704508_40-FC-Points%20(1).png" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">500</div>
            <div class="product-card__label">Puntos FC</div>
            <div class="product-card__price-old">18.000 COP</div>
            <div class="product-card__price">14.000 COP <span class="discount-tag">-32%</span></div>
          </div>

          <div class="product-card" data-id="3" data-pts="1100" data-price="25500" data-original="31000" data-discount="31">
            <img src="https://cdn1.codashop.com/images/826_219b1202-df20-4fe8-bfcb-d2689d381d31_EA%20SPORTS%20FC%20Mobile_category/1705583704508_40-FC-Points%20(1).png" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">1100</div>
            <div class="product-card__label">Puntos FC</div>
            <div class="product-card__price-old">31.000 COP</div>
            <div class="product-card__price">25.500 COP <span class="discount-tag">-31%</span></div>
          </div>

          <div class="product-card" data-id="4" data-pts="2200" data-price="36000" data-original="44000" data-discount="61">
            <img src="https://cdn1.codashop.com/images/826_219b1202-df20-4fe8-bfcb-d2689d381d31_EA%20SPORTS%20FC%20Mobile_category/1705583704508_40-FC-Points%20(1).png" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">2200</div>
            <div class="product-card__label">Puntos FC</div>
            <div class="product-card__price-old">44.000 COP</div>
            <div class="product-card__price">36.000 COP <span class="discount-tag">-61%</span></div>
          </div>
          <div class="product-card" data-id="5" data-pts="5000" data-price="42500" data-original="56900" data-discount="62">
            <img src="https://cdn1.codashop.com/images/1811_219b1202-df20-4fe8-bfcb-d2689d381d31_224713e22083a9e37572a9991d934d42_image/579606ef8775b5142968d05c21578c4c.webp" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">5000</div>
            <div class="product-card__label">Puntos FC</div>
            <div class="product-card__price-old">56.900 COP</div>
            <div class="product-card__price">42.500 COP <span class="discount-tag">-62%</span></div>
          </div>

          <div class="product-card" data-id="6" data-pts="9500" data-price="72000" data-original="85400" data-discount="67">
            <img src="https://cdn1.codashop.com/images/1811_219b1202-df20-4fe8-bfcb-d2689d381d31_224713e22083a9e37572a9991d934d42_image/579606ef8775b5142968d05c21578c4c.webp" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">9500</div>
            <div class="product-card__label">Puntos FC</div>
            <div class="product-card__price-old">85.400 COP</div>
            <div class="product-card__price">72.000 COP <span class="discount-tag">-67%</span></div>
          </div>

          <div class="product-card" data-id="7" data-pts="12500" data-price="90000" data-original="120500" data-discount="63">
            <img src="https://cdn1.codashop.com/images/1811_219b1202-df20-4fe8-bfcb-d2689d381d31_224713e22083a9e37572a9991d934d42_image/579606ef8775b5142968d05c21578c4c.webp" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">12500</div>
            <div class="product-card__label">Puntos FC</div>
            <div class="product-card__price-old">120.500 COP</div>
            <div class="product-card__price">90.000 COP <span class="discount-tag">-63%</span></div>
          </div>

          <div class="product-card battlepass-card" data-id="8" data-pts="Star Pass" data-price="18.500" data-original="29.000" data-discount="7">
            <img src="https://cdn1.codashop.com/images/1811_219b1202-df20-4fe8-bfcb-d2689d381d31_224713e22083a9e37572a9991d934d42_category/1765872198506_c3dfa8dd-cd10-4179-aaae-6aed47aa2962.webp" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts" style="font-size:0.85rem;">Star Pass</div>
            <div class="product-card__label">Pass</div>
            <div class="product-card__price-old">29.000 COP</div>
            <div class="product-card__price">18.500 COP <span class="discount-tag">-7%</span></div>
          </div>

        </div>
      </div>
    </section>

    <!-- RIGHT: Checkout Panel -->
    <aside class="checkout-panel" id="checkoutPanel">
      
      <div class="checkout-summary">
        <div class="checkout-product-name">
          <img id="checkoutImg" src="https://cdn1.codashop.com/images/826_219b1202-df20-4fe8-bfcb-d2689d381d31_EA%20SPORTS%20FC%20Mobile_category/1705583704508_40-FC-Points%20(1).png" class="checkout-coin-img" alt="" />
          <span id="checkoutName"></span>
        </div>
        <div class="checkout-row">
          <span class="checkout-label">Plazo de entrega</span>
          <span class="checkout-delivery">Instante</span>
        </div>

        <div class="checkout-divider"></div>

        <div class="checkout-row checkout-total-row">
          <span class="checkout-label">Total</span>
          <div class="checkout-pricing">
            <span class="checkout-original" id="checkoutOriginal">18.972 COP</span>
            <div class="checkout-final-row">
              <span class="checkout-badge" id="checkoutBadge">-32%</span>
              <span class="checkout-final-price" id="checkoutPrice">12.927 COP</span>
            </div>
          </div>
        </div>

        <button class="btn-buy" id="btnBuy">
          <span>Comprar ahora</span>
          <span class="btn-arrow">→</span>
        </button>

        <div class="checkout-divider"></div>

       <!-- <div class="trust-badges">
          <div class="trust-item"><i class="bi bi-shield-check fs-6 text-success"></i><span>Garantía de reembolso · P2P</span></div>
          <div class="trust-item"><i class="bi bi-lightning-fill fs-6 text-success"></i><span>Pago rápido · Apple Pay / G Pay</span></div>
          <div class="trust-item"><i class="bi bi-headset fs-6 text-success"></i><span>Asistencia en directo 24/7 — ¡A tu lado!</span></div>
        </div>
      </div> -->

      <div class="delivery-instructions">
        <p class="section-label">Instrucciones de entrega</p>
        <div class="instruction-text" id="instructionText">
          EA SPORTS® | 500 Points (Xbox Digital Key) 🎮<br>
          <span class="flag">🌐</span> Region: Global<br>
          <span class="flag warn">⛔</span> IMPORTANT NOTE BEFORE PURCHASE
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


  <!--JS -->
  <script>
  (function() {

    const products = {
      1:  { name: '100 FC',        price: '6.500 COP',  original: '',              badge: '',     delivery: 'Instante' },
      2:  { name: '500 FC',        price: '14.000 COP',  original: '18.000 COP',    badge: '-32%', delivery: 'Instante' },
      3:  { name: '1100 FC',       price: '25.500 COP',  original: '31.000 COP',    badge: '-31%', delivery: 'Instante' },
      4:  { name: ' 2200 FC',       price: '36.000 COP',  original: '44.000 COP',    badge: '-61%', delivery: 'Instante' },
      5:  { name: ' 5000 FC',       price: '42.500 COP',  original: '56.000 COP',   badge: '-62%', delivery: 'Instante' },
      6:  { name: ' 9500 FC',       price: '72.000 COP',  original: '85.400 COP',   badge: '-67%', delivery: 'Instante' },
      7:  { name: ' 12500 FC',      price: '90.000 COP', original: '120.500 COP',   badge: '-63%', delivery: 'Instante' },
      8: { name: 'Star Pass', price: '18.500 COP', original: '20.000 COP',   badge: '-7%',  delivery: 'Instante' },
    };

    function updateCheckout(id) {
      const p = products[id];
      if (!p) return; 

      document.getElementById('checkoutName').textContent  = p.name;
      document.getElementById('checkoutPrice').textContent = p.price;

      const imgEl  = document.getElementById('checkoutImg');
      const cardImg = document.querySelector('.product-card[data-id="' + id + '"] img');
      if (imgEl && cardImg) {
        imgEl.src = cardImg.getAttribute('src');
        imgEl.style.display = '';
      } else if (imgEl) {
        imgEl.style.display = 'none';
      }

      const origEl  = document.getElementById('checkoutOriginal');
      const badgeEl = document.getElementById('checkoutBadge');

      if (p.original) {
        origEl.textContent   = p.original;
        badgeEl.textContent  = p.badge;
        origEl.style.display  = '';
        badgeEl.style.display = '';
      } else {
        origEl.style.display  = 'none';
        badgeEl.style.display = 'none';
      }

      document.getElementById('instructionText').innerHTML =
        'EA SPORTS\u00ae | ' + p.name.replace(/[\u{1F4B0}\u{1F947}\u{1F3C6}\u{1F48E}\u{1F6E1}\uFE0F]/gu, '').trim() +
        ' \uD83C\uDFAE<br>' +
        '<span class="flag">\uD83C\uDF10</span> Region: Global<br>' +
        '<span class="flag warn">\u26D4</span> IMPORTANT NOTE BEFORE PURCHASE';
    }

    function initCards() {
      const cards = document.querySelectorAll('.product-card');

      if (cards.length === 0) {
        // Si el DOM aún no está listo, reintenta
        setTimeout(initCards, 100);
        return;
      }

      cards.forEach(function(card) {
        card.addEventListener('click', function() {
          cards.forEach(function(c) { c.classList.remove('selected'); });
          card.classList.add('selected');
          updateCheckout(parseInt(card.getAttribute('data-id')));
        });
      });

      // Selección por defecto: tarjeta 500 CP
      var def = document.querySelector('.product-card[data-id="2"]');
      if (def) {
        def.classList.add('selected');
        updateCheckout(2);
      }
    }

    // Funciona tanto si el DOM ya cargó como si no
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initCards);
    } else {
      initCards();
    }

    // Buy button
    document.addEventListener('click', function(e) {
      var btn = e.target.closest('#btnBuy');
      if (!btn) return;

      var jugadorId = document.getElementById('jugadorIdInput').value.trim();
      if (!jugadorId) {
        alert('⚠️ Por favor ingresa tu ID de jugador antes de continuar.');
        document.getElementById('jugadorIdInput').focus();
        return;
      }

      var producto = document.getElementById('checkoutName').textContent.trim();
      var precio   = document.getElementById('checkoutPrice').textContent.replace(/[^0-9]/g, '');

      var form = document.createElement('form');
      form.method = 'POST';
      form.action = '../../php/crear_orden.php';

      [['producto', producto], ['precio', precio], ['jugador_id', jugadorId]].forEach(function(pair) {
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
</body>
</html>

</body>
    <script src="anim.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validaciones.js"></script>
    <script src="assets/js/script.js"></script>
</html>