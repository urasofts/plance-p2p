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
    <title>Membresías y Verificados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <?php $theme_seccion = 'plataformas'; require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
    <link rel="stylesheet" href="../../assets/css/styles-plataformas.css">
    <link rel="stylesheet" href="../../assets/css/styles-code-block.css">
</head>
<style>
    /* Membresías y Verificados — acento azul */
    :root {
        --plat-accent:      #4d9fff;
        --plat-accent-rgb:  77, 159, 255;
        --plat-accent-dark: #2176d9;
    }
</style>
<body>
    <?php
    $nav_back_url  = "suscripciones.php";
    $nav_back_text = "Atrás";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>

    <!-- GAME BANNER -->
    <div class="game-banner">
        <div class="game-banner__tag">
            <i class="fa-solid fa-globe" style="color: #4d9fff;"></i> Membresías y Verificados
            <span class="recurring-badge"><i class="bi bi-calendar-check-fill" style="color: #4d9fff;"></i> Pago Recurrente</span>
        </div>
    </div>

    <!-- MAIN LAYOUT -->
    <main class="shop-layout">

        <!-- LEFT: Products -->
        <section class="products-panel">

            <!-- YOUTUBE PREMIUM -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=youtube.com&sz=32" alt="YouTube">
                    <span>YouTube Premium</span>
                </div>
                <div class="products-grid">
                    <div class="product-card popular-card" data-id="1" data-servicio="YouTube Premium" data-plan="Individual" data-precio="19900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">YouTube</div>
                        <div class="product-card__pts">Individual</div>
                        <div class="product-card__label">Sin anuncios · YT Music · 1 cuenta</div>
                        <div class="product-card__price">19.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="2" data-servicio="YouTube Premium" data-plan="Familiar" data-precio="29900">
                        <div class="product-card__platform">YouTube</div>
                        <div class="product-card__pts">Familiar</div>
                        <div class="product-card__label">Sin anuncios · YT Music · 5 cuentas</div>
                        <div class="product-card__price">29.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

            <!-- TWITTER/X VERIFICADO -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=x.com&sz=32" alt="Twitter X">
                    <span>X (Twitter) Verificado</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="3" data-servicio="Twitter Verificado" data-plan="Basic" data-precio="14900">
                        <div class="product-card__platform">X · Twitter</div>
                        <div class="product-card__pts">Basic</div>
                        <div class="product-card__label">Verificado · Editar tweets</div>
                        <div class="product-card__price">14.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="4" data-servicio="Twitter Verificado" data-plan="Premium" data-precio="32900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">X · Twitter</div>
                        <div class="product-card__pts">Premium</div>
                        <div class="product-card__label">Verificado · Menos anuncios · Grok AI</div>
                        <div class="product-card__price">32.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="5" data-servicio="Twitter Verificado" data-plan="Premium+" data-precio="49900">
                        <div class="product-card__platform">X · Twitter</div>
                        <div class="product-card__pts">Premium+</div>
                        <div class="product-card__label">Sin anuncios · Grok AI avanzado</div>
                        <div class="product-card__price">49.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

            <!-- META VERIFIED -->
            <div class="section-block">
                <div class="platform-header">
                    <img src="https://www.google.com/s2/favicons?domain=meta.com&sz=32" alt="Meta">
                    <span>Meta Verified</span>
                </div>
                <div class="products-grid">
                    <div class="product-card" data-id="6" data-servicio="Meta Verified" data-plan="Instagram" data-precio="24900">
                        <div class="product-card__platform">Meta · Instagram</div>
                        <div class="product-card__pts">Instagram</div>
                        <div class="product-card__label">✓ Verificado · Soporte prioritario</div>
                        <div class="product-card__price">24.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card" data-id="7" data-servicio="Meta Verified" data-plan="Facebook" data-precio="24900">
                        <div class="product-card__platform">Meta · Facebook</div>
                        <div class="product-card__pts">Facebook</div>
                        <div class="product-card__label">✓ Verificado · Soporte prioritario</div>
                        <div class="product-card__price">24.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                    <div class="product-card popular-card" data-id="8" data-servicio="Meta Verified" data-plan="Instagram + Facebook" data-precio="39900">
                        <div class="badge-popular">★ Popular</div>
                        <div class="product-card__platform">Meta · Combo</div>
                        <div class="product-card__pts">IG + FB</div>
                        <div class="product-card__label">✓ Ambas plataformas</div>
                        <div class="product-card__price">39.900 COP <span class="recurrente-tag">/ mes</span></div>
                    </div>
                </div>
            </div>

        </section>

        <!-- RIGHT: Checkout -->
        <aside class="checkout-panel" id="checkoutPanel">
            <div class="checkout-summary">
                <div class="checkout-product-name" id="checkoutName">▶️ YouTube Premium — Individual</div>

                <div class="checkout-row">
                    <span class="checkout-label">Periodicidad</span>
                    <span class="checkout-delivery">Mensual</span>
                </div>
                <div class="checkout-row">
                    <span class="checkout-label">Duración</span>
                    <span class="checkout-delivery">12 meses</span>
                </div>

                <div class="checkout-divider"></div>

                <div class="checkout-row checkout-total-row">
                    <span class="checkout-label">Total / mes</span>
                    <div class="checkout-pricing">
                        <span class="checkout-final-price" id="checkoutPrice">19.900 COP</span>
                    </div>
                </div>

                <div class="recurring-info">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Este servicio se cobra automáticamente cada mes durante 12 meses. Puedes cancelar en cualquier momento.</span>
                </div>

                <div class="checkout-divider"></div>

                <div class="field-group" style="margin-bottom:0.6rem;">
                    <label class="field-label" style="font-size:0.73rem;font-weight:600;color:var(--pt-text-sec);margin-bottom:0.25rem;display:block;">Correo electrónico</label>
                    <input type="email" id="usuarioIdInput" placeholder="tucorreo@ejemplo.com"
                           value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>"
                           style="width:100%;background:var(--pt-navbar);border:1.5px solid var(--pt-border);border-radius:8px;color:var(--pt-text);font-family:inherit;font-size:0.85rem;padding:0.45rem 0.7rem;outline:none;">
                </div>

                <button class="btn-buy" id="btnBuy">
                    <span>Suscribirse ahora</span>
                    <span class="btn-arrow">→</span>
                </button>

                <!-- <div class="trust-badges">
                    <div class="trust-item">🛡️ <span>Garantía de reembolso · P2P</span></div>
                    <div class="trust-item">🔄 <span>Cobro recurrente automático mensual</span></div>
                    <div class="trust-item">💬 <span>Asistencia en directo 24/7</span></div>
                </div> -->
            </div>

            <div class="session-instructions">
                <p class="section-label">Instrucciones para crear sesión</p>
                <ol class="session-steps">
                    <li class="session-step">
                        <span class="session-step__num">1</span>
                        <div class="session-step__body">
                            <span class="session-step__title">Digita tu correo electrónico</span>
                            <span class="session-step__desc">Escríbelo en el campo de arriba; ahí gestionamos el acceso a tu suscripción.</span>
                        </div>
                    </li>
                    <li class="session-step">
                        <span class="session-step__num">2</span>
                        <div class="session-step__body">
                            <span class="session-step__title">Elige el plan</span>
                            <span class="session-step__desc">Selecciona la plataforma y el plan que quieres suscribir en el panel de la izquierda.</span>
                        </div>
                    </li>
                    <li class="session-step">
                        <span class="session-step__num">3</span>
                        <div class="session-step__body">
                            <span class="session-step__title">Crea tu sesión de pago</span>
                            <span class="session-step__desc">Presiona "Suscribirse ahora" para generar tu sesión y completar el pago de forma segura.</span>
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
    <section class="integration-docs" style="--code-accent:var(--plat-accent); --code-accent-ink:var(--plat-accent-ink); --code-accent-soft:rgba(var(--plat-accent-rgb),0.12); --code-radius-sm:var(--plat-radius-sm); --code-radius-md:var(--plat-radius-md); --code-radius-lg:var(--plat-radius-lg); --code-font:var(--plat-font);">
        <span class="integration-docs__badge"><i class="bi bi-braces"></i> Integración PlacetoPay</span>
        <h3>Así se crea la sesión de pago de esta tienda</h3>
        <p>Cuando presionas <strong>"Suscribirse ahora"</strong>, nuestro backend arma este mismo request y lo envía a <strong>PlacetoPay Web Checkout</strong> con un bloque <code>recurring</code> fijo a mensual. Se cobra el primer mes ahora y PlacetoPay programa automáticamente los 11 cobros siguientes.</p>

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
  <span class="jk">"locale"</span>: <span class="js">"es_CO"</span>,
  <span class="jk">"auth"</span>: {
    <span class="jk">"login"</span>: <span class="js">"YOUR_LOGIN"</span>,
    <span class="jk">"tranKey"</span>: <span class="js">"TRAN_KEY_CALCULADO"</span>,
    <span class="jk">"nonce"</span>: <span class="js">"Tm9uY2VFbkJhc2U2NA=="</span>,
    <span class="jk">"seed"</span>: <span class="js">"2026-08-25T10:15:32-05:00"</span>
  },
  <span class="jk">"buyer"</span>: { <span class="jk">"email"</span>: <span class="js">"usuario@correo.com"</span> },
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"REC-000077"</span>,
    <span class="jk">"description"</span>: <span class="js">"YouTube Premium Individual"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: <span class="jn">19900</span> },
    <span class="cm">// recurring fijo: mensual, hasta 12 cobros</span>
    <span class="jk">"recurring"</span>: {
      <span class="jk">"periodicity"</span>: <span class="js">"M"</span>,
      <span class="jk">"interval"</span>: <span class="js">"1"</span>,
      <span class="jk">"nextPayment"</span>: <span class="js">"2026-09-25"</span>,
      <span class="jk">"maxPeriods"</span>: <span class="jn">12</span>
    }
  },
  <span class="jk">"expiration"</span>: <span class="js">"2026-08-25T11:15:32-05:00"</span>,
  <span class="jk">"returnUrl"</span>: <span class="js">"https://tu-dominio.com/retorno/retorno_rec.php?rec=77"</span>,
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

