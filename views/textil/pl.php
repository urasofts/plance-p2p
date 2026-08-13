<?php
session_start();
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) { header("Location: ../../index.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premier League — Kits</title>
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
        --bg-base:var(--pt-bg-base); --bg-surface:var(--pt-bg-surface); --bg-card:var(--pt-navbar);
        --bg-card-hover:var(--pt-hover); --bg-selected:var(--pt-border); --border:var(--pt-border);
        --accent:#3b82f6; --accent-glow:rgba(59,130,246,0.25); --accent-dark:#2563eb;
        --text-primary:var(--pt-text); --text-secondary:var(--pt-text-sec); --text-muted:var(--pt-text-sec);
        --font-display:'Barlow',sans-serif; --font-body:'Barlow',sans-serif;
        --radius-sm:6px; --radius-md:10px; --radius-lg:14px;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{background-color:var(--bg-base);color:var(--text-primary);font-family:var(--font-body);min-height:100vh;-webkit-font-smoothing:antialiased;}
    .navbar{background-color:var(--pt-navbar)!important;backdrop-filter:blur(8px);border-bottom:1px solid var(--border);}

    .game-banner{display:flex;align-items:center;justify-content:space-between;padding:0.6rem 2rem;background:var(--pt-th2);border-bottom:1px solid var(--border);gap:1rem;flex-wrap:wrap;}
    .game-banner__tag{display:flex;align-items:center;gap:0.5rem;font-family:var(--font-display);font-weight:700;font-size:1rem;letter-spacing:0.04em;color:var(--text-primary);}
    .link-badge{background:rgba(59,130,246,0.15);color:var(--accent);font-size:0.72rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:20px;letter-spacing:0.05em;font-family:var(--font-display);}

    .shop-layout{display:grid;grid-template-columns:1fr 360px;gap:1.5rem;max-width:1200px;margin:1.5rem auto;padding:0 1.5rem 3rem;align-items:start;}

    .section-label{font-family:var(--font-display);font-size:0.8rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:0.75rem;}
    .products-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0.65rem;}

    .product-card{position:relative;background:var(--bg-card);border:1.5px solid var(--border);border-radius:var(--radius-md);padding:0.85rem 0.75rem 0.9rem;cursor:pointer;transition:all 0.18s ease;display:flex;flex-direction:column;gap:0.1rem;overflow:hidden;}
    .product-card:hover{background:var(--bg-card-hover);border-color:rgba(59,130,246,0.4);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.35);}
    .product-card.selected{background:var(--bg-selected);border-color:var(--accent);box-shadow:0 0 0 1px var(--accent),0 4px 24px var(--accent-glow);}
    .product-card.selected::after{content:'✔';position:absolute;top:0.5rem;right:0.55rem;width:18px;height:18px;background:var(--accent);border-radius:50%;color:#fff;font-size:0.65rem;display:flex;align-items:center;justify-content:center;font-weight:900;line-height:18px;text-align:center;}
    .product-card__img{width:56px;height:56px;margin:0 auto 0.5rem;display:flex;align-items:center;justify-content:center;font-size:1.8rem;background:var(--bg-base);border-radius:8px;overflow:hidden;border:1px solid var(--border);}
    .product-card__img img{width:100%;height:100%;object-fit:contain;display:block;}
    .product-card__name{font-family:var(--font-display);font-size:0.95rem;font-weight:800;color:var(--text-primary);line-height:1.1;text-align:center;}
    .product-card__label{font-size:0.7rem;color:var(--text-secondary);margin-bottom:0.25rem;text-align:center;}
    .product-card__price{font-family:var(--font-display);font-size:0.9rem;font-weight:700;color:var(--accent);margin-top:auto;text-align:center;}

    /* CHECKOUT */
    .checkout-panel{display:flex;flex-direction:column;gap:1rem;position:sticky;top:16px;}
    .checkout-box{background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.4rem;}

    .checkout-header{display:flex;gap:0.85rem;align-items:center;margin-bottom:0.85rem;padding-bottom:0.85rem;border-bottom:1px solid var(--border);}
    .checkout-product-img{width:64px;height:64px;min-width:64px;background:var(--bg-card);border:2px solid var(--border);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;font-size:2rem;overflow:hidden;flex-shrink:0;position:relative;}
    .checkout-product-img img{width:100%;height:100%;object-fit:contain;display:block;}
    .checkout-product-info{display:flex;flex-direction:column;gap:0.25rem;flex:1;min-width:0;}
    .checkout-product-name{font-family:var(--font-display);font-size:1.05rem;font-weight:800;color:var(--text-primary);line-height:1.2;}
    .checkout-product-label{font-size:0.72rem;color:var(--text-secondary);margin-bottom:0.15rem;}
    .checkout-price-row{display:flex;justify-content:space-between;align-items:baseline;gap:0.5rem;}
    .checkout-price{font-family:var(--font-display);font-size:1.35rem;font-weight:800;color:var(--accent);}
    .checkout-divider{height:1px;background:var(--border);margin:0.8rem 0;}
    .section-label-sm{font-family:var(--font-display);font-size:0.73rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:0.5rem;display:block;}

    .field-group{margin-bottom:0.65rem;}
    .field-label{font-size:0.73rem;font-weight:600;color:var(--text-secondary);margin-bottom:0.25rem;display:block;}
    .field-input{width:100%;background:var(--pt-border);border:1.5px solid var(--border);border-radius:8px;color:var(--text-primary);font-family:var(--font-body);font-size:0.83rem;padding:0.4rem 0.7rem;outline:none;transition:border-color 0.2s;}
    .field-input:focus{border-color:var(--accent);}
    .field-input::placeholder{color:var(--text-muted);}

    /* Info link de pago */
    .link-info{background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);border-radius:8px;padding:0.75rem 1rem;margin-bottom:0.8rem;font-size:0.8rem;color:#93c5fd;display:flex;gap:0.5rem;align-items:flex-start;line-height:1.5;}

    .btn-generar{width:100%;padding:0.85rem;background:var(--accent);border:none;border-radius:var(--radius-md);color:#fff;font-family:var(--font-display);font-size:1.05rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;cursor:pointer;transition:all 0.18s ease;display:flex;align-items:center;justify-content:center;gap:0.5rem;margin-top:0.5rem;}
    .btn-generar:hover{background:var(--accent-dark);transform:translateY(-1px);box-shadow:0 6px 20px var(--accent-glow);}
    .security-note{display:flex;align-items:center;gap:0.4rem;font-size:0.73rem;color:var(--text-muted);margin-top:0.5rem;justify-content:center;}

    @keyframes fadeSlideIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
    .products-panel{animation:fadeSlideIn 0.4s ease both;}
    .checkout-panel{animation:fadeSlideIn 0.4s 0.1s ease both;}
    @media(max-width:900px){.shop-layout{grid-template-columns:1fr;}.checkout-panel{position:static;}.products-grid{grid-template-columns:repeat(3,1fr);}}
    @media(max-width:600px){.products-grid{grid-template-columns:repeat(2,1fr);}.game-banner{flex-direction:column;align-items:flex-start;}}
</style>
<body>
    <?php
    $nav_back_url  = "textiles.php";
    $nav_back_text = "Atras";
    $nav_base      = "../../";
    require_once '../../php/navbar.php';
    ?>

    <div class="game-banner">
        <div class="game-banner__tag">
            <i class="fa-solid fa-futbol" style="color: hsl(207, 100%, 62%);"></i> Premier League — Kits Deportivos
            <span class="link-badge">🔗 Link de Pago</span>
        </div>
    </div>

    <main class="shop-layout">
        <section class="products-panel">
            <p class="section-label">Elige tu equipación</p>
            <div class="products-grid">

                <div class="product-card" data-id="1" data-producto="Kit Liverpool FC" data-precio="50000">
                    <div class="product-card__img">
                        <img src="../assets/kits/premier-league/liv.png" alt="Liverpool FC">
                    </div>
                    <div class="product-card__name">Liverpool FC</div>
                    <div class="product-card__label">Camiseta · Temporada 17/18</div>
                    <div class="product-card__price">50.000 COP</div>
                </div>

                <div class="product-card" data-id="2" data-producto="Kit Manchester City" data-precio="50000">
                    <div class="product-card__img">
                        <img src="../assets/kits/premier-league/mcity.png" alt="Manchester City">
                    </div>
                    <div class="product-card__name">Manchester City</div>
                    <div class="product-card__label">Camiseta · Temporada 18/19</div>
                    <div class="product-card__price">50.000 COP</div>
                </div>

                <div class="product-card" data-id="3" data-producto="Kit Manchester United" data-precio="50000">
                    <div class="product-card__img">
                        <img src="../assets/kits/premier-league/mufc.png" alt="Manchester United">
                    </div>
                    <div class="product-card__name">Manchester United</div>
                    <div class="product-card__label">Camiseta · Temporada 18/19</div>
                    <div class="product-card__price">50.000 COP</div>
                </div>
                
                <div class="product-card" data-id="4" data-producto="Kit Chelsea FC" data-precio="50000">
                    <div class="product-card__img">
                        <img src="../assets/kits/premier-league/chelsea.png" alt="Chelsea FC">
                    </div>
                    <div class="product-card__name">Chelsea FC</div>
                    <div class="product-card__label">Camiseta · Temporada 23/24</div>
                    <div class="product-card__price">50.000 COP</div>
                </div>

                <div class="product-card" data-id="5" data-producto="Kit Arsenal FC" data-precio="50000">
                    <div class="product-card__img">
                        <img src="../assets/kits/premier-league/arsenal.png" alt="Arsenal FC">
                    </div>
                    <div class="product-card__name">Arsenal FC</div>
                    <div class="product-card__label">Camiseta · Temporada 24/25</div>
                    <div class="product-card__price">50.000 COP</div>
                </div>

                <div class="product-card" data-id="6" data-producto="Kit West Ham United" data-precio="50000">
                    <div class="product-card__img">
                        <img src="../assets/kits/premier-league/west-ham.png" alt="West Ham United">
                    </div>
                    <div class="product-card__name">West Ham United</div>
                    <div class="product-card__label">Camiseta · Temporada 24/25</div>
                    <div class="product-card__price">50.000 COP</div>
                </div>

                <div class="product-card" data-id="7" data-producto="Kit Tottenham Hotspur" data-precio="50000">
                    <div class="product-card__img">
                        <img src="../assets/kits/premier-league/tot.png" alt="Tottenham Hotspur">
                    </div>
                    <div class="product-card__name">Tottenham Hotspur</div>
                    <div class="product-card__label">Camiseta · Temporada 22/23</div>
                    <div class="product-card__price">50.000 COP</div>
                </div>

                <div class="product-card" data-id="8" data-producto="Kit Aston Villa" data-precio="50000">
                    <div class="product-card__img">🟣</div>
                    <div class="product-card__name">Aston Villa</div>
                    <div class="product-card__label">Camiseta · Temporada 24/25</div>
                    <div class="product-card__price">50.000 COP</div>
                </div>

            </div>
        </section>

        <!-- CHECKOUT -->
        <aside class="checkout-panel">
            <div class="checkout-box">
                <div class="checkout-header">
                    <div class="checkout-product-img" id="checkoutImg">🔴</div>
                    <div class="checkout-product-info">
                        <div class="checkout-product-name" id="checkoutName">Liverpool FC</div>
                        <div class="checkout-product-label">Camiseta · Temporada 24/25</div>
                        <div class="checkout-price-row">
                            <span style="font-size:0.85rem;color:var(--text-secondary);">Total</span>
                            <span class="checkout-price">50.000 COP</span>
                        </div>
                    </div>
                </div>

                <div class="link-info">
                    <i class="bi bi-link-45deg" style="font-size:1rem;flex-shrink:0;"></i>
                    <span>Se generará un <strong>link de pago</strong> que podrás compartir por correo, WhatsApp o redes sociales. El link expira en 24 horas.</span>
                </div>

                <div class="checkout-divider"></div>
                <span class="section-label-sm">Datos del comprador</span>

                <div class="field-group">
                    <label class="field-label">Correo electrónico</label>
                    <input type="email" class="field-input" id="correoInput"
                           value="<?php echo htmlspecialchars($_SESSION['correo'] ?? ''); ?>"
                           placeholder="correo@ejemplo.com">
                </div>
                <div class="field-group">
                    <label class="field-label">Nombre completo</label>
                    <input type="text" class="field-input" id="nombreInput" placeholder="Nombre y apellido">
                </div>

                <button class="btn-generar" id="btnGenerar">
                    <i class="bi bi-link-45deg"></i> Generar link de pago
                </button>
                <div class="security-note">
                    <i class="bi bi-shield-check"></i>
                    Link de Pago · PlacetoPay · Evertec
                </div>
            </div>
        </aside>
    </main>

    <script>
    (function() {
        // ====================================================================
        // 🎨 SISTEMA AUTOMÁTICO DE IMÁGENES
        // ====================================================================
        // El sistema detecta automáticamente si una card tiene imagen PNG
        // y la sincroniza con el checkout. Solo agrega tus imágenes en el HTML.
        // ====================================================================

        function updateCheckoutFromCard(card) {
            const cardImg = card.querySelector('.product-card__img');
            const checkoutImg = document.getElementById('checkoutImg');
            
            // Verificar si la card tiene una imagen <img> o solo emoji/texto
            const imgElement = cardImg.querySelector('img');
            
            if (imgElement) {
                // Tiene imagen PNG - clonarla al checkout
                checkoutImg.innerHTML = '<img src="' + imgElement.src + '" alt="' + imgElement.alt + '">';
            } else {
                // Solo tiene emoji/texto - copiar el contenido
                checkoutImg.innerHTML = cardImg.innerHTML;
            }
            
            // Actualizar nombre del producto
            const productName = card.querySelector('.product-card__name').textContent;
            document.getElementById('checkoutName').textContent = productName;
        }

        function initCards() {
            const cards = document.querySelectorAll('.product-card');
            if (cards.length === 0) { setTimeout(initCards, 100); return; }
            
            cards.forEach(function(card) {
                card.addEventListener('click', function() {
                    cards.forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    updateCheckoutFromCard(card);
                });
            });
            
            // Seleccionar el primer producto por defecto
            var def = document.querySelector('.product-card[data-id="1"]');
            if (def) {
                def.classList.add('selected');
                updateCheckoutFromCard(def);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCards);
        } else { initCards(); }

        document.getElementById('btnGenerar').addEventListener('click', function() {
            const selected = document.querySelector('.product-card.selected');
            if (!selected) { alert('⚠️ Selecciona una equipación primero.'); return; }

            const correo = document.getElementById('correoInput').value.trim();
            const nombre = document.getElementById('nombreInput').value.trim();

            if (!correo) { alert('⚠️ Por favor ingresa tu correo electrónico.'); return; }
            if (!nombre) { alert('⚠️ Por favor ingresa tu nombre.'); return; }

            const producto = selected.getAttribute('data-producto');
            const precio   = selected.getAttribute('data-precio');

            const btn = document.getElementById('btnGenerar');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando link...';

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../php/crear_link_pago.php';

            [['producto', producto], ['precio', precio],
             ['correo', correo], ['nombre', nombre]].forEach(function(pair) {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = pair[0]; input.value = pair[1];
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




























