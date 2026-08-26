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
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/styles-juegos.css">
    <link rel="stylesheet" href="../../assets/css/styles-code-block.css">
    <?php $theme_seccion = 'juegos'; require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    /* Free Fire — acento naranja/rojo */
    :root {
        --gj-accent:        #ff6b35;
        --gj-accent-glow:   rgba(255, 107, 53, 0.25);
        --gj-accent-soft:   rgba(255, 107, 53, 0.15);
        --gj-accent-hover:  rgba(255, 107, 53, 0.4);
        --gj-accent-dark:   #e04e1a;
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

      <div class="session-instructions">
        <p class="section-label">Instrucciones para crear sesión</p>
        <ol class="session-steps">
          <li class="session-step">
            <span class="session-step__num">1</span>
            <div class="session-step__body">
              <span class="session-step__title">Digita tu ID de jugador</span>
              <span class="session-step__desc">Escríbelo en el campo "ID de jugador" de arriba, tal como aparece en tu cuenta del juego.</span>
            </div>
          </li>
          <li class="session-step">
            <span class="session-step__num">2</span>
            <div class="session-step__body">
              <span class="session-step__title">Elige el producto</span>
              <span class="session-step__desc">Selecciona la cantidad de diamantes o el paquete que quieres comprar en el panel de la izquierda.</span>
            </div>
          </li>
          <li class="session-step">
            <span class="session-step__num">3</span>
            <div class="session-step__body">
              <span class="session-step__title">Crea tu sesión de pago</span>
              <span class="session-step__desc">Presiona "Comprar ahora" para generar tu sesión y completar el pago de forma segura.</span>
            </div>
          </li>
        </ol>
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

  <!-- ═══ INTEGRACIÓN PLACETOPAY ═══ -->
  <section class="integration-docs" style="--code-accent:var(--gj-accent); --code-accent-ink:var(--gj-accent-ink); --code-accent-soft:var(--gj-accent-soft); --code-radius-sm:var(--gj-radius-sm); --code-radius-md:var(--gj-radius-md); --code-radius-lg:var(--gj-radius-lg); --code-font:var(--gj-font-body);">
    <span class="integration-docs__badge"><i class="bi bi-braces"></i> Integración PlacetoPay</span>
    <h3>Así se crea la sesión de pago de esta tienda</h3>
    <p>Cuando presionas <strong>"Comprar ahora"</strong>, nuestro backend arma este mismo request y lo envía a <strong>PlacetoPay Web Checkout</strong>. La respuesta trae un <code>processUrl</code> al que te redirigimos para pagar con tarjeta o PSE — tus datos de pago nunca pasan por nuestro servidor.</p>

    <div class="endpoint-bar">
      <span class="method-pill">POST</span>
      <span class="endpoint-url">https://checkout-test.placetopay.com/api/session</span>
      <span class="endpoint-note">ambiente de pruebas</span>
    </div>

    <div class="code-block">
      <div class="code-tabs">
        <button class="code-tab active" data-key="json">JSON</button>
        <button class="code-tab" data-key="php">PHP</button>
        <button class="code-copy"><i class="bi bi-clipboard"></i> Copiar</button>
      </div>
      <pre class="code-panel active" data-key="json"><code>{
  <span class="jk">"auth"</span>: {
    <span class="jk">"login"</span>: <span class="js">"YOUR_LOGIN"</span>,
    <span class="jk">"tranKey"</span>: <span class="js">"TRAN_KEY_CALCULADO"</span>,
    <span class="jk">"nonce"</span>: <span class="js">"Tm9uY2VFbkJhc2U2NA=="</span>,
    <span class="jk">"seed"</span>: <span class="js">"2026-08-25T10:15:32-05:00"</span>
  },
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"ORD-000726"</span>,
    <span class="jk">"description"</span>: <span class="js">"310 Diamantes"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: <span class="jn">11900</span> }
  },
  <span class="jk">"expiration"</span>: <span class="js">"2026-08-25T11:15:32-05:00"</span>,
  <span class="jk">"returnUrl"</span>: <span class="js">"https://tu-dominio.com/retorno/retorno.php?order=726"</span>,
  <span class="jk">"notifyUrl"</span>: <span class="js">"https://tu-dominio.com/php/notify.php"</span>,
  <span class="jk">"ipAddress"</span>: <span class="js">"203.0.113.42"</span>,
  <span class="jk">"userAgent"</span>: <span class="js">"Mozilla/5.0 (Windows NT 10.0; Win64; x64)"</span>
}</code></pre>
      <pre class="code-panel" data-key="php"><code>&lt;?php
