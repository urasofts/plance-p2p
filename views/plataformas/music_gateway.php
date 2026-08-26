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
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
    <link rel="stylesheet" href="../../assets/css/styles-plataformas-gateway.css">
    <link rel="stylesheet" href="../../assets/css/styles-code-block.css">
</head>
<style>
    /* Música API Gateway — acento verde Spotify */
    :root {
        --plat-accent:      #1db954;
        --plat-accent-rgb:  29, 185, 84;
        --plat-accent-dark: #17a248;
    }
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
                    <img src="https://www.google.com/s2/favicons?domain=spotify.com&sz=32" alt="Spotify">
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
                    <img src="https://www.google.com/s2/favicons?domain=deezer.com&sz=32" alt="Deezer">
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
                    <span style="font-size:0.85rem;color:var(--pt-text-sec);">Total / mes</span>
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

    <!-- ═══ INTEGRACIÓN PLACETOPAY ═══ -->
    <section class="integration-docs" style="--code-accent:var(--plat-accent); --code-accent-ink:var(--plat-accent-ink); --code-accent-soft:rgba(var(--plat-accent-rgb),0.12); --code-radius-sm:var(--plat-radius-sm); --code-radius-md:var(--plat-radius-md); --code-radius-lg:var(--plat-radius-lg); --code-font:var(--plat-font);">
        <span class="integration-docs__badge"><i class="bi bi-braces"></i> Integración PlacetoPay</span>
        <h3>Así se procesa el pago de esta tienda</h3>
        <p>A diferencia de Web Checkout, aquí <strong>no hay redirección</strong>: los datos de tarjeta que llenas en este mismo panel viajan en el request, y <strong>PlaceToPay Gateway</strong> cobra el primer mes y tokeniza la tarjeta (<code>subscribe: true</code>) en una sola llamada, sin devolver un <code>processUrl</code>.</p>

        <div class="endpoint-bar">
            <span class="method-pill">POST</span>
            <span class="endpoint-url">https://api-test.placetopay.com/rest/gateway/process</span>
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
  <span class="jk">"payer"</span>: {
    <span class="jk">"name"</span>: <span class="js">"Andrés Torres"</span>,
    <span class="jk">"surname"</span>: <span class="js">""</span>,
    <span class="jk">"email"</span>: <span class="js">"usuario@correo.com"</span>,
    <span class="jk">"documentType"</span>: <span class="js">"CC"</span>,
    <span class="jk">"document"</span>: <span class="js">"1234567890"</span>,
    <span class="jk">"mobile"</span>: <span class="js">"3001234567"</span>
  },
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"GWMUS-9F3A2E1C"</span>,
    <span class="jk">"description"</span>: <span class="js">"Spotify — Individual"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: <span class="jn">14900</span> },
    <span class="cm">// clave: cobra el primer mes Y tokeniza la tarjeta</span>
    <span class="jk">"subscribe"</span>: <span class="jb">true</span>
  },
  <span class="jk">"instrument"</span>: {
    <span class="jk">"card"</span>: {
      <span class="jk">"number"</span>: <span class="js">"tok_************1111"</span>,
      <span class="jk">"expiration"</span>: <span class="js">"12/28"</span>,
      <span class="jk">"cvv"</span>: <span class="js">"***"</span>
    }
  },
  <span class="jk">"notificationUrl"</span>: <span class="js">"https://tu-dominio.com/php/notify.php"</span>,
  <span class="jk">"ipAddress"</span>: <span class="js">"203.0.113.42"</span>,
  <span class="jk">"userAgent"</span>: <span class="js">"Mozilla/5.0 (Windows NT 10.0; Win64; x64)"</span>
}</code></pre>
            <pre class="code-panel" data-key="php"><code>&lt;?php
<span class="cm">// credenciales fuera del código, nunca hardcodeadas</span>
<span class="cvar">$login</span>     = getenv(<span class="js">'P2P_LOGIN'</span>);
<span class="cvar">$secretKey</span> = getenv(<span class="js">'P2P_SECRET_KEY'</span>);
<span class="cvar">$endpoint</span>  = <span class="js">'https://api-test.placetopay.com/rest/gateway/process'</span>;

<span class="cm">// autenticación: Base64( SHA256( nonce + seed + secretKey ) )</span>
<span class="cvar">$seed</span>     = date(<span class="js">'c'</span>);
<span class="cvar">$nonce</span>    = bin2hex(random_bytes(16));
<span class="cvar">$tranKey</span>  = base64_encode(hash(<span class="js">'sha256'</span>, <span class="cvar">$nonce</span> . <span class="cvar">$seed</span> . <span class="cvar">$secretKey</span>, true));
<span class="cvar">$nonceB64</span> = base64_encode(<span class="cvar">$nonce</span>);

