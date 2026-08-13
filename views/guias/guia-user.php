<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Guía de Usuario | Plance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
  <style>
    
    :root {
      --accent:       #f07929;
      --accent2:      rgba(255, 157, 45, 0.25);
      --accent-soft:  rgba(240, 180, 41, 0.12);
      --accent-glow:  rgba(240, 180, 41, 0.25);
      --muted:        var(--pt-text-sec);
    }

    * { box-sizing: border-box; }

    html, body {
      margin: 0; padding: 0;
      background: var(--pt-bg-base);
      color: var(--pt-text);
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
      min-height: 100%;
    }

    .layout {
      display: grid;
      grid-template-columns: 280px 1fr;
      min-height: 100vh;
    }

    /* SIDEBAR */
    .sidebar {
      background: var(--pt-boxitem);
      /* border-right: 1px solid var(--pt-border); */
      padding: 20px 16px;
      position: sticky;
      top: 0;
      align-self: start;
      height: 100vh;
      overflow-y: auto;
    }

    .brand {
      display: flex; align-items: center; gap: 12px;
      margin-bottom: 22px; padding: 14px;
      border-radius: 14px;
      background: var(--pt-bg-secondary);
      border: 1px solid rgba(240,180,41,0.22);
    }
    .brand-logo {
      width: 42px; height: 42px; border-radius: 11px;
      display: grid; place-items: center; flex: 0 0 auto;
      background: var(--accent-soft);
      border: 1px solid rgba(240,180,41,0.35);
      color: var(--accent); font-size: 20px;
      box-shadow: 0 0 18px var(--accent-soft);
    }
    .brand-text { min-width: 0; }
    .brand-text h1 { font-size: 15px; margin: 0; line-height: 1.25; font-weight: 700; }
    .brand-text p  {
      margin: 3px 0 0; color: var(--muted); font-size: 11.5px;
      display: flex; align-items: center; gap: 6px;
    }
    .brand-dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: var(--accent);
      box-shadow: 0 0 0 4px var(--accent-soft);
      flex: 0 0 auto;
    }

    .nav-group { margin-top: 14px; }
    .nav-title {
      width: 100%;
      display: flex; align-items: center; justify-content: space-between;
      margin: 0 0 8px; padding: 8px 10px;
      background: transparent; border: 1px solid transparent;
      border-radius: 8px; cursor: pointer;
      font-size: 11px; letter-spacing: 0.08em;
      text-transform: uppercase; color: #6a6e78; font-weight: 700;
      transition: all 0.15s;
      font-family: inherit;
    }
    .nav-title:hover { background: rgba(128,128,128,0.08); color: var(--accent); }
    .nav-title > span { display: flex; align-items: center; gap: 8px; }
    .nav-chevron { font-size: 12px; transition: transform 0.25s ease; }
    .nav-title.collapsed .nav-chevron { transform: rotate(-90deg); }

    .nav {
      list-style: none; margin: 0; padding: 0;
      display: grid; gap: 3px;
      overflow: hidden;
      max-height: 600px;
      transition: max-height 0.3s ease, opacity 0.25s ease;
    }
    .nav.collapsed { max-height: 0; opacity: 0; }
    .nav a {
      color: var(--muted); text-decoration: none;
      display: flex; align-items: center; gap: 9px;
      padding: 8px 10px;
      border-radius: 8px; border: 1px solid transparent; font-size: 13.5px;
      transition: all 0.15s;
    }
    .nav a i { font-size: 14px; width: 16px; text-align: center; opacity: 0.8; }
    .nav a:hover, .nav a.active {
      background: var(--accent-soft);
      border-color: rgba(240,180,41,0.2);
      color: var(--accent);
    }
    .nav a.active i, .nav a:hover i { opacity: 1; }

    /* CONTENT */
    .content {
      min-width: 0;
      background: var(--pt-bg-secondary);
    }

    .topbar {
      position: sticky; top: 0; z-index: 10;
      display: flex; align-items: center; justify-content: space-between;
      gap: 10px; padding: 14px 22px;
      /* border-bottom: 1px solid var(--pt-border); */
      background: var(--pt-navbar);
      backdrop-filter: blur(12px);
    }
    .topbar h2 { margin: 0; font-size: 15px; font-weight: 600; color: var(--pt-text); }
    .search {
      width: 280px; max-width: 100%;
      background: var(--pt-boxitem); border: 1px solid var(--pt-border);
      color: var(--pt-text); border-radius: 10px;
      padding: 8px 12px; font-size: 13.5px; outline: none;
      transition: border-color 0.2s;
    }
    .search:focus { border-color: rgba(240,180,41,0.4); }

    .container {
      width: min(1100px, 100%);
      margin: 0 auto;
      padding: 28px 22px 60px;
      display: grid; gap: 32px;
    }

    /* HERO */
    .hero {
      display: grid; grid-template-columns: 1.2fr 1fr;
      gap: 24px; align-items: center;
      background: var(--pt-lightbox);
      /* border: 1px solid var(--pt-border); */
      border-top: 2px solid var(--accent);
      border-radius: 16px; padding: 28px;
    }
    .hero h3 {
      margin: 0 0 12px;
      font-size: clamp(24px, 3vw, 34px); line-height: 1.15;
    }
    .hero h3 span { color: var(--accent); }
    .hero p { margin: 0; color: var(--pt-text); line-height: 1.75; font-size: 15px; }

    .hero-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--accent-soft); border: 1px solid rgba(240,180,41,0.3);
      color: var(--accent); border-radius: 20px;
      padding: 4px 12px; font-size: 12px; font-weight: 600;
      margin-bottom: 12px; letter-spacing: 0.04em;
    }

    .image-placeholder {
      border: 1px dashed rgba(240,180,41,0.2);
      background: rgba(240,180,41,0.03);
      border-radius: 14px; min-height: 200px;
      display: flex; align-items: center; justify-content: center;
      text-align: center; color: #555860; font-size: 13px; padding: 16px;
    }
    .image-fluid { max-width: 100%; height: auto; }

    /* Desplegable de capturas con resumen */
    .shots-dropdown {
      margin-top: 16px;
      border: 1px solid var(--pt-border);
      border-radius: 12px;
      overflow: hidden;
      background: var(--pt-dropdown);
    }
    .shots-dropdown summary {
      cursor: pointer;
      padding: 12px 16px;
      font-weight: 600;
      font-size: 14px;
      color: var(--accent);
      list-style: none;
      display: flex;
      align-items: center;
      justify-content: space-between;
      user-select: none;
    }
    .shots-dropdown summary::-webkit-details-marker { display: none; }
    .shots-dropdown summary::after {
      content: '▾';
      color: #6b7280;
      transition: transform 0.2s ease;
      margin-left: 8px;
    }
    .shots-dropdown[open] summary::after { transform: rotate(180deg); }
    .shots-dropdown summary:hover { background: rgba(240,180,41,0.05); }
    .shots-summary {
      margin: 0;
      padding: 0 16px 10px;
      font-size: 12.5px;
      color: #93a5be;
      font-style: italic;
      line-height: 1.5;
    }
    .shots-dropdown .split { margin: 0 16px 16px !important; }

    /* Contenedores que si tienen una captura dentro: sin padding, la imagen llena todo */
    .image-placeholder:has(img) {
      padding: 0;
      overflow: hidden;
      border-style: solid;
      border-color: var(--pt-border);
      background: var(--pt-lightbox);
      aspect-ratio: 16 / 10;
    }
    .image-placeholder img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      cursor: zoom-in;
      transition: transform 0.35s ease, filter 0.2s ease;
    }
    .image-placeholder:has(img):hover img {
      transform: scale(1.04);
      filter: brightness(1.05);
    }

    /* LIGHTBOX (vista previa ampliada de capturas) */
    .img-lightbox {
      position: fixed; inset: 0; z-index: 2000;
      background: rgba(6,6,8,0.78);
      backdrop-filter: blur(10px) saturate(120%);
      -webkit-backdrop-filter: blur(10px) saturate(120%);
      display: flex; align-items: center; justify-content: center;
      padding: 40px; cursor: zoom-out;
      opacity: 0; visibility: hidden;
      transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .img-lightbox.active { opacity: 1; visibility: visible; }
    .img-lightbox img {
      max-width: min(92vw, 1100px); max-height: 88vh;
      width: auto; height: auto;
      border-radius: 12px;
      box-shadow: 0 25px 70px rgba(0,0,0,0.65), 0 0 0 1px rgba(240,180,41,0.15);
      border: 1px solid var(--pt-border);
      transform: scale(0.94);
      transition: transform 0.28s ease;
      cursor: default;
    }
    .img-lightbox.active img { transform: scale(1); }
    .lightbox-close {
      position: absolute; top: 22px; right: 28px;
      width: 42px; height: 42px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.15);
      color: #fff; font-size: 1.4rem; line-height: 1; cursor: pointer;
      transition: background 0.2s, transform 0.2s;
    }
    .lightbox-close:hover { background: rgba(240,180,41,0.25); transform: rotate(90deg); }
    body.lightbox-open { overflow: hidden; }

    /* SECTIONS */
    .section {
      /* border: 1px solid var(--pt-border); */
      background: var(--pt-lightbox);
      border-radius: 14px; padding: 24px;
    }
    .section-header {
      display: flex; align-items: center; gap: 10px; margin-bottom: 12px;
    }
    .section-icon {
      width: 36px; height: 36px; border-radius: 10px;
      background: var(--accent-soft); border: 1px solid rgba(240,180,41,0.25);
      display: flex; align-items: center; justify-content: center;
      font-size: 17px; flex-shrink: 0;
    }
    .section h4 { margin: 0; font-size: 20px; }
    .section > p { margin: 0 0 16px; color: var(--pt-text-sec); line-height: 1.7; font-size: 14.5px; }

    .split {
      display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px;
    }

    /* CARDS */
    .cards {
      display: grid; grid-template-columns: repeat(3, minmax(0,1fr));
      gap: 14px; margin-top: 16px;
    }
    .card {
      background: var(--pt-bg-card2);
      /* border: 1px solid var(--pt-border); */
      border-radius: 12px; padding: 18px;
      transition: border-color 0.2s, transform 0.2s;
    }
    .card:hover { border-color: rgba(240,180,41,0.3); transform: translateY(-2px); }
    .card-icon { font-size: 1.5rem; margin-bottom: 10px; }
    .card h5 { margin: 0 0 8px; font-size: 15px; color: var(--pt-text); }
    .card p  { margin: 0; color: var(--pt-text-sec); font-size: 13.5px; line-height: 1.6; }
    .card-tag {
      display: inline-block; margin-top: 10px;
      background: var(--accent-soft); color: var(--accent);
      font-size: 11px; font-weight: 700; padding: 2px 8px;
      border-radius: 20px; letter-spacing: 0.04em;
    }

    /* STEPS */
    .steps { display: grid; gap: 10px; margin-top: 16px; }
    .step {
      display: grid; grid-template-columns: 36px 1fr;
      gap: 14px; align-items: start; padding: 14px;
      border-radius: 12px; border: 1px solid var(--pt-border);
      background: var(--pt-bg-card2);
      transition: border-color 0.2s;
    }
    .step:hover { border-color: rgba(240,180,41,0.25); }
    .step-index {
      width: 36px; height: 36px; border-radius: 50%;
      background: var(--accent-soft);
      border: 1px solid rgba(240,180,41,0.4);
      color: var(--accent);
      display: grid; place-items: center;
      font-weight: 800; font-size: 14px; flex-shrink: 0;
    }
    .step h6 { margin: 0 0 5px; font-size: 15px; color: var(--pt-text); }
    .step p  { margin: 0; color: var(--pt-text-sec); font-size: 13.5px; line-height: 1.6; }

    /* INFO BOXES */
    .info-box {
      display: flex; gap: 10px; align-items: flex-start;
      padding: 12px 14px; border-radius: 10px; margin-top: 14px;
      font-size: 13.5px; line-height: 1.6;
    }
    .info-box.tip {
      background: rgba(240,180,41,0.07);
      border: 1px solid rgba(240,180,41,0.2); color: #c99010;
    }
    .info-box.warning {
      background: rgba(224,82,82,0.07);
      border: 1px solid rgba(224,82,82,0.2); color: #e05252;
    }
    .info-box-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }

    /* TAGS TIPO PAGO */
    .tipos-pago {
      display: grid; grid-template-columns: repeat(2, 1fr);
      gap: 12px; margin-top: 14px;
    }
    .tipo-card {
      background: var(--pt-bg-card2); border: 1px solid var(--pt-border);
      border-radius: 12px; padding: 16px;
    }
    .tipo-card h6 { margin: 0 0 6px; font-size: 14px; color: var(--accent); }
    .tipo-card p  { margin: 0; color: var(--pt-text-sec); font-size: 13px; line-height: 1.6; }

    /* COLORES PARA LOS ICONOS */
    .icon-colors {
      color: var(--accent);
    }


    @media (max-width: 1100px) {
      .layout { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
      .hero, .split, .cards, .tipos-pago { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<div class="layout">

  <!-- SIDEBAR -->
  <?php include 'sidebar-guia-user.php'; ?>

  <!-- MAIN -->
  <main class="content">
    <header class="topbar">
      <a href="../../home.php"><i class="icon-colors bi bi-backspace-fill" style="font-size: 1.5rem;"></i></a>
      <h2>Centro de Ayuda — Usuarios</h2>
      <input class="search" type="text" placeholder="🔍 Buscar en la guía...">
      
    </header>

    <div class="container">

      <!-- HERO -->
      <section class="hero" id="introduccion">
        <div>
          <div class="hero-badge"><i class="icon-colors bi bi-book-fill"></i> Guía para usuarios</div>
          <h3>Bienvenido <span> Usuario</span></h3>
          <p>
            ESta es una plataforma <strong>DEMO</strong> donde el usuario simula pagar recargas de juegos, suscripciones de streaming, membresías y mucho más — todo desde un solo lugar, de forma rápida y segura gracias a <strong>PlacetoPay</strong>.
            <br><br>
            Aquí te explicamos paso a paso cómo usarla, sin complicaciones. 😊
          </p>
        </div>
        <div class="image-placeholder">  
          <img src="../assets/images/plance2.jpg" alt="imagen" class="img-fluid">
        </div>
         
        
      </section>

      <!-- CÓMO FUNCIONA -->
      <section class="section" id="como-funciona">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors fa-solid fa-clipboard"></i></div>
          <h4>¿Cómo funciona?</h4>
        </div>
        <p>Es muy sencillo. Escoge lo que quieres comprar, completa el pago con tu tarjeta o cuenta bancaria, y listo. Todo queda registrado en tu historial para que puedas consultarlo cuando quieras.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Inicia sesión en tu cuenta</h6>
              <p>Entra con tu correo y contraseña. Si aún no tienes cuenta, regístrate gratis en menos de un minuto.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Elige lo que quieres</h6>
              <p>Navega entre juegos, plataformas de streaming, membresías o planes de IA. Selecciona el producto que más te convenga.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Completa tu pago</h6>
              <p>Ingresa los datos de tu tarjeta o cuenta bancaria en la pasarela de PlacetoPay. Es un proceso 100% seguro.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">4</div>
            <div>
              <h6>¡Listo! Recibe tu confirmación</h6>
              <p>Una vez aprobado el pago, recibirás la confirmación en pantalla y quedará guardada en tu historial.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- SERVICIOS -->
      <section class="section" id="servicios">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-bag-check-fill"></i></div>
          <h4>¿Qué puedo comprar?</h4>
        </div>
        <p>La web ofrece varios tipos de productos y servicios. Aquí te contamos qué hay disponible:</p>

        <div class="cards">
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-controller"></i></div>
            <h5>Recargas de juegos</h5>
            <p>Compra monedas, puntos o pases de batalla para tus juegos favoritos como Call of Duty, Free Fire, PUBG y más.</p>
            <span class="card-tag">Pago único</span>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-tv"></i></div>
            <h5>Streaming</h5>
            <p>Activa o renueva tu suscripción a plataformas como Netflix, Amazon Prime, Crunchyroll, Star+ y más.</p>
            <span class="card-tag">Suscripción</span>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-patch-check"></i></div>
            <h5>Membresías y verificados</h5>
            <p>Obtén tu verificado en redes sociales como X (Twitter) o Meta, o activa YouTube Premium con cobro mensual automático.</p>
            <span class="card-tag">Recurrente</span>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-robot"></i></div>
            <h5>Planes de IA</h5>
            <p>Activa planes de inteligencia artificial como Claude Pro o ChatGPT Plus con renovación mensual o anual.</p>
            <span class="card-tag">Recurrente</span>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-music-note-beamed"></i></div>
            <h5>Música</h5>
            <p>Suscríbete a Spotify o Deezer y disfruta de música sin anuncios. Tu tarjeta queda guardada de forma segura.</p>
            <span class="card-tag">Suscripción</span>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-stars"></i></div>
            <h5>Otras plataformas</h5>
            <p>Encuentra más opciones de streaming y entretenimiento disponibles en la sección de otras plataformas.</p>
            <span class="card-tag">Suscripción</span>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-building"></i></div>
            <h5>Reservas de hotel</h5>
            <p>Reserva habitaciones en el Hotel Plance. Tu tarjeta no se cobra hasta el check-out.</p>
            <span class="card-tag">Preautorización</span>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-airplane"></i></div>
            <h5>Tiquetes aéreos</h5>
            <p>Compra tiquetes a destinos en Suramérica, Europa y Asia. El pago se reparte entre aerolínea e impuestos.</p>
            <span class="card-tag">Dispersión</span>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-tshirt"></i></div>
            <h5>Equipaciones deportivas</h5>
            <p>Compra camisetas de tus equipos favoritos o genera un link de pago para que alguien más pague por ti.</p>
            <span class="card-tag">Link de Pagos</span>
          </article>
        </div>
      </section>

      <!-- TIPOS DE PAGO -->
      <section class="section" id="tipos-pago">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-credit-card"></i></div>
          <h4>¿Cómo puedo pagar?</h4>
        </div>
        <p>Aqui puedes encontrar distintas formas de pago según el servicio que elijas. Todas son seguras y están respaldadas por PlacetoPay.</p>

        <div class="tipos-pago">
          <div class="tipo-card">
            <h6><i class="bi bi-credit-card-2-back-fill fs-5 " style="color: rgb(255, 251, 0)" > </i> Pago único (Web Checkout)</h6>
            <p>Pagas una sola vez por tu producto. Eres redirigido a la página segura de PlacetoPay, ingresas tus datos y listo. Ideal para recargas de juegos.</p>
          </div>
          <div class="tipo-card">
            <h6><i class="bi bi-shield-fill-check fs-5 text-success" ></i> Suscripción con token</h6>
            <p>Pagas el primer mes y, si lo deseas, puedes guardar tu tarjeta de forma segura para que los siguientes cobros sean automáticos. Típico en plataformas de streaming.</p>
          </div>
          <div class="tipo-card">
            <h6><i class="bi bi-arrow-repeat fs-5 text-info"></i> Cobro recurrente</h6>
            <p>Tu tarjeta se cobra automáticamente cada mes o cada año sin que tengas que hacer nada. Aplica a membresías de redes sociales y planes de IA.</p>
          </div>
          <div class="tipo-card">
            <h6><i class="bi bi-bank fs-5 " style="color: #c0cad8"></i> Cuenta bancaria / PSE</h6>
            <p>En algunos servicios puedes pagar directamente desde tu cuenta bancaria a través de PSE. Solo necesitas los datos de tu banco.</p>
          </div>
          <div class="tipo-card">
            <h6><i class="bi bi-cpu fs-5" style="color: #f0b429"></i> API Gateway</h6>
            <p>El pago se procesa directo en la misma página, sin redirigir a otro sitio. Usado en tiendas como PUBG, Blood Strike, Streaming y Música.</p>
          </div>
          <div class="tipo-card">
            <h6><i class="bi bi-link-45deg fs-5" style="color: #3b82f6"></i> Link de Pagos</h6>
            <p>Genera un enlace de cobro que puedes compartir por WhatsApp o correo, para que otra persona pague por ti.</p>
          </div>
          <div class="tipo-card">
            <h6><i class="bi bi-shuffle fs-5" style="color: #6366f1"></i> Pago mixto (parcial)</h6>
            <p>Paga solo una parte del total ahora y completa el resto después, sin fecha límite para saldar el pendiente.</p>
          </div>
          <div class="tipo-card">
            <h6><i class="bi bi-shield-lock fs-5" style="color: #a5b4fc"></i> Preautorización</h6>
            <p>El monto se aparta en tu tarjeta sin cobrarse — se usa en reservas de hotel y se cobra realmente al hacer check-out.</p>
          </div>
          <div class="tipo-card">
            <h6><i class="bi bi-diagram-3 fs-5" style="color: #10b981"></i> Dispersión</h6>
            <p>Tu pago se reparte automáticamente entre varios destinatarios en una sola transacción — usado en la compra de tiquetes.</p>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>Tip:</strong> Si guardas tu tarjeta al pagar, la próxima vez no tendrás que ingresarla de nuevo. PlacetoPay guarda solo un token seguro — nunca el número real de tu tarjeta.</span>
        </div>
      </section>

      <!-- HISTORIAL -->
      <section class="section" id="historial">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-clipboard-data"></i></div>
          <h4>Tu historial de pagos</h4>
        </div>
        <p>En la sección de <strong>Historial</strong> puedes ver todos tus movimientos: recargas, suscripciones y membresías. Desde ahí también puedes verificar si un pago quedó pendiente.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index"><i class="bi bi-box-seam-fill" style="color: #f0b429;"></i></div>
            <div>
              <h6>Pagos básicos</h6>
              <p>Aquí aparecen todas tus recargas de juegos. Puedes ver el estado de cada una: aprobada, pendiente o rechazada.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-box-seam-fill" style="color: rgb(129, 94, 255);"></i></div>
            <div>
              <h6>Suscripciones</h6>
              <p>Revisa tus suscripciones activas de streaming. Si guardaste tu tarjeta, verás el ícono de tarjeta guardada.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-box-seam-fill" style="color: rgb(65, 195, 255);"></i></div>
            <div>
              <h6>Membresías recurrentes</h6>
              <p>Aquí aparecen tus membresías con cobro automático. También puedes cancelarlas desde este panel si ya no las necesitas.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-cpu-fill" style="color: #f0b429;"></i></div>
            <div>
              <h6>API Gateway</h6>
              <p>Pestaña aparte para los pagos procesados directo en la página, sin redirección a PlacetoPay.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-shuffle" style="color: #6366f1;"></i></div>
            <div>
              <h6>Pagos mixtos</h6>
              <p>Muestra el total del pedido, lo pagado hasta ahora y el saldo restante, con botón para continuar el pago.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-shield-lock-fill" style="color: #a5b4fc;"></i></div>
            <div>
              <h6>Preautorizaciones</h6>
              <p>Historial de tus reservas de hotel, con el estado de la preautorización y el monto apartado.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-diagram-3-fill" style="color: #10b981;"></i></div>
            <div>
              <h6>Dispersiones</h6>
              <p>Historial de tiquetes comprados, con el desglose de cuánto fue para la aerolínea y cuánto para impuestos.</p>
            </div>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>¿Tu pago quedó pendiente?</strong> No te preocupes. En el historial encontrarás un botón para verificar el estado. Si el banco lo aprobó, se actualizará automáticamente.</span>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — verificación de pagos</summary>
          <p class="shots-summary">Cómo se ve el botón de verificar y el resultado cuando el pago ya fue aprobado.</p>
          <div class="split" style="margin-top:16px;">
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/boton-verificar.webp" type="image/webp">
                <img src="../assets/capturas/boton-verificar.webp" alt="Boton para verificar el estado de un pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/estado-aprobed.webp" type="image/webp">
                <img src="../assets/capturas/estado-aprobed.webp" alt="Estado de pago aprobado">
              </picture>
            </div>
          </div>
        </details>
      </section>

      <!-- PERFIL -->
      <section class="section" id="perfil">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-person"></i></div>
          <h4>Tu perfil</h4>
        </div>
        <p>En tu perfil puedes personalizar tu información: cambiar tu foto, actualizar tu correo o contraseña, y ver un resumen de toda tu actividad en la plataforma.</p>

        <div class="cards" style="grid-template-columns: repeat(2,1fr);">
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-image"></i></div>
            <h5>Foto de perfil</h5>
            <p>Puedes subir o cambiar tu foto en cualquier momento. Aparecerá en la barra de navegación de toda la plataforma.</p>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-calendar"></i></div>
            <h5>Calendario de actividad</h5>
            <p>Igual que en GitHub, verás un calendario con los días en que realizaste transacciones. Mientras más activo, más dorado se ve.</p>
          </article>
        </div>
      </section>

      <!-- REVERSOS -->
      <section class="section" id="reversos">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-arrow-return-left"></i></div>
          <h4>Reversos</h4>
        </div>
        <p>Un reverso es cuando cancelas un pago antes de que se liquide completamente. Es diferente a un reembolso — con el reverso el dinero nunca llega a salir de tu cuenta.</p>

        <div class="info-box warning">
          <span class="info-box-icon">⚠️</span>
          <span><strong>Importante:</strong> El reverso solo está disponible antes de la hora de corte del día en que se realizó el pago. Después de ese momento, ya no es posible revertirlo.</span>
        </div>

        <div class="steps" style="margin-top:16px;">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Ve a la sección de Reversos</h6>
              <p>Desde el historial, entra a la sección de reversos. Verás todas tus transacciones aprobadas del día.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Selecciona la transacción</h6>
              <p>Haz click en "Ver detalle" para abrir el resumen completo del pago que deseas cancelar.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Elige la opción de reverso</h6>
              <p>En el menú de opciones encontrarás "Reversar transacción". Confirma la acción y el proceso iniciará automáticamente.</p>
            </div>
          </div>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — reverso de transacción</summary>
          <p class="shots-summary">Cómo se ve la lista de transacciones reversables y la confirmación tras reversar.</p>
          <div class="split" style="margin-top:16px;">
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/trans-reversar.webp" type="image/webp">
                <img src="../assets/capturas/trans-reversar.webp" alt="Transaccion disponible para reversar">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/reversar-tran.webp" type="image/webp">
                <img src="../assets/capturas/reversar-tran.webp" alt="Confirmacion de reverso de transaccion">
              </picture>
            </div>
          </div>
        </details>
      </section>

      <!-- SESIÓN INTRO -->
      <section class="section" id="sesion-intro">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-lightning-charge"></i></div>
          <h4>¿Qué es una sesión de pago?</h4>
        </div>
        <p>Una sesión de pago es el proceso que se inicia cada vez que quieres comprar algo. Dependiendo del producto que elijas, la sesión puede ser un pago único, una suscripción o un cobro recurrente. En todos los casos, el proceso es guiado paso a paso para que nunca tengas dudas de dónde ir ni qué hacer.</p>
        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span> Utiliza <strong>Web Checkout de PlacetoPay</strong> para procesar los pagos de forma segura. Cuando hagas clic en "Pagar", serás llevado a la pasarela oficial de PlacetoPay donde completarás la transacción.</span>
        </div>

        <!-- Captura general del flujo -->
        <details class="shots-dropdown">
          <summary>📸 Ver capturas — flujo general</summary>
          <p class="shots-summary">Cómo se ve la pantalla de sesiones y la pasarela de Web Checkout de PlacetoPay.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/seciones.webp" type="image/webp">
                <img src="../assets/capturas/seciones.webp" alt="Creacion de una sesion de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/webchekout.webp" type="image/webp">
                <img src="../assets/capturas/webchekout.webp" alt="Pasarela Web Checkout de PlacetoPay">
              </picture>
            </div>
          </div>
        </details>
      </section>

      <!-- SESIÓN PAGO BÁSICO -->
      <section class="section" id="sesion-basico">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-controller"></i></div>
          <h4>Cómo crear una sesión de pago básico</h4>
        </div>
        <p>El pago básico es el más sencillo — pagas una sola vez por un producto. Lo encontrarás en la sección de <strong>Juegos</strong>, donde puedes recargar monedas o puntos para tus juegos favoritos.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Ingresa a la sección de Juegos</h6>
              <p>Desde el menú principal ve a <strong>Sesiones → Juegos</strong>. Verás las tiendas disponibles como CoD Mobile, Free Fire, PUBG y más.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Elige tu juego y selecciona el producto</h6>
              <p>Entra a la tienda del juego que quieras. Verás una lista de paquetes de monedas o puntos con su precio. Haz clic en el que prefieras para seleccionarlo.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Ingresa tu ID de jugador</h6>
              <p>En el panel de la derecha ingresa tu ID de jugador — es el identificador de tu cuenta en el juego. Sin este dato no podremos procesar la recarga.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">4</div>
            <div>
              <h6>Haz clic en "Pagar"</h6>
              <p>Serás redirigido a la pasarela segura de PlacetoPay. Ahí elige el método de pago (tarjeta, PSE, etc.) y completa la transacción.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">5</div>
            <div>
              <h6>Recibe tu confirmación</h6>
              <p>Al completar el pago, verás la pantalla de confirmación con el estado de tu transacción. También quedará guardada en tu historial.</p>
            </div>
          </div>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — proceso de pago básico</summary>
          <p class="shots-summary">Selección del producto, ingreso del ID de jugador, la pasarela de PlacetoPay y el recibo final.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/seleccion-juegos.webp" type="image/webp">
                <img src="../assets/capturas/seleccion-juegos.webp" alt="Selección de producto en la tienda de juegos">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/sesion-juegos.webp" type="image/webp">
                <img src="../assets/capturas/sesion-juegos.webp" alt="Pasarela Web Checkout de PlacetoPay">
              </picture>
  
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/comprar-recarga.webp" type="image/webp">
                <img src="../assets/capturas/comprar-recarga.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/digitar-id.webp" type="image/webp">
                <img src="../assets/capturas/digitar-id.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/redireccion-wc.webp" type="image/webp">
                <img src="../assets/capturas/redireccion-wc.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>         
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/pago-pse.webp" type="image/webp">
                <img src="../assets/capturas/pago-pse.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/pago-tarjeta.webp" type="image/webp">
                <img src="../assets/capturas/pago-tarjeta.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/recibo-pago.webp" type="image/webp">
                <img src="../assets/capturas/recibo-pago.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
          </div>
        </details>



      </section>

      <!-- SESIÓN SUSCRIPCIÓN -->
      <section class="section" id="sesion-suscripcion">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-tv"></i></div>
          <h4>Cómo crear una sesión de suscripción</h4>
        </div>
        <p>Las suscripciones te permiten activar un servicio de streaming o plataforma digital. Pagas el primer mes y, si lo deseas, puedes guardar tu tarjeta para que los cobros siguientes sean automáticos. Lo encontrarás en la sección de <strong>Plataformas → Streamings</strong>.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Ve a Plataformas → Streamings</h6>
              <p>Desde el menú principal accede a la sección de plataformas y elige la categoría de Streamings.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Selecciona la plataforma y el plan</h6>
              <p>Verás plataformas como Netflix, Paramount+, DAZN y más. Haz clic en el plan que más te convenga — básico, estándar o premium.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Haz clic en "Pagar"</h6>
              <p>Serás llevado a la pasarela de PlacetoPay. Aquí tendrás la opción de guardar tu tarjeta para futuros cobros automáticos — es completamente opcional.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">4</div>
            <div>
              <h6>Confirma el pago</h6>
              <p>Al completarse, recibirás confirmación de que tu suscripción quedó activa. Si guardaste tu tarjeta, verás el ícono 🔐 en tu historial.</p>
            </div>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>¿Guardaste tu tarjeta?</strong> Si activaste la opción, el sistema la tokenizará de forma segura — nunca se guarda el número real, solo un código que PlacetoPay usa para los cobros siguientes.</span>
            <div class="image-placeholder">
                <picture>
                  <source srcset="../assets/capturas/WEBP/opcion-tokenizar.webp" type="image/webp">
                  <img src="../assets/capturas/opcion-tokenizar.webp" alt="tokenizar tarjeta">
                </picture>
            </div>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — proceso de suscripción</summary>
          <p class="shots-summary">Selección de plataforma y plan, la pasarela de pago y la confirmación de suscripción activa.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder"> 
              <picture>
                <source srcset="../assets/capturas/WEBP/seleccion-platf.webp" type="image/webp">
                <img src="../assets/capturas/seleccion-platf.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/sesion-streamings.webp" type="image/webp">
                <img src="../assets/capturas/sesion-streamings.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/comprar-subs.webp" type="image/webp">
                <img src="../assets/capturas/comprar-subs.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/pago-tarjeta-subs.webp" type="image/webp">
                <img src="../assets/capturas/pago-tarjeta-subs.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/tokenizar-tar.webp" type="image/webp">
                <img src="../assets/capturas/tokenizar-tar.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/volver-comercio.webp" type="image/webp">
                <img src="../assets/capturas/volver-comercio.webp" alt="Pantalla de confirmación de pago">
              </picture>
            </div>
          </div>
        </details>
      </section>

      <!-- SESIÓN RECURRENCIA -->
      <section class="section" id="sesion-recurrencia">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-arrow-repeat"></i></div>
          <h4>Cómo crear una sesión de recurrencia</h4>
        </div>
        <p>Las recurrencias son cobros automáticos que se realizan cada mes o cada año sin que tengas que hacer nada. Son ideales para membresías de redes sociales o planes de IA. Las encontrarás en <strong>Plataformas → Redes Sociales</strong> o <strong>Plataformas → IA's</strong>.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Ve a Plataformas → Redes Sociales o IA's</h6>
              <p>Desde el menú de plataformas elige la sección de redes sociales (para verificados y membresías) o IA's (para planes como Claude Pro o ChatGPT Plus).</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Elige tu plan y periodicidad</h6>
              <p>Selecciona el plan que quieres activar. En algunos servicios podrás elegir entre cobro mensual o anual — el anual suele ser más económico.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Completa el pago en PlacetoPay</h6>
              <p>Serás llevado a la pasarela. Al completar el primer pago, el sistema registrará tu membresía y programará los cobros automáticos siguientes.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">4</div>
            <div>
              <h6>Revisa y cancela cuando quieras</h6>
              <p>En tu historial de <strong>Membresías recurrentes</strong> puedes ver la fecha del próximo cobro, la fecha en que termina la recurrencia y cancelarla con un solo clic si ya no la necesitas.</p>
            </div>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>¿Quieres cancelar?</strong> Ve a tu historial, busca la membresía activa y haz clic en el botón de cancelar. La membresía seguirá activa hasta el final del periodo ya pagado.</span>
          <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/cancelar-sub.webp" type="image/webp">
                <img src="../assets/capturas/cancelar-sub.webp" alt="volver al comercio">
              </picture>  
          </div>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — proceso de recurrencia</summary>
          <p class="shots-summary">La tienda de redes sociales o IA's y el historial con la opción de cancelar.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/recurrencias.webp" type="image/webp">
                <img src="../assets/capturas/recurrencias.webp" alt="tienda de recurrencias">
              </picture>
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/ia-rec.webp" type="image/webp">
                <img src="../assets/capturas/ia-rec.webp" alt="tienda de ias">
              </picture>  
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/pago-recurrente.webp" type="image/webp">
                <img src="../assets/capturas/pago-recurrente.webp" alt="pago recurrente">
              </picture>  
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/volver-comercio-rec.webp" type="image/webp">
                <img src="../assets/capturas/volver-comercio-rec.webp" alt="volver al comercio">
              </picture>  
            </div>
          </div>
        </details>
      </section>

      <!-- SEGURIDAD -->
      <section class="section" id="seguridad">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-shield-check"></i></div>
          <h4>¿Es seguro pagar?</h4>
        </div>
        <p>Sí, completamente. Los pagos se procesan a través de <strong>PlacetoPay by Evertec</strong>, una pasarela certificada y reconocida en toda Latinoamérica.</p>

        <div class="cards">
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-lock"></i></div>
            <h5>Nunca guardamos tu tarjeta</h5>
            <p>Plance no almacena los números de tu tarjeta. Solo usamos un código llamado "token" que genera PlacetoPay de forma segura.</p>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-bank"></i></div>
            <h5>Certificado PCI-DSS</h5>
            <p>PlacetoPay cumple con los más altos estándares internacionales de seguridad en el manejo de datos de pago.</p>
          </article>
          <article class="card">
            <div class="card-icon"><i class="icon-colors bi bi-check-circle"></i></div>
            <h5>Confirmación en tiempo real</h5>
            <p>Recibes la confirmación de tu pago al instante, y tu historial se actualiza automáticamente.</p>
          </article>
        </div>
      </section>

      <!-- DATOS -->
      <section class="section" id="datos">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-lock"></i></div>
          <h4>Tus datos siempre protegidos</h4>
        </div>
        <p>Tu información personal (correo, nombre, teléfono) solo se usa para procesar tus transacciones. Nunca la compartimos con terceros sin tu consentimiento.</p>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>Recuerda:</strong> Puedes actualizar o eliminar tu información en cualquier momento desde tu perfil. Si tienes alguna duda, escríbenos y con gusto te ayudamos.</span>
        </div>
      </section>
       <!-- API GATEWAY -->
        <section class="section" id="sesion-gateway">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-cpu"></i></div>
          <h4>Pagos con API Gateway</h4>
        </div>
        <p>Algunas tiendas (como PUBG, Blood Strike, Streaming y Música) usan <strong>API Gateway</strong> en lugar de la pasarela visual. Aquí ingresas los datos de tu tarjeta o cuenta directamente en la misma página, sin ser redirigido a otro sitio.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Elige tu producto</h6>
              <p>Selecciona el paquete o plan que quieres comprar. Verás una insignia "⚡ API Gateway" indicando que el pago se procesa directamente aquí.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Ingresa tus datos de pago</h6>
              <p>Completa el formulario con los datos de tu tarjeta (o cuenta bancaria) y tus datos personales. Todo queda en la misma pantalla.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Haz click en pagar ahora</h6>
              <p>Una vez diligenciado los campos puedes pagar tu producto.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">4</div>
            <div>
              <h6>Recibe tu confirmación</h6>
              <p>El resultado se muestra al instante y queda registrado en la pestaña <strong>API Gateway</strong> de tu historial.</p>
            </div>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>¿Por qué existe esta opción?</strong> Le permite a comercios que quieren tener control total sobre la experiencia de pago procesarlo desde su propia página, cumpliendo con los estándares de seguridad PCI-DSS.</span>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — proceso API Gateway</summary>
          <p class="shots-summary">Formulario de pago directo y la verificación 3D Secure.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder">
   
              <picture>
                <source srcset="../assets/capturas/WEBP/juegos-api.webp" type="image/webp">
                <img src="../assets/capturas/juegos-api.webp" alt="elegir tienda">
              </picture>  
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/tienda-api.webp" type="image/webp">
                <img src="../assets/capturas/tienda-api.webp" alt="elegir producto">
              </picture>  
            </div>
            <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/recibo-api.webp" type="image/webp">
                <img src="../assets/capturas/recibo-api.webp" alt="recibo de la compra">
              </picture>  
            </div>
              <div class="image-placeholder">
              <picture>
                <source srcset="../assets/capturas/WEBP/pago-hist.webp" type="image/webp">
                <img src="../assets/capturas/pago-hist.webp" alt="pago en el historial">
              </picture>  
          </div>
          </div>
        </details>
      </section>

      <!-- LINK DE PAGOS -->
      <section class="section" id="sesion-link">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-link-45deg"></i></div>
          <h4>Link de Pagos</h4>
        </div>
        <p>En la tienda de equipaciones deportivas puedes generar un <strong>enlace de pago</strong> en lugar de pagar directamente. Es útil cuando quieres que otra persona pague por ti, o compartir el cobro por WhatsApp o correo.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Elige tu producto</h6>
              <p>Selecciona la equipación o artículo que deseas y da clic en "Generar link de pago".</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Copia o comparte el enlace</h6>
              <p>Se genera un link único con el monto exacto. Puedes copiarlo o enviarlo directamente por WhatsApp.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>El comprador paga desde el link</h6>
              <p>Cualquier persona con el enlace puede completar el pago desde la pasarela de PlacetoPay, sin necesidad de tener cuenta en Plance.</p>
            </div>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>Ojo:</strong> Los links de pago tienen fecha de expiración. Revisa el tiempo restante antes de compartirlo.</span>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — link de pago</summary>
          <p class="shots-summary">Generación del enlace y la vista para compartirlo.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder">📸 Captura del botón "Generar link de pago"</div>
            <div class="image-placeholder">📸 Captura del link generado con opción de compartir</div>
          </div>
        </details>
      </section>

      <!-- PAGO MIXTO -->
      <section class="section" id="sesion-mixto">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-shuffle"></i></div>
          <h4>Pago mixto (parcial)</h4>
        </div>
        <p>En la tienda de Rainbow Six Siege Mobile puedes seleccionar varios productos a la vez y, si no tienes el monto completo, pagar solo una parte ahora y completar el resto más adelante.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Agrega productos al carrito</h6>
              <p>Activa "Selección múltiple" para elegir más de un producto y súmalos a tu pedido.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Ve a la pasarela de pago</h6>
              <p>Al continuar, PlacetoPay te mostrará el total y te dará la opción de pagar completo o solo una parte.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Completa el saldo cuando quieras</h6>
              <p>Si pagaste una parte, en tu historial verás el saldo restante y un botón "Continuar pago" para terminarlo cuando tengas el dinero disponible.</p>
            </div>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>¿Sabías?</strong> El saldo pendiente no vence — puedes completarlo cuando quieras desde tu historial de pagos.</span>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — pago mixto</summary>
          <p class="shots-summary">Selección múltiple de productos y el desglose del pago parcial.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder">📸 Captura de la selección múltiple de productos</div>
            <div class="image-placeholder">📸 Captura del desglose: pagado ahora vs. saldo restante</div>
          </div>
        </details>
      </section>

      <!-- PREAUTORIZACIÓN -->
      <section class="section" id="sesion-preautorizacion">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-shield-lock"></i></div>
          <h4>Reservas con preautorización</h4>
        </div>
        <p>Al reservar una habitación en el Hotel Plance, tu tarjeta <strong>no se cobra de inmediato</strong>. El monto queda "apartado" como garantía y solo se cobra realmente cuando haces check-out.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Elige tu habitación y fechas</h6>
              <p>Selecciona la habitación e indica el check-in y check-out. El total se calcula según las noches.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Confirma la reserva</h6>
              <p>PlacetoPay reserva el monto en tu tarjeta sin cobrarlo — verás la reserva marcada como "pendiente" mientras se confirma.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>El cobro se hace al check-out</h6>
              <p>El monto se debita realmente cuando el hotel procesa tu salida. Si cancelas antes, el dinero apartado se libera automáticamente.</p>
            </div>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>Recuerda:</strong> Ver tu reserva como "pendiente" es normal — significa que el monto está apartado, no que algo salió mal.</span>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — preautorización</summary>
          <p class="shots-summary">Selección de habitación y fechas, y la confirmación de la reserva.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder">📸 Captura de selección de habitación y fechas</div>
            <div class="image-placeholder">📸 Captura de la reserva confirmada</div>
          </div>
        </details>
      </section>

      <!-- DISPERSIÓN -->
      <section class="section" id="sesion-dispersion">
        <div class="section-header">
          <div class="section-icon"><i class="icon-colors bi bi-diagram-3"></i></div>
          <h4>Tiquetes con dispersión de pago</h4>
        </div>
        <p>Al comprar un tiquete aéreo, tu pago se divide automáticamente: una parte va para la aerolínea y otra para los impuestos aeroportuarios — todo en una sola transacción.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Elige tu destino</h6>
              <p>Filtra por región (Suramérica, Europa, Asia) y selecciona el destino que quieras.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Revisa el desglose</h6>
              <p>Antes de pagar verás cuánto corresponde al vuelo y cuánto a impuestos — la suma siempre es igual al total.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Completa el pago</h6>
              <p>Al aprobarse, PlacetoPay reparte el dinero automáticamente entre los destinatarios correspondientes.</p>
            </div>
          </div>
        </div>

        <details class="shots-dropdown">
          <summary>📸 Ver capturas — dispersión de pago</summary>
          <p class="shots-summary">Selección de destino y el desglose de la dispersión tras el pago.</p>
          <div class="split" style="margin-top:1.2rem;">
            <div class="image-placeholder">📸 Captura de selección de destino</div>
            <div class="image-placeholder">📸 Captura del desglose aerolínea / impuestos</div>
          </div>
        </details>
      </section>

    </div>
  </main>
</div>

<div class="img-lightbox" id="imgLightbox">
  <span class="lightbox-close" id="lightboxClose">&times;</span>
  <img id="lightboxImg" src="" alt="">
</div>

<script>
// Desplegables del sidebar (Guía / Seguridad)
document.querySelectorAll('.nav-title').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const target = document.getElementById(this.dataset.target);
        const collapsed = this.classList.toggle('collapsed');
        this.setAttribute('aria-expanded', String(!collapsed));
        if (target) target.classList.toggle('collapsed', collapsed);
    });
});

// Scroll suave para los links del sidebar
document.querySelectorAll('.nav a[href^="#"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.nav a').forEach(a => a.classList.remove('active'));
        this.classList.add('active');
    });
});

// Búsqueda simple
document.querySelector('.search').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.section').forEach(function(sec) {
        const text = sec.textContent.toLowerCase();
        sec.style.opacity = (!q || text.includes(q)) ? '1' : '0.25';
    });
});

(function () {
    const lightbox = document.getElementById('imgLightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const closeBtn = document.getElementById('lightboxClose');

    function openLightbox(src, alt) {
        lightboxImg.src = src;
        lightboxImg.alt = alt || '';
        lightbox.classList.add('active');
        document.body.classList.add('lightbox-open');
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.classList.remove('lightbox-open');
    }

    document.querySelectorAll('.image-placeholder img').forEach(function (img) {
        img.addEventListener('click', function () {
            openLightbox(img.currentSrc || img.src, img.alt);
        });
    });

    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
})();
</script>
</body>
</html>