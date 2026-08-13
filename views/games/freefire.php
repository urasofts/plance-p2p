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
    <title>Free Fire — Diamantes</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    /* 
    FREE FIRE — Dark Theme · Naranja/Rojo */

    :root {
    --bg-base:        #18191b;
    --bg-surface:     #16181c;
    --bg-card:        #1e2128;
    --bg-card-hover:  #252830;
    --bg-selected:    #2a0d08;
    --border:         #2e3038;
    --border-active:  #ff6b35;
    --accent:         #ff6b35;
    --accent-glow:    rgba(255, 107, 53, 0.25);
    --accent-dark:    #e04e1a;
    --text-primary:   #f0f1f3;
    --text-secondary: #8a8d96;
    --text-muted:     #555860;
    --green:          #3ecf8e;
    --red-badge:      #e05252;
    --font-display:   'calibri', sans-serif; /*'Barlow Condensed', sans-serif; */
    --font-body:      'calibri', sans-serif; /* 'Barlow', sans-serif; */
    --radius-sm:      6px;
    --radius-md:      10px;
    --radius-lg:      14px;
    --transition:     0.2s ease;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { font-size: 16px; scroll-behavior: smooth; }

    body {
    /*     background-image: url(../assets/images/bg25.jpg); */
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
    border: 1.5px solid var(--pt-border);
    border-radius: var(--radius-sm);
    color: var(--pt-text);
    font-family: var(--font-body);
    font-size: 0.85rem;
    padding: 0.35rem 0.75rem;
    outline: none;
    transition: border-color var(--transition);
    width: 180px;
    }

    .banner-player-id input::placeholder { color: var(--text-muted); }
    .banner-player-id input:focus { border-color: var(--accent); }

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
    color: var(--text-secondary);
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
    border-color: rgba(255, 107, 53, 0.4);
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
    color: var(--pt-text);
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

    .product-card__img { font-size: 1.5rem; margin-bottom: 0.25rem; }

    .product-card__pts {
    /* */
    font-family: var(--font-display);
    font-size: 1.00rem;
    font-weight: 700;
    color: var(--pt-text);
    line-height: 1;
    }

    .product-card__label {
    font-size: 0.75rem;
    color: var(--pt-text-sec);
    margin-bottom: 0.3rem;
    }

    .product-card__price-old {
    font-size: 0.72rem;
    color: var(--text-muted);
    text-decoration: line-through;
    }

    .product-card__price {
    font-family: var(--font-display);
    font-size: 1.0rem;
    font-weight: 700;
    color: var(--accent);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
    }

    .discount-tag {
    background: rgba(255, 107, 53, 0.15);
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
    background: var(--pt-boxitem);
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

    .checkout-label { font-size: 0.85rem; color: var(--text-secondary); }
    .checkout-delivery { font-size: 0.85rem; font-weight: 600; color: var(--pt-text); }
    .checkout-divider { height: 1px; background: var(--pt-border); margin: 0.8rem 0; }
    .checkout-total-row { align-items: flex-end; }

    .checkout-pricing {
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    }

    .checkout-original {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-decoration: line-through;
    }

    .checkout-final-row { display: flex; align-items: center; gap: 0.4rem; }

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

    .btn-buy:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 6px 20px var(--accent-glow); }
    .btn-buy:hover::before { transform: translateX(100%); }
    .btn-buy:active { transform: translateY(0); }

    .btn-arrow { font-size: 1.1rem; transition: transform 0.2s; }
    .btn-buy:hover .btn-arrow { transform: translateX(4px); }

    .trust-badges { margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; }
    .trust-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--text-secondary); }

    .instruction-text { font-size: 0.83rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 0.75rem; }
    .flag { margin-right: 0.2rem; }

    .btn-instructions {
    background: none;
    border: 1px solid var(--pt-border);
    color: var(--pt-text-sec);
    font-size: 0.82rem;
    padding: 0.35rem 0.8rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all var(--transition);
    font-family: var(--font-body);
    }
    .btn-instructions:hover { border-color: var(--accent); color: var(--pt-text); }

    .vendor-info { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; }

    .vendor-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b35, #e04e1a);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 0.85rem;
    color: #0d0e10;
    flex-shrink: 0;
    }

    .vendor-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
    .vendor-rating { font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.1rem; }

    /* ── ANIMATIONS ── */
    @keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
    }
    .products-panel { animation: fadeSlideIn 0.4s ease both; }
    .checkout-panel { animation: fadeSlideIn 0.4s 0.1s ease both; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
    .shop-layout { grid-template-columns: 1fr; }
    .checkout-panel { position: static; }
    .products-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 600px) {
    .products-grid { grid-template-columns: repeat(2, 1fr); }
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
      <img src="https://www.masgamers.com/wp-content/uploads/2022/06/free-fire-nuevo-logo.png" class="card-img-top" alt="" class="game-icon" />
      Free Fire — Diamantes
    </div>
    <div class="banner-player-id">
      <label for="jugadorIdInput"><i class="bi bi-person-vcard-fill fs-6 text-warning"></i> ID de jugador</label>
      <input type="text" id="jugadorIdInput" placeholder="Ej: 123456789" autocomplete="off" />
    </div>
  </div>

  <!-- ═══ MAIN LAYOUT ═══ -->
  <main class="shop-layout">

    <!-- LEFT: Products Panel -->
    <section class="products-panel">
      <div class="section-block">
        <p class="section-label">Elige el importe</p>
        <div class="products-grid" id="productsGrid">

          <div class="product-card" data-id="1" data-pts="100" data-price="4500" data-original="" data-discount="">
            <img src="https://pngimg.com/d/diamond_PNG6694.png" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">100</div>
            <div class="product-card__label">Diamantes</div>
            <div class="product-card__price">4.500 COP</div>
          </div>

          <div class="product-card popular-card" data-id="2" data-pts="310" data-price="11900" data-original="15000" data-discount="21">
             <!-- Badge Popular <div class="badge-popular">★ Popular</div>   -->
            <img src="https://pngimg.com/d/diamond_PNG6694.png" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">310</div>
            <div class="product-card__label">Diamantes</div>
            <div class="product-card__price-old">15.000 COP</div>
            <div class="product-card__price">11.900 COP <span class="discount-tag">-21%</span></div>
          </div>

          <div class="product-card" data-id="3" data-pts="520" data-price="19800" data-original="26000" data-discount="24">
            <img src="https://www.ciberonline.com/recargas/img/diamantes1.webp" style="height: 40px; width: 40px" alt="💎">
            <div class="product-card__pts">520</div>
            <div class="product-card__label">Diamantes</div>
            <div class="product-card__price-old">26.000 COP</div>
            <div class="product-card__price">19.800 COP <span class="discount-tag">-24%</span></div>
          </div>

          <div class="product-card" data-id="4" data-pts="1060" data-price="38500" data-original="52000" data-discount="26">
            <img src="https://www.ciberonline.com/recargas/img/diamantes1.webp" style="height: 40px; width: 40px" alt="💎">
            <div class="product-card__pts">1060</div>
            <div class="product-card__label">Diamantes</div>
            <div class="product-card__price-old">52.000 COP</div>
            <div class="product-card__price">38.500 COP <span class="discount-tag">-26%</span></div>
          </div>

          <div class="product-card" data-id="5" data-pts="2180" data-price="74000" data-original="98000" data-discount="24">
            <img src="https://www.ciberonline.com/recargas/img/diamantes1.webp" style="height: 40px; width: 40px" alt="">
            <div class="product-card__pts">2180</div>
            <div class="product-card__label">Diamantes</div>
            <div class="product-card__price-old">98.000 COP</div>
            <div class="product-card__price">74.000 COP <span class="discount-tag">-24%</span></div>
          </div>

          <div class="product-card" data-id="6" data-pts="3640" data-price="118000" data-original="155000" data-discount="24">
            <img src="https://www.ciberonline.com/recargas/img/diamantes1.webp" style="height: 40px; width: 40px" alt="💎">
            <div class="product-card__pts">3640</div>
            <div class="product-card__label">Diamantes</div>
            <div class="product-card__price-old">155.000 COP</div>
            <div class="product-card__price">118.000 COP <span class="discount-tag">-24%</span></div>
          </div>

          <div class="product-card" data-id="7" data-pts="5600" data-price="175000" data-original="230000" data-discount="24">
            <img src="https://www.ciberonline.com/recargas/img/diamantes1.webp" style="height: 40px; width: 40px" alt="💎">
            <div class="product-card__pts">5600</div>
            <div class="product-card__label">Diamantes</div>
            <div class="product-card__price-old">230.000 COP</div>
            <div class="product-card__price">175.000 COP <span class="discount-tag">-24%</span></div>
          </div>

          <div class="product-card" data-id="8" data-pts="11000" data-price="320000" data-original="420000" data-discount="24">
            <img src="https://www.ciberonline.com/recargas/img/diamantes1.webp" style="height: 40px; width: 40px" alt="💎">
            <div class="product-card__pts">11000</div>
            <div class="product-card__label">Diamantes</div>
            <div class="product-card__price-old">420.000 COP</div>
            <div class="product-card__price">320.000 COP <span class="discount-tag">-24%</span></div>
          </div>

          <div class="product-card battlepass-card" data-id="9" data-pts="Pase Elite" data-price="22000" data-original="35000" data-discount="37">
            <img src="https://sc.filehippo.net/images/t_app-icon-l/p/49509f05-847a-4e51-80dc-701dd861418a/2900070462/pase-elite-free-fire-icon.png" style="height: 40px; width: 40px" alt="🛡️">
            <div class="product-card__pts" style="font-size:0.85rem;">Pase Elite</div>
            <div class="product-card__label">Pase de Batalla</div>
            <div class="product-card__price-old">35.000 COP</div>
            <div class="product-card__price">22.000 COP <span class="discount-tag">-37%</span></div>
          </div>

        </div>
      </div>
    </section>

    <!-- RIGHT: Checkout Panel -->
    <aside class="checkout-panel" id="checkoutPanel">

      <div class="checkout-summary">
        <div class="checkout-product-name"><img id="checkoutImg" src="" alt="" /><span id="checkoutName">💎 310 Diamantes</span></div>

        <div class="checkout-row">
          <span class="checkout-label">Plazo de entrega</span>
          <span class="checkout-delivery">Instante</span>
        </div>

        <div class="checkout-divider"></div>

        <div class="checkout-row checkout-total-row">
          <span class="checkout-label">Total</span>
          <div class="checkout-pricing">
            <span class="checkout-original" id="checkoutOriginal">15.000 COP</span>
            <div class="checkout-final-row">
              <span class="checkout-badge" id="checkoutBadge">-21%</span>
              <span class="checkout-final-price" id="checkoutPrice">11.900 COP</span>
            </div>
          </div>
        </div>

        <button class="btn-buy" id="btnBuy">
          <span>Comprar ahora</span>
          <span class="btn-arrow">→</span>
        </button>

        <div class="checkout-divider"></div>

        <!-- <div class="trust-badges">
          <div class="trust-item"><i class="bi bi-shield-check fs-6 text-danger"></i><span>Garantía de reembolso · P2P</span></div>
          <div class="trust-item"><i class="bi bi-lightning-fill fs-6 text-danger"></i><span>Pago rápido · Apple Pay / G Pay</span></div>
          <div class="trust-item"><i class="bi bi-headset fs-6 text-danger"></i><span>Asistencia en directo 24/7 — ¡A tu lado!</span></div>
        </div>
      </div> -->

      <div class="delivery-instructions">
        <p class="section-label">Instrucciones de entrega</p>
        <div class="instruction-text" id="instructionText">
          Garena® | 310 Diamantes 🎮<br>
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

  <!-- JS -->
  <script>
  (function() {

    const products = {
      1: { name: ' 100 Diamantes',   price: '4.500 COP',   original: '',           badge: '',     delivery: 'Instante' },
      2: { name: ' 310 Diamantes',   price: '11.900 COP',  original: '15.000 COP', badge: '-21%', delivery: 'Instante' },
      3: { name: ' 520 Diamantes',   price: '19.800 COP',  original: '26.000 COP', badge: '-24%', delivery: 'Instante' },
      4: { name: '1060 Diamantes',  price: '38.500 COP',  original: '52.000 COP', badge: '-26%', delivery: 'Instante' },
      5: { name: ' 2180 Diamantes',  price: '74.000 COP',  original: '98.000 COP', badge: '-24%', delivery: 'Instante' },
      6: { name: ' 3640 Diamantes',  price: '118.000 COP', original: '155.000 COP',badge: '-24%', delivery: 'Instante' },
      7: { name: ' 5600 Diamantes',  price: '175.000 COP', original: '230.000 COP',badge: '-24%', delivery: 'Instante' },
      8: { name: ' 11000 Diamantes', price: '320.000 COP', original: '420.000 COP',badge: '-24%', delivery: 'Instante' },
      9: { name: ' Pase Elite',     price: '22.000 COP',  original: '35.000 COP', badge: '-37%', delivery: 'Instante' },
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
        origEl.textContent    = p.original;
        badgeEl.textContent   = p.badge;
        origEl.style.display  = '';
        badgeEl.style.display = '';
      } else {
        origEl.style.display  = 'none';
        badgeEl.style.display = 'none';
      }

      document.getElementById('instructionText').innerHTML =
        'Garena\u00ae | ' + p.name.replace(/[\u{1F48E}\u{1F6E1}\uFE0F]/gu, '').trim() +
        ' \uD83C\uDFAE<br>' +
        '<span class="flag">\uD83C\uDF10</span> Region: Global<br>' +
        '<span class="flag warn">\u26D4</span> IMPORTANT NOTE BEFORE PURCHASE';
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validaciones.js"></script>
    <script src="assets/js/script.js"></script>
</html>