<span class="cm">// cuerpo del request — cobra el primer mes Y tokeniza la tarjeta</span>
<span class="cvar">$body</span> = [
    <span class="jk">'auth'</span> =&gt; [
        <span class="jk">'login'</span>   =&gt; <span class="cvar">$login</span>,
        <span class="jk">'tranKey'</span> =&gt; <span class="cvar">$tranKey</span>,
        <span class="jk">'nonce'</span>   =&gt; <span class="cvar">$nonceB64</span>,
        <span class="jk">'seed'</span>    =&gt; <span class="cvar">$seed</span>,
    ],
    <span class="jk">'payer'</span> =&gt; [
        <span class="jk">'name'</span>         =&gt; <span class="cvar">$nombre</span>,
        <span class="jk">'surname'</span>      =&gt; <span class="js">''</span>,
        <span class="jk">'email'</span>        =&gt; <span class="cvar">$correo</span>,
        <span class="jk">'documentType'</span> =&gt; <span class="cvar">$tipo_doc</span>,
        <span class="jk">'document'</span>     =&gt; <span class="cvar">$num_doc</span>,
        <span class="jk">'mobile'</span>       =&gt; <span class="cvar">$telefono</span>,
    ],
    <span class="jk">'payment'</span> =&gt; [
        <span class="jk">'reference'</span>   =&gt; <span class="js">'GWMUS-'</span> . strtoupper(bin2hex(random_bytes(4))),
        <span class="jk">'description'</span> =&gt; <span class="cvar">$servicio</span> . <span class="js">' — '</span> . <span class="cvar">$plan</span>,
        <span class="jk">'amount'</span>      =&gt; [<span class="jk">'currency'</span> =&gt; <span class="js">'COP'</span>, <span class="jk">'total'</span> =&gt; (float) <span class="cvar">$precio</span>],
        <span class="jk">'subscribe'</span>   =&gt; true,  <span class="cm">// ← clave: cobra + tokeniza en un solo paso</span>
    ],
    <span class="jk">'instrument'</span> =&gt; [
        <span class="jk">'card'</span> =&gt; [
            <span class="jk">'number'</span>     =&gt; <span class="cvar">$card_number</span>,
            <span class="jk">'expiration'</span> =&gt; <span class="cvar">$card_expiry</span>,
            <span class="jk">'cvv'</span>        =&gt; <span class="cvar">$card_cvv</span>,
        ],
    ],
    <span class="jk">'notificationUrl'</span> =&gt; <span class="cvar">$notifyUrl</span>,
    <span class="jk">'ipAddress'</span>       =&gt; <span class="cvar">$_SERVER</span>[<span class="js">'REMOTE_ADDR'</span>],
    <span class="jk">'userAgent'</span>       =&gt; <span class="cvar">$_SERVER</span>[<span class="js">'HTTP_USER_AGENT'</span>],
];

<span class="cvar">$ch</span> = curl_init(<span class="cvar">$endpoint</span>);
curl_setopt_array(<span class="cvar">$ch</span>, [
    CURLOPT_POST           =&gt; true,
    CURLOPT_RETURNTRANSFER =&gt; true,
    CURLOPT_HTTPHEADER     =&gt; [<span class="js">'Content-Type: application/json'</span>],
    CURLOPT_POSTFIELDS     =&gt; json_encode(<span class="cvar">$body</span>),
]);

<span class="cvar">$result</span> = json_decode(curl_exec(<span class="cvar">$ch</span>), true);
curl_close(<span class="cvar">$ch</span>);

<span class="cm">// el token de la tarjeta viene en la respuesta, listo para renovar el mes siguiente</span>
<span class="cvar">$token</span> = <span class="cvar">$result</span>[<span class="js">'subscription'</span>][<span class="js">'token'</span>][<span class="js">'token'</span>] ?? <span class="js">''</span>;</code></pre>
        </div>

        <div class="doc-note">
            <span class="doc-note-icon">⚠️</span>
            <span>Como los datos de tarjeta pasan por nuestra página, este flujo requiere <strong>certificación PCI-DSS</strong> en producción. El <code>token</code> que devuelve PlacetoPay reemplaza la tarjeta para los cobros de los meses siguientes — nunca volvemos a pedir ni guardar el número real.</span>
        </div>

        <a class="integration-docs__link" href="../guias/guia-developer.php#api-gateway">
            <div>
                <strong>¿Quieres entender esta integración a fondo?</strong>
                <span>Lee la documentación completa de API Gateway — alcance PCI-DSS, 3D Secure y más.</span>
            </div>
            <i class="bi bi-arrow-right"></i>
        </a>
    </section>

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
    <script src="../../assets/js/code-block.js"></script>
</body>
</html>