<span class="cm">// credenciales fuera del código, nunca hardcodeadas</span>
<span class="cvar">$login</span>     = getenv(<span class="js">'P2P_LOGIN'</span>);
<span class="cvar">$secretKey</span> = getenv(<span class="js">'P2P_SECRET_KEY'</span>);
<span class="cvar">$url</span>       = <span class="js">'https://checkout-test.placetopay.com/api/session'</span>;

<span class="cm">// autenticación: Base64( SHA256( nonce + seed + secretKey ) )</span>
<span class="cvar">$seed</span>     = date(<span class="js">'c'</span>);
<span class="cvar">$nonce</span>    = bin2hex(random_bytes(16));
<span class="cvar">$tranKey</span>  = base64_encode(hash(<span class="js">'sha256'</span>, <span class="cvar">$nonce</span> . <span class="cvar">$seed</span> . <span class="cvar">$secretKey</span>, true));
<span class="cvar">$nonceB64</span> = base64_encode(<span class="cvar">$nonce</span>);

<span class="cm">// cuerpo del request — así lo arma esta tienda</span>
<span class="cvar">$data</span> = [
    <span class="jk">'auth'</span> =&gt; [
        <span class="jk">'login'</span>   =&gt; <span class="cvar">$login</span>,
        <span class="jk">'tranKey'</span> =&gt; <span class="cvar">$tranKey</span>,
        <span class="jk">'nonce'</span>   =&gt; <span class="cvar">$nonceB64</span>,
        <span class="jk">'seed'</span>    =&gt; <span class="cvar">$seed</span>,
    ],
    <span class="jk">'payment'</span> =&gt; [
        <span class="jk">'reference'</span>   =&gt; <span class="js">'ORD-'</span> . str_pad(<span class="cvar">$order_id</span>, 6, <span class="js">'0'</span>, STR_PAD_LEFT),
        <span class="jk">'description'</span> =&gt; <span class="cvar">$producto</span>,               <span class="cm">// ej: "310 Diamantes"</span>
        <span class="jk">'amount'</span>      =&gt; [<span class="jk">'currency'</span> =&gt; <span class="js">'COP'</span>, <span class="jk">'total'</span> =&gt; (float) <span class="cvar">$precio</span>],
    ],
    <span class="jk">'expiration'</span> =&gt; date(<span class="js">'c'</span>, strtotime(<span class="js">'+1 hour'</span>)),
    <span class="jk">'returnUrl'</span>  =&gt; app_base_url() . <span class="js">'/retorno/retorno.php?order='</span> . <span class="cvar">$order_id</span>,
    <span class="jk">'notifyUrl'</span>  =&gt; <span class="cvar">$notifyUrl</span>,
    <span class="jk">'ipAddress'</span>  =&gt; <span class="cvar">$_SERVER</span>[<span class="js">'REMOTE_ADDR'</span>],
    <span class="jk">'userAgent'</span>  =&gt; <span class="cvar">$_SERVER</span>[<span class="js">'HTTP_USER_AGENT'</span>],
];

<span class="cvar">$ch</span> = curl_init(<span class="cvar">$url</span>);
curl_setopt_array(<span class="cvar">$ch</span>, [
    CURLOPT_POST           =&gt; true,
    CURLOPT_RETURNTRANSFER =&gt; true,
    CURLOPT_HTTPHEADER     =&gt; [<span class="js">'Content-Type: application/json'</span>],
    CURLOPT_POSTFIELDS     =&gt; json_encode(<span class="cvar">$data</span>),
]);

<span class="cvar">$result</span> = json_decode(curl_exec(<span class="cvar">$ch</span>), true);
curl_close(<span class="cvar">$ch</span>);

<span class="cm">// redirige al comprador a la pasarela de PlacetoPay</span>
header(<span class="js">'Location: '</span> . <span class="cvar">$result</span>[<span class="js">'processUrl'</span>]);</code></pre>
    </div>

    <div class="doc-note">
      <span class="doc-note-icon">💡</span>
      <span>Este flujo es <strong>Web Checkout de pago único</strong>: un solo cobro por el valor de los diamantes elegidos, sin tokenizar tarjeta ni programar cobros futuros. Cuando vuelvas de la pasarela, consultamos el estado real de la sesión antes de entregar el producto.</span>
    </div>

    <a class="integration-docs__link" href="../guias/guia-developer.php#web-checkout">
      <div>
        <strong>¿Quieres entender esta integración a fondo?</strong>
        <span>Lee la documentación completa de Web Checkout — autenticación, notificaciones y más.</span>
      </div>
      <i class="bi bi-arrow-right"></i>
    </a>
  </section>

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
  <script src="../../assets/js/code-block.js"></script>

</body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validaciones.js"></script>
    <script src="assets/js/script.js"></script>
</html>