<span class="cm">// próximo cobro: dentro de 1 mes, hasta 12 meses en total</span>
<span class="cvar">$nextPayment</span> = date(<span class="js">'Y-m-d'</span>, strtotime(<span class="js">'+1 month'</span>));

<span class="cm">// cuerpo del request — cobra el primer mes y programa la recurrencia</span>
<span class="cvar">$data</span> = [
    <span class="jk">'locale'</span> =&gt; <span class="js">'es_CO'</span>,
    <span class="jk">'auth'</span>   =&gt; [
        <span class="jk">'login'</span>   =&gt; <span class="cvar">$login</span>,
        <span class="jk">'tranKey'</span> =&gt; <span class="cvar">$tranKey</span>,
        <span class="jk">'nonce'</span>   =&gt; <span class="cvar">$nonceB64</span>,
        <span class="jk">'seed'</span>    =&gt; <span class="cvar">$seed</span>,
    ],
    <span class="jk">'buyer'</span> =&gt; [<span class="jk">'email'</span> =&gt; <span class="cvar">$usuario_id</span>],
    <span class="jk">'payment'</span> =&gt; [
        <span class="jk">'reference'</span>   =&gt; <span class="js">'REC-'</span> . <span class="cvar">$rec_id</span>,
        <span class="jk">'description'</span> =&gt; <span class="cvar">$servicio</span> . <span class="js">' '</span> . <span class="cvar">$plan</span>,
        <span class="jk">'amount'</span>      =&gt; [<span class="jk">'currency'</span> =&gt; <span class="js">'COP'</span>, <span class="jk">'total'</span> =&gt; (float) <span class="cvar">$precio</span>],
        <span class="cm">// campo clave para el cobro automático mensual</span>
        <span class="jk">'recurring'</span>   =&gt; [
            <span class="jk">'periodicity'</span> =&gt; <span class="js">'M'</span>,   <span class="cm">// mensual</span>
            <span class="jk">'interval'</span>    =&gt; <span class="js">'1'</span>,   <span class="cm">// cada 1 mes</span>
            <span class="jk">'nextPayment'</span> =&gt; <span class="cvar">$nextPayment</span>,
            <span class="jk">'maxPeriods'</span>  =&gt; 12,  <span class="cm">// máximo 12 meses</span>
        ],
    ],
    <span class="jk">'expiration'</span> =&gt; date(<span class="js">'c'</span>, strtotime(<span class="js">'+1 hour'</span>)),
    <span class="jk">'returnUrl'</span>  =&gt; app_base_url() . <span class="js">'/retorno/retorno_rec.php?rec='</span> . <span class="cvar">$rec_id</span>,
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
            <span>A diferencia de las suscripciones con periodo elegible, aquí la periodicidad es <strong>siempre mensual</strong> (<code>maxPeriods: 12</code>): el sistema cobra automáticamente cada mes durante un año, sin que tengas que volver a autorizar el pago.</span>
        </div>

        <a class="integration-docs__link" href="../guias/guia-developer.php#tipos-pago">
            <div>
                <strong>¿Quieres entender esta integración a fondo?</strong>
                <span>Lee la documentación completa sobre tipos de pago y cobros recurrentes en Web Checkout.</span>
            </div>
            <i class="bi bi-arrow-right"></i>
        </a>
    </section>

    <script>
    (function() {
        const products = {
            1: { name: '▶️ YouTube Premium — Individual', servicio: 'YouTube Premium', plan: 'Individual',          price: '19.900 COP', precio: 19900 },
            2: { name: '▶️ YouTube Premium — Familiar',  servicio: 'YouTube Premium', plan: 'Familiar',            price: '29.900 COP', precio: 29900 },
            3: { name: '𝕏 Twitter Verificado — Basic',   servicio: 'Twitter Verificado', plan: 'Basic',            price: '14.900 COP', precio: 14900 },
            4: { name: '𝕏 Twitter Verificado — Premium', servicio: 'Twitter Verificado', plan: 'Premium',          price: '32.900 COP', precio: 32900 },
            5: { name: '𝕏 Twitter Verificado — Premium+',servicio: 'Twitter Verificado', plan: 'Premium+',         price: '49.900 COP', precio: 49900 },
            6: { name: '✓ Meta Verified — Instagram',    servicio: 'Meta Verified',      plan: 'Instagram',        price: '24.900 COP', precio: 24900 },
            7: { name: '✓ Meta Verified — Facebook',     servicio: 'Meta Verified',      plan: 'Facebook',         price: '24.900 COP', precio: 24900 },
            8: { name: '✓ Meta Verified — IG + FB',      servicio: 'Meta Verified',      plan: 'Instagram + Facebook', price: '39.900 COP', precio: 39900 },
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
                    cards.forEach(function(c) { c.classList.remove('selected'); });
                    card.classList.add('selected');
                    updateCheckout(parseInt(card.getAttribute('data-id')));
                });
            });

            // Selección por defecto
            var def = document.querySelector('.product-card[data-id="1"]');
            if (def) { def.classList.add('selected'); updateCheckout(1); }
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
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!usuarioId || !emailRegex.test(usuarioId)) {
                alert('⚠️ Por favor ingresa un correo electrónico válido.');
                document.getElementById('usuarioIdInput').focus();
                return;
            }

            var selectedCard = document.querySelector('.product-card.selected');
            if (!selectedCard) { alert('⚠️ Selecciona un plan primero.'); return; }

            var servicio = selectedCard.getAttribute('data-servicio');
            var plan     = selectedCard.getAttribute('data-plan');
            var precio   = selectedCard.getAttribute('data-precio');

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../php/crear_recurrencia.php';

            [['servicio', servicio], ['plan', plan], ['precio', precio], ['usuario_id', usuarioId]].forEach(function(pair) {
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
    <script src="../../assets/js/code-block.js"></script>
</body>
</html>
