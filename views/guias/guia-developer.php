<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Guía Developer | DevS </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>


  <style>

    :root {
      --accent:       #f07929;
      --accent2:      rgba(255, 157, 45, 0.25);
      --accent-soft:  rgba(240, 180, 41, 0.12);
      --accent-glow:  rgba(240, 180, 41, 0.25);
      --muted:        var(--pt-text-sec);
      --mono:         'JetBrains Mono', 'Courier New', monospace;

      --code-bg:      #0d1117;
      --code-border:  #262c36;
      --code-text:    #e6edf3;
      --code-muted:   #7d8590;

      --ok:   #10b981;
      --info: #3b82f6;
      --warn: #e05252;
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

    /* SIDEBAR (compartido con guia-user.php) */
    .sidebar {
      background: var(--pt-boxitem);
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
    .nav a.soon { opacity: 0.6; cursor: default; }
    .nav a.soon:hover { background: transparent; border-color: transparent; color: var(--muted); }
    .soon-tag {
      margin-left: auto; font-size: 9px; font-weight: 800;
      background: rgba(128,128,128,0.15); color: var(--muted);
      padding: 2px 6px; border-radius: 20px; letter-spacing: 0.03em;
      white-space: nowrap;
    }
    .lab-link {
      display: flex; align-items: center; gap: 9px;
      padding: 10px 12px; margin-top: 4px;
      border-radius: 10px; text-decoration: none;
      background: var(--accent-soft); border: 1px solid rgba(240,180,41,0.3);
      color: var(--accent); font-size: 13px; font-weight: 700;
      transition: all 0.15s;
    }
    .lab-link:hover { background: var(--accent-glow); transform: translateX(2px); color: var(--accent); }

    /* CONTENT */
    .content { min-width: 0; background: var(--pt-bg-secondary); }

    .topbar {
      position: sticky; top: 0; z-index: 10;
      display: flex; align-items: center; justify-content: space-between;
      gap: 10px; padding: 14px 22px;
      background: var(--pt-navbar);
      backdrop-filter: blur(12px);
    }
    .topbar h2 { margin: 0; font-size: 15px; font-weight: 600; color: var(--pt-text); }
    .search {
      width: 260px; max-width: 100%;
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
      background: var(--pt-lightbox);
      border-top: 2px solid var(--accent);
      border-radius: 16px; padding: 28px;
    }
    .hero h3 { margin: 0 0 12px; font-size: clamp(24px, 3vw, 34px); line-height: 1.15; }
    .hero h3 span { color: var(--accent); }
    .hero p { margin: 0; color: var(--pt-text); line-height: 1.75; font-size: 15px; max-width: 780px; }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--accent-soft); border: 1px solid rgba(240,180,41,0.3);
      color: var(--accent); border-radius: 20px;
      padding: 4px 12px; font-size: 12px; font-weight: 600;
      margin-bottom: 12px; letter-spacing: 0.04em;
    }
    .hero-actions { display: flex; gap: 10px; margin-top: 18px; flex-wrap: wrap; }
    .btn-ghost {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 9px 16px; border-radius: 10px;
      border: 1px solid var(--pt-border); color: var(--pt-text);
      text-decoration: none; font-size: 13.5px; font-weight: 600;
      transition: all 0.15s;
    }
    .btn-ghost:hover { border-color: var(--accent); color: var(--accent); }
    .btn-ghost.primary { background: var(--accent); border-color: var(--accent); color: #17140f; }
    .btn-ghost.primary:hover { color: #17140f; filter: brightness(1.08); }

    /* SECTIONS */
    .section { background: var(--pt-lightbox); border-radius: 14px; padding: 24px; }
    .section-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .section-icon {
      width: 36px; height: 36px; border-radius: 10px;
      background: var(--accent-soft); border: 1px solid rgba(240,180,41,0.25);
      display: flex; align-items: center; justify-content: center;
      font-size: 17px; flex-shrink: 0; color: var(--accent);
    }
    .section h4 { margin: 0; font-size: 20px; }
    .section > p { margin: 0 0 16px; color: var(--pt-text-sec); line-height: 1.7; font-size: 14.5px; }
    .section-sub { font-size: 15px; margin: 22px 0 6px; color: var(--pt-text); font-weight: 700; }

    /* CARDS */
    .cards { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 14px; margin-top: 16px; }
    .card {
      background: var(--pt-bg-card2); border-radius: 12px; padding: 18px;
      transition: border-color 0.2s, transform 0.2s;
      border: 1px solid transparent;
    }
    .card:hover { border-color: rgba(240,180,41,0.3); transform: translateY(-2px); }
    .card-icon { font-size: 1.5rem; margin-bottom: 10px; color: var(--accent); }
    .card h5 { margin: 0 0 8px; font-size: 15px; color: var(--pt-text); }
    .card p  { margin: 0; color: var(--pt-text-sec); font-size: 13.5px; line-height: 1.6; }
    .card-tag {
      display: inline-block; margin-top: 10px;
      background: var(--accent-soft); color: var(--accent);
      font-size: 11px; font-weight: 700; padding: 2px 8px;
      border-radius: 20px; letter-spacing: 0.04em;
    }
    .card-link {
      display: inline-flex; align-items: center; gap: 5px; margin-top: 10px;
      color: var(--accent); font-size: 12.5px; font-weight: 700; text-decoration: none;
    }
    .card-link:hover { gap: 8px; }

    /* STEPS (reusado para flujo de credenciales) */
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
      background: var(--accent-soft); border: 1px solid rgba(240,180,41,0.4);
      color: var(--accent); display: grid; place-items: center;
      font-weight: 800; font-size: 14px; flex-shrink: 0;
    }
    .step h6 { margin: 0 0 5px; font-size: 15px; color: var(--pt-text); }
    .step p  { margin: 0; color: var(--pt-text-sec); font-size: 13.5px; line-height: 1.6; }
    .step code { font-family: var(--mono); font-size: 12.5px; color: var(--accent); background: var(--accent-soft); padding: 1px 6px; border-radius: 5px; }

    /* INFO BOXES */
    .info-box {
      display: flex; gap: 10px; align-items: flex-start;
      padding: 12px 14px; border-radius: 10px; margin-top: 14px;
      font-size: 13.5px; line-height: 1.6;
    }
    .info-box.tip { background: rgba(240,180,41,0.07); border: 1px solid rgba(240,180,41,0.2); color: #c99010; }
    .info-box.warning { background: rgba(224,82,82,0.07); border: 1px solid rgba(224,82,82,0.2); color: #e05252; }
    .info-box.note { background: rgba(59,130,246,0.07); border: 1px solid rgba(59,130,246,0.2); color: #3b82f6; }
    .info-box-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
    .info-box strong { color: inherit; }

    /* CALLOUT GRANDE — "un JSON, muchos lenguajes" */
    .callout {
      background: linear-gradient(135deg, var(--accent-soft), transparent 60%);
      border: 1px solid rgba(240,180,41,0.3);
      border-radius: 16px; padding: 26px 28px;
      display: grid; grid-template-columns: auto 1fr; gap: 20px; align-items: center;
    }
    .callout-icon {
      width: 54px; height: 54px; border-radius: 14px;
      background: var(--accent); color: #17140f;
      display: grid; place-items: center; font-size: 24px; flex-shrink: 0;
    }
    .callout h4 { margin: 0 0 8px; font-size: 18px; }
    .callout p { margin: 0; color: var(--pt-text); font-size: 14.5px; line-height: 1.75; }
    .callout p + p { margin-top: 8px; }

    /* ENDPOINT BAR */
    .endpoint-bar {
      display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
      background: var(--code-bg); border: 1px solid var(--code-border);
      border-radius: 10px; padding: 11px 16px; margin: 16px 0 0;
      font-family: var(--mono); font-size: 12.8px;
    }
    .method-pill {
      background: rgba(16,185,129,0.15); color: var(--ok);
      font-weight: 800; font-size: 11px; padding: 4px 10px;
      border-radius: 6px; letter-spacing: 0.03em; flex-shrink: 0;
    }
    .endpoint-url { color: #cdd3dc; word-break: break-all; }
    .endpoint-note { color: var(--code-muted); font-size: 11.5px; }

    /* CODE BLOCK con pestañas */
    .code-block {
      border-radius: 12px; overflow: hidden;
      border: 1px solid var(--code-border);
      background: var(--code-bg); margin-top: 14px;
    }
    .code-tabs {
      display: flex; align-items: center;
      background: rgba(255,255,255,0.02);
      border-bottom: 1px solid var(--code-border);
      padding: 0 6px;
    }
    .code-tab {
      padding: 9px 16px; font-size: 12.5px; font-weight: 700;
      color: var(--code-muted); background: transparent; border: none;
      border-bottom: 2px solid transparent; cursor: pointer;
      font-family: inherit; transition: color 0.15s;
    }
    .code-tab:hover { color: #cdd3dc; }
    .code-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
    .code-copy {
      margin-left: auto; background: transparent; border: none;
      color: var(--code-muted); cursor: pointer; padding: 8px 12px;
      display: flex; align-items: center; gap: 6px; font-size: 11.5px;
      font-family: inherit; transition: color 0.15s;
    }
    .code-copy:hover { color: var(--accent); }
    .code-panel {
      display: none; margin: 0; padding: 16px 18px;
      overflow-x: auto; font-family: var(--mono); font-size: 12.6px;
      line-height: 1.75; color: var(--code-text); white-space: pre;
    }
    .code-panel.active { display: block; }
    /* Bootstrap le pone color rosado (--bs-code-color) al elemento <code>;
       lo pisamos para que el texto sin resaltar sea legible sobre fondo oscuro */
    .code-panel code { color: var(--code-text); font-size: inherit; word-wrap: normal; }

    /* Colores de sintaxis (manual, simple) */
    .jk  { color: #79c0ff; }               /* keys JSON */
    .js  { color: #a5d6a7; }               /* strings */
    .jn  { color: #ffab91; }               /* numbers */
    .jb  { color: #d2a8ff; }               /* bool/null/type */
    .cm  { color: var(--code-muted); font-style: italic; } /* comentarios */
    .fn  { color: #d2a8ff; }               /* funciones/keywords */
    .cvar { color: #ffa657; }              /* variables ($login, $body...) — nombre distinto de .vr para no chocar con la utilidad "vertical rule" de Bootstrap */

    /* TIPOS DE PAGO (pastillas de payload) */
    .type-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 16px; }
    .type-pill {
      padding: 7px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 700;
      border: 1px solid var(--pt-border); background: var(--pt-bg-card2);
      color: var(--pt-text-sec); cursor: pointer; transition: all 0.15s;
    }
    .type-pill:hover { border-color: rgba(240,180,41,0.4); color: var(--pt-text); }
    .type-pill.active { background: var(--accent); border-color: var(--accent); color: #17140f; }

    /* SDK CARDS */
    .sdk-cards { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; margin-top: 16px; }
    .sdk-card {
      background: var(--pt-bg-card2); border-radius: 12px; padding: 16px;
      display: flex; flex-direction: column; gap: 8px;
    }
    .sdk-card-top { display: flex; align-items: center; gap: 10px; }
    .sdk-icon {
      width: 34px; height: 34px; border-radius: 9px; display: grid; place-items: center;
      background: var(--accent-soft); color: var(--accent); font-size: 16px; flex-shrink: 0;
    }
    .sdk-card h6 { margin: 0; font-size: 14.5px; color: var(--pt-text); }
    .sdk-card code {
      font-family: var(--mono); font-size: 11.5px; color: var(--pt-text-sec);
      background: var(--pt-bg-base); padding: 4px 8px; border-radius: 6px; display: inline-block;
      word-break: break-all;
    }
    .sdk-card a {
      margin-top: auto; display: inline-flex; align-items: center; gap: 5px;
      color: var(--accent); font-size: 12.5px; font-weight: 700; text-decoration: none;
    }
    .sdk-card a:hover { gap: 8px; }

    .plugin-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 14px; }
    .plugin-chip {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--pt-bg-card2); border: 1px solid var(--pt-border);
      color: var(--pt-text-sec); font-size: 12.5px; font-weight: 600;
      padding: 6px 12px; border-radius: 20px;
    }

    @media (max-width: 1100px) {
      .layout { grid-template-columns: 1fr; }
      .sidebar { position: static; height: auto; }
      .cards { grid-template-columns: 1fr; }
      .sdk-cards { grid-template-columns: repeat(2, minmax(0,1fr)); }
      .callout { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<div class="layout">

  <?php include __DIR__ . '/sidebar-guia-dev.php'; ?>

  <!-- MAIN -->
  <main class="content">
    <header class="topbar">
      <div style="display:flex; align-items:center; gap:10px;">
        <a href="guia.php" class="btn-ghost" title="Volver a Guías">
          <i class="bi bi-arrow-left"></i> Guías
        </a>
        <a href="guia-user.php" class="btn-ghost" title="Ir a la Guía de Usuario">
          <i class="bi bi-person-badge"></i> Guía Usuario
        </a>
      </div>
      <h2>Guía Developer — Integraciones</h2>
      <input class="search" type="text" placeholder="🔍 Buscar en la guía...">
    </header>

    <div class="container">

      <!-- HERO -->
      <section class="hero" id="intro">
        <div class="hero-badge"><i class="bi bi-file-earmark-code"></i> Guía técnica</div>
        <h3>Integra <span>PlacetoPay</span> en tu comercio</h3>
        <p>
          Esta guía resume, en un solo lugar, cómo se integran los tres servicios de PlacetoPay que usamos: <strong>Web Checkout</strong>, <strong>API Gateway</strong> y <strong>Link de Pagos</strong>.
          Encontrarás los endpoints, el JSON de cada tipo de pago (básico, suscripción, preautorización, dispersión) con ejemplos en <strong>PHP, JavaScript y Python</strong>, y cómo funcionan las credenciales sin exponer ninguna real.
        </p>
        <div class="hero-actions">
          <a class="btn-ghost primary" href="#web-checkout"><i class="bi bi-rocket-takeoff"></i> Empezar por Web Checkout</a>
          <a class="btn-ghost" href="guia-dev/lab_index.php"><i class="bi bi-terminal"></i> Probar requests en el Laboratorio</a>
        </div>
      </section>

      <!-- CALLOUT: UN JSON, MUCHOS LENGUAJES -->
      <section class="callout">
        <div class="callout-icon"><i class="bi bi-braces"></i></div>
        <div>
          <h4>Un mismo JSON viaja por HTTP, sin importar el lenguaje</h4>
          <p>No todas las integraciones que hacen los devs son iguales — pero el <strong>request en sí</strong> (el cuerpo JSON con <code>auth</code>, <code>payer</code>, <code>payment</code>, <code>expiration</code>, <code>returnUrl</code>, etc.) es exactamente el mismo sin importar si tu backend está en PHP, Node.js o Python.</p>
          <p>Lo que cambia entre lenguajes es <strong>el código que construye y envía ese JSON</strong>: cómo generas el hash de las credenciales, cómo haces la petición HTTP y cómo lees la respuesta. Por eso, en cada sección verás el mismo payload con tres formas distintas de mandarlo.</p>
        </div>
      </section>

      <!-- SERVICIOS DISPONIBLES -->
      <section class="section" id="servicios">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-grid"></i></div>
          <h4>Servicios disponibles</h4>
        </div>
        <p>Aqui puedes usar tres formas distintas de conectarse a PlacetoPay según el caso de uso. Elige la que corresponda a tu integración:</p>

        <div class="cards">
          <article class="card">
            <div class="card-icon"><i class="bi bi-box-arrow-up-right"></i></div>
            <h5>Web Checkout</h5>
            <p>Pasarela hospedada por PlacetoPay. Rediriges al comprador, él paga en su dominio y vuelve a tu <code>returnUrl</code>. El más simple y el más usado.</p>
            <span class="card-tag">Redirección</span>
            <br><a class="card-link" href="#web-checkout">Ver detalle <i class="bi bi-arrow-right"></i></a>
          </article>
          <article class="card">
            <div class="card-icon"><i class="bi bi-cpu"></i></div>
            <h5>API Gateway</h5>
            <p>El formulario de pago vive en tu propia página; los datos de tarjeta viajan directo en el mismo request, sin redirigir a otro sitio.</p>
            <span class="card-tag">Sin redirección</span>
            <br><a class="card-link" href="#api-gateway">Ver detalle <i class="bi bi-arrow-right"></i></a>
          </article>
          <article class="card">
            <div class="card-icon"><i class="bi bi-link-45deg"></i></div>
            <h5>Link de Pagos</h5>
            <p>Genera un enlace de cobro (usa la misma sesión de Web Checkout) para compartir por WhatsApp o correo. Ideal cuando quien paga no es quien compra.</p>
            <span class="card-tag">Enlace compartible</span>
            <br><a class="card-link" href="#link-pagos">Ver detalle <i class="bi bi-arrow-right"></i></a>
          </article>
        </div>

        <p style="margin-top:16px;">Como regla rápida: si puedes redirigir al comprador y no necesitas tocar el dato de la tarjeta, usa <strong>Web Checkout</strong> — o <strong>Link de Pagos</strong> cuando quien paga no es quien compra. En ambos casos tu comercio nunca ve el número de tarjeta, así que tu alcance de cumplimiento PCI-DSS se mantiene mínimo. Pasa a <strong>API Gateway</strong> solo cuando de verdad necesites que el formulario de pago viva en tu propia página; a cambio, el dato de tarjeta sí pasa por tu servidor, y con eso asumes más responsabilidad de cumplimiento.</p>

        <div class="info-box warning">
          <span class="info-box-icon">⚠️</span>
          <span><strong>Restricciones reales de la API</strong> (de la referencia oficial de creación de sesión): <code>expiration</code> debe apuntar a un momento al menos <strong>5 minutos</strong> en el futuro o PlacetoPay rechaza la sesión de una vez; <code>ipAddress</code> admite máximo <strong>46 caracteres</strong> y <code>userAgent</code> máximo <strong>255</strong>; y <code>locale</code> debe seguir el formato <code>xx_YY</code> (por ejemplo <code>es_CO</code>). Son validaciones silenciosas — si no las conoces de antemano, terminas depurando un rechazo que no tiene nada que ver con tus credenciales.</span>
        </div>
      </section>

      <!-- WEB CHECKOUT -->
      <section class="section" id="web-checkout">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-box-arrow-up-right"></i></div>
          <h4>Web Checkout</h4>
        </div>
        <p>Creas una <strong>sesión de pago</strong> con un POST autenticado; PlacetoPay te responde con una URL (<code>processUrl</code>) a la que rediriges al comprador para que complete el pago en un entorno 100% controlado por ellos.</p>

        <div class="endpoint-bar">
          <span class="method-pill">POST</span>
          <span class="endpoint-url">https://checkout-test.placetopay.com/api/session</span>
          <span class="endpoint-note">(ambiente de pruebas — la URL de producción depende del país/entidad configurada)</span>
        </div>

        <div class="section-sub">Construir y enviar la sesión</div>
        <div class="code-block">
          <div class="code-tabs">
            <button class="code-tab active" data-key="php">PHP</button>
            <button class="code-tab" data-key="js">JavaScript</button>
            <button class="code-tab" data-key="py">Python</button>
            <button class="code-copy"><i class="bi bi-clipboard"></i> Copiar</button>
          </div>
          <pre class="code-panel active" data-key="php"><code>&lt;?php
<span class="cm">// credenciales fuera del código, nunca hardcodeadas</span>
<span class="cvar">$login</span>     = getenv(<span class="js">'P2P_LOGIN'</span>);
<span class="cvar">$secretKey</span> = getenv(<span class="js">'P2P_SECRET_KEY'</span>);

<span class="cvar">$seed</span>  = date(<span class="js">'c'</span>);                 <span class="cm">// fecha ISO 8601</span>
<span class="cvar">$nonce</span> = random_bytes(16);
<span class="cvar">$tranKey</span> = base64_encode(
    hash(<span class="js">'sha256'</span>, <span class="cvar">$nonce</span> . <span class="cvar">$seed</span> . <span class="cvar">$secretKey</span>, true)
);

<span class="cvar">$body</span> = [
    <span class="jk">'auth'</span> =&gt; [
        <span class="jk">'login'</span>   =&gt; <span class="cvar">$login</span>,
        <span class="jk">'tranKey'</span> =&gt; <span class="cvar">$tranKey</span>,
        <span class="jk">'nonce'</span>   =&gt; base64_encode(<span class="cvar">$nonce</span>),
        <span class="jk">'seed'</span>    =&gt; <span class="cvar">$seed</span>,
    ],
    <span class="jk">'payment'</span> =&gt; [
        <span class="jk">'reference'</span>   =&gt; <span class="js">'ORDER-'</span> . uniqid(),
        <span class="jk">'description'</span> =&gt; <span class="js">'Recarga 500 UC - PUBG Mobile'</span>,
        <span class="jk">'amount'</span>      =&gt; [<span class="jk">'currency'</span> =&gt; <span class="js">'COP'</span>, <span class="jk">'total'</span> =&gt; 25000],
    ],
    <span class="jk">'expiration'</span> =&gt; date(<span class="js">'c'</span>, strtotime(<span class="js">'+30 minutes'</span>)),
    <span class="jk">'returnUrl'</span>  =&gt; <span class="js">'https://tu-comercio.test/retorno/retorno.php'</span>,
    <span class="jk">'ipAddress'</span>  =&gt; <span class="cvar">$_SERVER</span>[<span class="js">'REMOTE_ADDR'</span>],
    <span class="jk">'userAgent'</span>  =&gt; <span class="cvar">$_SERVER</span>[<span class="js">'HTTP_USER_AGENT'</span>],
];

<span class="cvar">$ch</span> = curl_init(<span class="js">'https://checkout-test.placetopay.com/api/session'</span>);
curl_setopt_array(<span class="cvar">$ch</span>, [
    CURLOPT_POST           =&gt; true,
    CURLOPT_RETURNTRANSFER =&gt; true,
    CURLOPT_HTTPHEADER     =&gt; [<span class="js">'Content-Type: application/json'</span>],
    CURLOPT_POSTFIELDS     =&gt; json_encode(<span class="cvar">$body</span>),
]);

<span class="cvar">$session</span> = json_decode(curl_exec(<span class="cvar">$ch</span>), true);
curl_close(<span class="cvar">$ch</span>);

<span class="cm">// redirige al comprador a la pasarela</span>
header(<span class="js">'Location: '</span> . <span class="cvar">$session</span>[<span class="js">'processUrl'</span>]);</code></pre>
          <pre class="code-panel" data-key="js"><code><span class="fn">import</span> crypto <span class="fn">from</span> <span class="js">'node:crypto'</span>;

<span class="cm">// credenciales fuera del código, nunca hardcodeadas</span>
<span class="fn">const</span> login     = process.env.P2P_LOGIN;
<span class="fn">const</span> secretKey = process.env.P2P_SECRET_KEY;

<span class="fn">const</span> seed  = <span class="fn">new</span> Date().toISOString();
<span class="fn">const</span> nonce = crypto.randomBytes(16);
<span class="fn">const</span> tranKey = crypto
  .createHash(<span class="js">'sha256'</span>)
  .update(Buffer.concat([nonce, Buffer.from(seed + secretKey)]))
  .digest(<span class="js">'base64'</span>);

<span class="fn">const</span> body = {
  auth: {
    login,
    tranKey,
    nonce: nonce.toString(<span class="js">'base64'</span>),
    seed,
  },
  payment: {
    reference: <span class="js">`ORDER-${Date.now()}`</span>,
    description: <span class="js">'Recarga 500 UC - PUBG Mobile'</span>,
    amount: { currency: <span class="js">'COP'</span>, total: 25000 },
  },
  expiration: <span class="fn">new</span> Date(Date.now() + 30 * 60 * 1000).toISOString(),
  returnUrl: <span class="js">'https://tu-comercio.test/retorno/retorno.php'</span>,
  ipAddress: req.ip,
  userAgent: req.headers[<span class="js">'user-agent'</span>],
};

<span class="fn">const</span> res = <span class="fn">await</span> fetch(<span class="js">'https://checkout-test.placetopay.com/api/session'</span>, {
  method: <span class="js">'POST'</span>,
  headers: { <span class="js">'Content-Type'</span>: <span class="js">'application/json'</span> },
  body: JSON.stringify(body),
});

<span class="fn">const</span> session = <span class="fn">await</span> res.json();

<span class="cm">// redirige al comprador a la pasarela (ejemplo con Express)</span>
res.redirect(session.processUrl);</code></pre>
          <pre class="code-panel" data-key="py"><code><span class="fn">import</span> os, base64, hashlib, requests
<span class="fn">from</span> datetime <span class="fn">import</span> datetime, timedelta, timezone

<span class="cm"># credenciales fuera del código, nunca hardcodeadas</span>
login      = os.environ[<span class="js">'P2P_LOGIN'</span>]
secret_key = os.environ[<span class="js">'P2P_SECRET_KEY'</span>]

seed  = datetime.now(timezone.utc).isoformat()
nonce = os.urandom(16)
tran_key = base64.b64encode(
    hashlib.sha256(nonce + seed.encode() + secret_key.encode()).digest()
).decode()

body = {
    <span class="js">"auth"</span>: {
        <span class="js">"login"</span>: login,
        <span class="js">"tranKey"</span>: tran_key,
        <span class="js">"nonce"</span>: base64.b64encode(nonce).decode(),
        <span class="js">"seed"</span>: seed,
    },
    <span class="js">"payment"</span>: {
        <span class="js">"reference"</span>: <span class="js">f"ORDER-{int(datetime.now().timestamp())}"</span>,
        <span class="js">"description"</span>: <span class="js">"Recarga 500 UC - PUBG Mobile"</span>,
        <span class="js">"amount"</span>: {<span class="js">"currency"</span>: <span class="js">"COP"</span>, <span class="js">"total"</span>: 25000},
    },
    <span class="js">"expiration"</span>: (datetime.now(timezone.utc) + timedelta(minutes=30)).isoformat(),
    <span class="js">"returnUrl"</span>: <span class="js">"https://tu-comercio.test/retorno/retorno.php"</span>,
    <span class="js">"ipAddress"</span>: request.remote_addr,
    <span class="js">"userAgent"</span>: request.headers.get(<span class="js">"User-Agent"</span>),
}

resp = requests.post(
    <span class="js">"https://checkout-test.placetopay.com/api/session"</span>,
    json=body,
)
session = resp.json()

<span class="cm"># redirige al comprador a la pasarela (ejemplo con Flask)</span>
<span class="fn">return</span> redirect(session[<span class="js">"processUrl"</span>])</code></pre>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>Nota:</strong> los tres bloques envían exactamente el mismo <code>body</code> — sólo cambia cómo se calcula <code>tranKey</code> y cómo se hace el POST en cada lenguaje. Cuando el comprador vuelve a tu <code>returnUrl</code>, consulta el estado con <code>GET/POST</code> a <code>/api/session/{requestId}</code> antes de dar el pedido por aprobado.</span>
        </div>

        <div class="info-box note">
          <span class="info-box-icon">📦</span>
          <span><strong>Atajo:</strong> para PHP existe el SDK oficial <code>composer require placetopay/checkout</code>, que ya resuelve la firma, el envío y la consulta de estado por ti (<code>Checkout::builder()->login()->secretKey()->build()->createSession(...)</code>). Ver más en <a href="#sdks" style="color:inherit;text-decoration:underline;">Librerías &amp; SDKs</a>.</span>
        </div>
      </section>

      <!-- API GATEWAY -->
      <section class="section" id="api-gateway">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-cpu"></i></div>
          <h4>API Gateway</h4>
        </div>
        <p>A diferencia de Web Checkout, aquí <strong>no hay redirección</strong>: el formulario de tarjeta (o cuenta) vive en tu propia página y los datos de pago viajan en el mismo request de creación de la transacción. PlacetoPay responde directamente con el <strong>estado final</strong>, no con una URL para redirigir.</p>

        <div class="info-box warning">
          <span class="info-box-icon">⚠️</span>
          <span><strong>Importante:</strong> si el dato de la tarjeta toca tu servidor o tu página, tu comercio entra en un alcance de cumplimiento PCI-DSS mucho mayor que con Web Checkout. Aquí esta vía es solo para fines de <strong>demo/sandbox</strong>; el endpoint y los nombres exactos de campos pueden variar según el país y la entidad configurada — verifica siempre la referencia oficial de Gateway API en <code>docs.placetopay.dev</code> antes de integrar en producción.</span>
        </div>

        <div class="section-sub">Forma conceptual del request</div>
        <div class="code-block">
          <div class="code-tabs">
            <button class="code-tab active" data-key="gw">JSON</button>
            <button class="code-copy"><i class="bi bi-clipboard"></i> Copiar</button>
          </div>
          <pre class="code-panel active" data-key="gw"><code>{
  <span class="jk">"auth"</span>: { <span class="cm">/* igual que en Web Checkout: login, tranKey, nonce, seed */</span> },
  <span class="jk">"payer"</span>: {
    <span class="jk">"document"</span>: <span class="js">"1234567890"</span>,
    <span class="jk">"documentType"</span>: <span class="js">"CC"</span>,
    <span class="jk">"name"</span>: <span class="js">"John"</span>,
    <span class="jk">"email"</span>: <span class="js">"john@example.com"</span>
  },
  <span class="jk">"instrument"</span>: {
    <span class="jk">"card"</span>: {
      <span class="jk">"number"</span>: <span class="js">"tok_************1111"</span>,
      <span class="jk">"expiration"</span>: <span class="js">"12/28"</span>,
      <span class="jk">"cvv"</span>: <span class="js">"***"</span>
    }
  },
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"ORDER-00231"</span>,
    <span class="jk">"description"</span>: <span class="js">"Recarga Blood Strike - 6000 Gemas"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: 89900 }
  },
  <span class="jk">"ipAddress"</span>: <span class="js">"203.0.113.7"</span>,
  <span class="jk">"userAgent"</span>: <span class="js">"Mozilla/5.0 ..."</span>
}</code></pre>
        </div>

        <p style="margin-top:14px;">La construcción en PHP/JS/Python sigue el <strong>mismo patrón</strong> que Web Checkout (auth con hash SHA-256, POST con <code>Content-Type: application/json</code>) — lo único que cambia es que ahora <code>instrument</code> viaja en el request y la respuesta trae el estado (<code>APPROVED</code>, <code>REJECTED</code>, <code>PENDING</code>) de una vez, sin <code>processUrl</code>.</p>
      </section>

      <!-- LINK DE PAGOS -->
      <section class="section" id="link-pagos">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-link-45deg"></i></div>
          <h4>Link de Pagos</h4>
        </div>
        <p>No es un servicio ni un endpoint distinto: <strong>es la misma sesión de Web Checkout</strong>. La diferencia está en qué haces con la respuesta — en vez de redirigir automáticamente al comprador, <strong>compartes el <code>processUrl</code></strong> por WhatsApp, correo o cualquier canal para que otra persona pague desde ese enlace.</p>

        <div class="code-block">
          <div class="code-tabs">
            <button class="code-tab active" data-key="link">Respuesta de /api/session</button>
            <button class="code-copy"><i class="bi bi-clipboard"></i> Copiar</button>
          </div>
          <pre class="code-panel active" data-key="link"><code>{
  <span class="jk">"status"</span>: { <span class="jk">"status"</span>: <span class="js">"OK"</span>, <span class="jk">"reason"</span>: <span class="js">"PS"</span>, <span class="jk">"message"</span>: <span class="js">"La petición se procesó correctamente"</span> },
  <span class="jk">"requestId"</span>: 8452193,
  <span class="jk">"processUrl"</span>: <span class="js">"https://checkout-test.placetopay.com/spa/session/8452193/abc123..."</span>
}</code></pre>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span>Usa <code>expiration</code> para controlar cuánto tiempo es válido el enlace, y opcionalmente <code>skipResult: true</code> si no quieres que el comprador vea la pantalla de resultado de PlacetoPay al terminar. El estado real siempre lo confirmas después consultando <code>/api/session/{requestId}</code>, nunca confiando solo en que el link se haya abierto.</span>
        </div>
      </section>

      <!-- NOTIFICACIONES: URL DE NOTIFICACIÓN -->
      <section class="section" id="notificacion-url">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-broadcast"></i></div>
          <h4>La URL de notificación</h4>
        </div>
        <p>Piénsala como un buzón propio: es una dirección dentro de tu servidor donde PlacetoPay "toca la puerta" apenas un pago termina, para avisarte el resultado sin que dependas de que el comprador se quede esperando frente a la pantalla hasta el final.</p>

        <div class="section-sub">¿Para qué sirve?</div>
        <p>Cuando alguien paga, no siempre vuelve "limpiamente" a tu página: puede cerrar la pestaña, perder la conexión o simplemente no regresar. Sin una URL de notificación, tu sistema se quedaría sin saber qué pasó con ese pedido. Con ella, tu backend recibe automáticamente el resultado final del pago y puede actualizar el pedido, activar el producto o servicio, o enviar la confirmación, todo sin depender de que el usuario haga algo más.</p>

        <div class="section-sub">¿Cómo se configura? (guía rápida)</div>
        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6>Ten un endpoint HTTPS listo en tu servidor</h6>
              <p>Es una URL de tu backend (por ejemplo <code>https://tu-comercio.com/notificaciones/placetopay</code>) que recibe un <code>POST</code> con el resultado del pago y responde rápido con un código de éxito (2xx).</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6>Verifica que sea pública y accesible desde internet</h6>
              <p>No puede ser <code>localhost</code>, una IP privada de tu red interna, ni algo que solo funcione dentro de tu oficina: PlacetoPay necesita poder llegar a ella desde afuera, igual que cualquier visitante de tu sitio.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6>Valida la firma de cada notificación</h6>
              <p>Antes de confiar en el contenido, confirma que la notificación realmente viene de PlacetoPay comparando la firma recibida contra una que calculas tú mismo con tu <code>secretKey</code>. Así evitas procesar "notificaciones" falsas enviadas por un tercero.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">4</div>
            <div>
              <h6>Entrégasela al equipo de PlacetoPay</h6>
              <p>Esta URL no se escribe en el checkout: se la compartes a PlacetoPay para que quede configurada en el panel administrativo de tu comercio. Desde ahí, todas tus sesiones de pago la usarán automáticamente.</p>
            </div>
          </div>
        </div>

        <div class="info-box warning">
          <span class="info-box-icon">⚠️</span>
          <span><strong>Debe quedar expuesta en un puerto público:</strong> al ser una URL HTTPS, tiene que responder por el puerto estándar <strong>443</strong> (el que usa todo el tráfico seguro de internet), no por un puerto interno, personalizado o bloqueado por el firewall. Si tu servidor no es alcanzable desde afuera por ese puerto, la notificación simplemente nunca llega.</span>
        </div>

        <div class="info-box warning">
          <span class="info-box-icon">⚠️</span>
          <span><strong>PlacetoPay no reintenta el envío:</strong> si justo en el momento en que se dispara la notificación tu servidor está caído, lento o responde con error, esa notificación se pierde para siempre, sin un segundo intento automático. Por eso el endpoint debe estar siempre disponible — de eso se encarga la <a href="#sonda" style="color:inherit;text-decoration:underline;">sonda</a>, que verás a continuación.</span>
        </div>

        <div class="info-box note">
          <span class="info-box-icon">🗂️</span>
          <span><strong>Se te pedirá esta URL para configurarla del lado de PlacetoPay:</strong> normalmente el equipo comercial o de soporte la solicita durante el proceso de afiliación o integración, y queda registrada en el panel administrativo asociado a tu comercio. No es algo que el comprador vea ni que debas exponer públicamente en tu sitio.</span>
        </div>

        <div class="section-sub">Para producción, no te quedes con un túnel de pruebas</div>
        <p>En desarrollo es normal usar herramientas como <strong>ngrok</strong>, <strong>Cloudflare Tunnel</strong> o <strong>localtunnel</strong> para exponer temporalmente tu máquina local. Pero en producción necesitas algo permanente y estable:</p>
        <div class="cards">
          <article class="card">
            <div class="card-icon"><i class="bi bi-hdd-network"></i></div>
            <h5>Servidor o hosting real</h5>
            <p>Un servidor propio, VPS o hosting con IP y dominio fijos, no una máquina local ni un túnel que se cae al cerrar la terminal.</p>
          </article>
          <article class="card">
            <div class="card-icon"><i class="bi bi-patch-check"></i></div>
            <h5>Certificado TLS válido</h5>
            <p>Un certificado real y vigente (por ejemplo de Let's Encrypt o el de tu proveedor de hosting). Los certificados autofirmados no son confiables para este tráfico.</p>
          </article>
          <article class="card">
            <div class="card-icon"><i class="bi bi-shield-check"></i></div>
            <h5>Firewall abierto en el puerto correcto</h5>
            <p>Confirma con quien administre tu infraestructura que el puerto 443 esté abierto hacia internet para ese endpoint específico.</p>
          </article>
        </div>
      </section>

      <!-- NOTIFICACIONES: SONDA -->
      <section class="section" id="sonda">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-activity"></i></div>
          <h4>¿Qué es la sonda?</h4>
        </div>
        <p>La sonda es, en pocas palabras, un chequeo de salud periódico: una verificación automática que confirma que tu URL de notificación sigue viva, responde a tiempo y no está devolviendo errores, antes de que un pago real dependa de ella.</p>

        <div class="section-sub">¿Cómo funciona?</div>
        <p>Cada cierto intervalo de tiempo se hacen peticiones de prueba contra tu endpoint para medir si sigue respondiendo correctamente. Si detecta fallas repetidas (tu servidor caído, el certificado vencido, el firewall bloqueando el tráfico, etc.), esa información queda disponible para actuar a tiempo, idealmente antes de que un pago real dependa de una notificación que nunca iba a llegar.</p>

        <div class="info-box note">
          <span class="info-box-icon">⏱️</span>
          <span><strong>Se harán consultas periódicas:</strong> estas verificaciones no ocurren una sola vez, se repiten con cierta frecuencia mientras tu integración esté activa. La idea es detectar caídas o cambios (un certificado que venció, un despliegue que rompió el endpoint) apenas ocurran, y no meses después cuando ya se perdieron notificaciones reales.</span>
        </div>

        <div class="section-sub">¿Trabaja en conjunto con la URL de notificación?</div>
        <p>Sí, y vale la pena entender por qué: como ya vimos, PlacetoPay <strong>no reintenta</strong> el envío de una notificación si falla. Eso significa que la sonda no reemplaza a la notificación, la protege. Su función es asegurarse de que, cuando llegue el momento real de avisarte un pago, tu endpoint esté efectivamente en condiciones de recibirlo.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index"><i class="bi bi-1-circle-fill" style="color:var(--accent);"></i></div>
            <div>
              <h6>La sonda vigila que el endpoint esté vivo</h6>
              <p>Verificación constante de disponibilidad, como una alarma temprana ante caídas.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-2-circle-fill" style="color:var(--accent);"></i></div>
            <div>
              <h6>La notificación avisa el resultado real del pago</h6>
              <p>Es el mensaje que de verdad le importa a tu negocio, pero solo se envía una vez.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-3-circle-fill" style="color:var(--accent);"></i></div>
            <div>
              <h6>Recomendado: una consulta propia de respaldo</h6>
              <p>Además de la sonda, conviene tener un proceso propio que cada cierto tiempo revise los pedidos que quedaron "pendientes" en tu base de datos y consulte su estado real contra PlacetoPay (Query Session). Así, aunque una notificación puntual se pierda, ningún pago queda huérfano.</p>
            </div>
          </div>
        </div>

        <div class="section-sub">Validar que la notificación es real (opcional, para devs)</div>
        <p>Si quieres blindar tu endpoint, valida la firma antes de procesar cualquier notificación:</p>
        <div class="code-block">
          <div class="code-tabs">
            <button class="code-tab active" data-key="sig">PHP</button>
            <button class="code-copy"><i class="bi bi-clipboard"></i> Copiar</button>
          </div>
          <pre class="code-panel active" data-key="sig"><code>&lt;?php
<span class="cm">// firma esperada = sha256(requestId + status.status + status.date + secretKey)</span>
<span class="cvar">$firma</span> = hash(<span class="js">'sha256'</span>,
    <span class="cvar">$body</span>[<span class="js">'requestId'</span>] . <span class="cvar">$body</span>[<span class="js">'status'</span>][<span class="js">'status'</span>] . <span class="cvar">$body</span>[<span class="js">'status'</span>][<span class="js">'date'</span>] . <span class="cvar">$secretKey</span>
);

<span class="fn">if</span> (<span class="js">'sha256:'</span> . <span class="cvar">$firma</span> !== <span class="cvar">$body</span>[<span class="js">'signature'</span>]) {
    http_response_code(400);
    <span class="fn">exit</span>;
}</code></pre>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span>En resumen: la <strong>URL de notificación</strong> es el mensajero, y la <strong>sonda</strong> es quien revisa que el camino hacia ese mensajero esté despejado. Necesitas las dos funcionando bien para no perder ningún pago en el camino.</span>
        </div>
      </section>

      <!-- TIPOS DE PAGO -->
      <section class="section" id="tipos-pago">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-credit-card"></i></div>
          <h4>Tipos de pago — mismo request, distinto payload</h4>
        </div>
        <p>El endpoint y la estructura general del <code>body</code> no cambian entre tipos de pago. Lo único que varía es el contenido de <code>payment</code>, o si se agregan los bloques <code>subscription</code>, <code>type</code> o <code>dispersion</code>. Aquí tienes los cuatro casos que usamos:</p>

        <div class="code-block">
          <div class="code-tabs">
            <button class="code-tab active" data-key="basico">Pago básico</button>
            <button class="code-tab" data-key="subs">Suscripción</button>
            <button class="code-tab" data-key="preauth">Preautorización</button>
            <button class="code-tab" data-key="disp">Dispersión</button>
            <button class="code-copy"><i class="bi bi-clipboard"></i> Copiar</button>
          </div>
          <pre class="code-panel active" data-key="basico"><code>{
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"ORDER-00231"</span>,
    <span class="jk">"description"</span>: <span class="js">"Recarga 500 UC - PUBG Mobile"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: 25000 }
  }
}</code></pre>
          <pre class="code-panel" data-key="subs"><code>{
  <span class="cm">// "subscription" registra el medio de pago para cobros futuros (collect)</span>
  <span class="jk">"subscription"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"SUB-NETFLIX-00231"</span>,
    <span class="jk">"description"</span>: <span class="js">"Netflix Estándar - tokenización de tarjeta"</span>
  },
  <span class="cm">// "payment" cobra el primer periodo, igual que un pago básico</span>
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"ORDER-00231"</span>,
    <span class="jk">"description"</span>: <span class="js">"Netflix Estándar - primer mes"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: 32900 }
  }
}</code></pre>
          <pre class="code-panel" data-key="preauth"><code>{
  <span class="cm">// "type": "checkin" aparta el monto sin cobrarlo todavía</span>
  <span class="jk">"type"</span>: <span class="js">"checkin"</span>,
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"HOTEL-00231"</span>,
    <span class="jk">"description"</span>: <span class="js">"Preautorización - Hotel Demo (3 noches)"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: 540000 }
  }
}</code></pre>
          <pre class="code-panel" data-key="disp"><code>{
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"TIQ-00231"</span>,
    <span class="jk">"description"</span>: <span class="js">"Tiquete Bogotá - Madrid"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: 3200000 },
    <span class="cm">// "dispersion" reparte el total entre varios beneficiarios en una sola transacción</span>
    <span class="jk">"dispersion"</span>: [
      { <span class="jk">"agreement"</span>: <span class="js">"AEROLINEA_001"</span>, <span class="jk">"agreementType"</span>: <span class="js">"MERCHANT_ID"</span>, <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: 2800000 } },
      { <span class="jk">"agreement"</span>: <span class="js">"IMPUESTOS_001"</span>, <span class="jk">"agreementType"</span>: <span class="js">"MERCHANT_ID"</span>, <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: 400000 } }
    ]
  }
}</code></pre>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span>El código PHP/JS/Python para armar y enviar cualquiera de estos cuatro es <strong>el mismo</strong> que viste en <a href="#web-checkout" style="color:inherit;text-decoration:underline;">Web Checkout</a> — solo reemplazas el contenido del <code>$body['payment']</code> (o le agregas <code>subscription</code>, <code>type</code> o <code>dispersion</code>) antes de enviarlo.</span>
        </div>
      </section>

      <!-- 3D SECURE -->
      <section class="section" id="3ds">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-shield-lock-fill"></i></div>
          <h4>3D Secure — autenticar al titular de la tarjeta</h4>
        </div>
        <p>3D Secure (3DS) es un paso extra de verificación que confirma que quien está pagando con la tarjeta es realmente su dueño, antes de que el banco apruebe el cobro. Es lo mismo que ya conoces cuando el banco manda un código por SMS o una notificación push para "confirmar que fuiste tú" — solo que aquí ese paso está estandarizado a nivel mundial (protocolo EMV 3-D Secure) para que funcione igual sin importar el banco o la tarjeta.</p>

        <div class="section-sub">¿Cómo funciona?</div>
        <div class="cards">
          <article class="card">
            <div class="card-icon"><i class="bi bi-lightning-charge"></i></div>
            <h5>Flujo sin fricción</h5>
            <p>El banco emisor analiza la transacción en segundo plano (dispositivo, comportamiento, historial de compras) y si todo cuadra, autentica al comprador sin pedirle nada extra. Para él es transparente: ni se entera que pasó por ahí.</p>
          </article>
          <article class="card">
            <div class="card-icon"><i class="bi bi-phone-vibrate"></i></div>
            <h5>Flujo con fricción (challenge)</h5>
            <p>Si algo genera dudas, se le pide al comprador un paso adicional — típicamente un código OTP por SMS o una confirmación desde la app del banco — antes de continuar con el pago.</p>
          </article>
        </div>

        <div class="section-sub">¿Por qué es importante?</div>
        <div class="info-box warning">
          <span class="info-box-icon">🛡️</span>
          <span><strong>Reduce contracargos:</strong> al autenticar al titular antes de cobrar, buena parte de la responsabilidad de un fraude pasa del comercio al banco emisor. En la práctica: si el comprador resulta ser un fraude después de haber pasado por una autenticación 3DS exitosa, normalmente ya no es tu comercio quien carga con ese contracargo.</span>
        </div>
        <div class="info-box note">
          <span class="info-box-icon">📜</span>
          <span><strong>Cada vez es menos "opcional":</strong> varias redes de tarjetas y regulaciones locales ya exigen 3DS para ciertos montos, países o tipos de comercio. No es solo una capa extra de seguridad — en varios mercados es un requisito para seguir cobrando con tarjeta sin fricción regulatoria.</span>
        </div>

        <div class="section-sub">Preguntas frecuentes</div>
        <div class="steps">
          <div class="step">
            <div class="step-index"><i class="bi bi-question-lg" style="color:var(--accent);"></i></div>
            <div>
              <h6>¿Web Checkout ya incluye 3DS?</h6>
              <p>No tienes que llamar ningún endpoint tú mismo: como el formulario de tarjeta vive por completo en la pasarela hospedada de PlacetoPay, la autenticación (cuando aplica) queda del lado de ellos como parte de ese flujo. Los endpoints de esta sección (<code>mpi/lookup</code> y <code>mpi/query</code>) están documentados específicamente para <strong>API Gateway</strong>, donde eres tú quien captura el dato de tarjeta y por lo tanto quien debe orquestar el 3DS manualmente.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-question-lg" style="color:var(--accent);"></i></div>
            <div>
              <h6>¿Consultar el MPI ya autentica el pago solo?</h6>
              <p><strong>No.</strong> Así lo dice explícitamente la documentación oficial: consumir <code>mpi/query</code> no autentica la transacción por sí solo. El resultado (<code>eci</code>, <code>cavv</code>, <code>xid</code>) lo tienes que reenviar tú, a mano, dentro de <code>instrument.threeDS</code> en el request de pago real — si lo omites, ese pago se procesa como si 3DS nunca hubiera pasado.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-question-lg" style="color:var(--accent);"></i></div>
            <div>
              <h6>¿Qué son eci, cavv y xid?</h6>
              <p>Son el "comprobante" de que la autenticación realmente ocurrió: <code>eci</code> indica el nivel de seguridad con el que quedó la transacción, y <code>cavv</code>/<code>xid</code> son valores criptográficos que el emisor puede usar después para verificar que ese pago sí pasó por 3DS. No necesitas entenderlos a fondo, solo saber que hay que guardarlos y reenviarlos tal cual los recibes.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-question-lg" style="color:var(--accent);"></i></div>
            <div>
              <h6>¿Qué pasa si el comprador no pasa el challenge?</h6>
              <p>Depende de las reglas de riesgo configuradas para tu comercio: algunas integraciones detienen el cobro por completo, otras permiten continuar sin el respaldo de 3DS (asumiendo tú el riesgo del contracargo). Eso se define junto con PlacetoPay para tu comercio, no es algo que controles solo desde el código.</p>
            </div>
          </div>
        </div>

        <div class="section-sub">Cómo se ve en un request (API Gateway)</div>
        <p>La autenticación va <strong>antes</strong> del cobro: primero preguntas si la tarjeta necesita autenticarse (<code>lookup</code>), esperas a que el comprador resuelva el challenge si le toca, confirmas el resultado (<code>query</code>), y solo después mandas el pago real con ese resultado adjunto.</p>

        <div class="code-block">
          <div class="code-tabs">
            <button class="code-tab active" data-key="lookup">1. MPI Lookup</button>
            <button class="code-tab" data-key="query">2. MPI Query (respuesta)</button>
            <button class="code-tab" data-key="pago">3. Pago con 3DS incluido</button>
            <button class="code-copy"><i class="bi bi-clipboard"></i> Copiar</button>
          </div>
          <pre class="code-panel active" data-key="lookup"><code><span class="cm">// POST /gateway/mpi/lookup</span>
{
  <span class="jk">"auth"</span>: { <span class="cm">/* igual que en los demás servicios */</span> },
  <span class="jk">"instrument"</span>: {
    <span class="jk">"card"</span>: { <span class="jk">"number"</span>: <span class="js">"tok_************1111"</span>, <span class="jk">"expiration"</span>: <span class="js">"12/28"</span> }
  },
  <span class="jk">"payment"</span>: {
    <span class="jk">"reference"</span>: <span class="js">"ORDER-00231"</span>,
    <span class="jk">"amount"</span>: { <span class="jk">"currency"</span>: <span class="js">"COP"</span>, <span class="jk">"total"</span>: 89900 }
  },
  <span class="jk">"returnUrl"</span>: <span class="js">"https://tu-comercio.test/3ds/retorno"</span>
}</code></pre>
          <pre class="code-panel" data-key="query"><code><span class="cm">// Respuesta de POST /gateway/mpi/query, una vez el comprador pasó (o no) el challenge</span>
{
  <span class="jk">"data"</span>: {
    <span class="jk">"enrolled"</span>: <span class="js">"Y"</span>,
    <span class="jk">"authenticated"</span>: <span class="js">"Y"</span>,
    <span class="jk">"eci"</span>: <span class="js">"05"</span>,
    <span class="jk">"cavv"</span>: <span class="js">"AAABCZIhcQAAAABZlyFxAAAAAAA="</span>,
    <span class="jk">"xid"</span>: <span class="js">"MDAwMDAwMDAwMDAwMDAwMzIyNzY="</span>
  }
}</code></pre>
          <pre class="code-panel" data-key="pago"><code><span class="cm">// El eci/cavv/xid de arriba viajan dentro del pago real — si los omites, se procesa sin 3DS</span>
{
  <span class="jk">"payment"</span>: { <span class="cm">/* mismo payment de siempre */</span> },
  <span class="jk">"instrument"</span>: {
    <span class="jk">"card"</span>: { <span class="jk">"number"</span>: <span class="js">"tok_************1111"</span>, <span class="jk">"expiration"</span>: <span class="js">"12/28"</span> },
    <span class="jk">"threeDS"</span>: {
      <span class="jk">"eci"</span>: <span class="js">"05"</span>,
      <span class="jk">"cavv"</span>: <span class="js">"AAABCZIhcQAAAABZlyFxAAAAAAA="</span>,
      <span class="jk">"xid"</span>: <span class="js">"MDAwMDAwMDAwMDAwMDAwMzIyNzY="</span>
    }
  }
}</code></pre>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span>En resumen, el orden importa: <strong>1)</strong> <code>lookup</code> para saber si la tarjeta necesita autenticarse, <strong>2)</strong> dejas que el comprador resuelva el challenge si le toca, <strong>3)</strong> <code>query</code> para confirmar el resultado, y <strong>4)</strong> recién ahí mandas el pago real con <code>instrument.threeDS</code> adjunto. Saltarte un paso no rompe nada técnicamente, pero sí te deja sin la protección de contracargos que es, al final, la razón de ser de todo esto.</span>
        </div>
      </section>

      <!-- CREDENCIALES -->
      <section class="section" id="credenciales">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-key"></i></div>
          <h4>Credenciales — cómo funcionan (sin mostrarlas)</h4>
        </div>
        <p>Toda petición a PlacetoPay se autentica con un objeto <code>auth</code> de cuatro campos. Aquí no verás ninguna credencial real — solo el <strong>efecto</strong> que produce cada una y cómo se combinan.</p>

        <div class="steps">
          <div class="step">
            <div class="step-index">1</div>
            <div>
              <h6><code>login</code> — identifica el sitio</h6>
              <p>Es público: viaja en texto plano en cada request. Identifica qué comercio/sitio está haciendo la petición, pero por sí solo no autoriza nada.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">2</div>
            <div>
              <h6><code>seed</code> — la marca de tiempo</h6>
              <p>Fecha y hora actuales en formato ISO 8601. PlacetoPay la usa para rechazar peticiones viejas o repetidas fuera de la ventana de tiempo permitida.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">3</div>
            <div>
              <h6><code>nonce</code> — un valor de un solo uso</h6>
              <p>Bytes aleatorios generados en cada request, codificados en Base64. Garantizan que dos peticiones idénticas nunca produzcan el mismo <code>tranKey</code>.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index">4</div>
            <div>
              <h6><code>tranKey</code> — la firma calculada</h6>
              <p>Se calcula así: <code>Base64( SHA256( nonce + seed + secretKey ) )</code>. La <code>secretKey</code> nunca viaja en la petición — solo su huella digital, distinta en cada request gracias al <code>nonce</code> y el <code>seed</code>.</p>
            </div>
          </div>
        </div>

        <div class="info-box warning">
          <span class="info-box-icon">⚠️</span>
          <span><strong>La <code>secretKey</code> nunca sale del backend:</strong> no se hardcodea en el repo, no se manda al navegador y no se comparte en capturas ni en tickets de soporte. Se guarda en variables de entorno (<code>.env</code>, secretos del servidor) y se lee solo del lado del servidor al calcular <code>tranKey</code>.</span>
        </div>

        <div class="info-box note">
          <span class="info-box-icon">🏦</span>
          <span><strong>¿Y las credenciales de AvalPay, Getnet u otras entidades?</strong> El algoritmo de firma (<code>login</code> + <code>tranKey</code>) es siempre el mismo, sin importar qué entidad procesadora hay detrás de una tienda. Lo que cambia es <strong>a qué par de credenciales apunta cada integración</strong>: cada entidad/ambiente tiene su propio <code>login</code> y <code>secretKey</code> configurados por separado, así que mezclar credenciales de una entidad con el endpoint de otra simplemente hace que la firma no valide — nunca se "cruzan" datos entre comercios.</span>
        </div>
      </section>

      <!-- SDKs -->
      <section class="section" id="sdks">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-box-seam"></i></div>
          <h4>Librerías &amp; SDKs oficiales</h4>
        </div>
        <p>Si no quieres calcular el hash de autenticación a mano, PlacetoPay mantiene SDKs oficiales que ya resuelven la firma, el envío y la consulta de estado:</p>

        <div class="sdk-cards">
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="fa-brands fa-php"></i></div>
              <h6>PHP</h6>
            </div>
            <code>composer require placetopay/checkout</code>
            <a href="https://github.com/placetopay-org/sdk-checkout-php" target="_blank" rel="noopener">Ver repositorio <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="fa-brands fa-python"></i></div>
              <h6>Python</h6>
            </div>
            <code>pip install placetopay-checkout</code>
            <a href="https://github.com/placetopay-org/sdk-checkout-python" target="_blank" rel="noopener">Ver repositorio <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="bi bi-hash"></i></div>
              <h6>C# / .NET</h6>
            </div>
            <code>dotnet add package PlaceToPay.Checkout</code>
            <a href="https://github.com/placetopay-org/sdk-checkout-dotnet" target="_blank" rel="noopener">Ver repositorio <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="fa-brands fa-java"></i></div>
              <h6>Java</h6>
            </div>
            <code>com.placetopay:sdk-checkout</code>
            <a href="https://github.com/placetopay-org/sdk-checkout-java" target="_blank" rel="noopener">Ver repositorio <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">🧩</span>
          <span>Para e-commerce ya armado (sin escribir tu propio backend de integración), PlacetoPay también ofrece plugins listos para <strong>WooCommerce, Magento, PrestaShop, Jumpseller, VTEX y Shopify</strong> — los ves en detalle en la sección <a href="#plugins" style="color:inherit;text-decoration:underline;">Plugins</a>, justo abajo.</span>
        </div>
      </section>

      <!-- PLUGINS -->
      <section class="section" id="plugins">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-puzzle"></i></div>
          <h4>Plugins para tiendas en línea</h4>
        </div>
        <p>Si el comercio ya tiene su tienda armada en una de estas plataformas, no hace falta escribir la integración desde cero: PlacetoPay mantiene un plugin oficial para cada una. La documentación técnica de <code>docs.placetopay.dev</code> casi no entra en detalle sobre ellos — la guía paso a paso real vive aparte, en las páginas propias de cada plugin dentro de <code>placetopay.dev/plugins</code>. Vale la pena revisarlas antes de instalar cualquiera.</p>

        <div class="sdk-cards" style="grid-template-columns: repeat(3, minmax(0,1fr));">
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="bi bi-cart3"></i></div>
              <h6>WooCommerce</h6>
            </div>
            <p style="font-size:12.5px;color:var(--pt-text-sec);margin:0;flex:1;">Plugin para WordPress/WooCommerce. Se sube como cualquier plugin de WP (el .zip) y se configura con tu login y secretKey.</p>
            <a href="https://placetopay.dev/plugins/woocommerce" target="_blank" rel="noopener">Ver guía oficial <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="bi bi-shop"></i></div>
              <h6>Jumpseller</h6>
            </div>
            <p style="font-size:12.5px;color:var(--pt-text-sec);margin:0;flex:1;">No se instala nada: se agrega como método de pago desde Configuración → Proceso de compra → Pagos.</p>
            <a href="https://placetopay.dev/plugins/jumpseller" target="_blank" rel="noopener">Ver guía oficial <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="bi bi-bag-check"></i></div>
              <h6>Magento</h6>
            </div>
            <p style="font-size:12.5px;color:var(--pt-text-sec);margin:0;flex:1;">Módulo para Magento 2.3.x–2.4.6, con varios proveedores disponibles (PlacetoPay, AvalPay, Atlabank...) desde una sola configuración.</p>
            <a href="https://placetopay.dev/plugins/magento" target="_blank" rel="noopener">Ver guía oficial <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="bi bi-bag"></i></div>
              <h6>PrestaShop</h6>
            </div>
            <p style="font-size:12.5px;color:var(--pt-text-sec);margin:0;flex:1;">Módulo que se sube como .zip desde el catálogo de módulos, con manejo automático de los estados del pedido.</p>
            <a href="https://placetopay.dev/plugins/prestashop" target="_blank" rel="noopener">Ver guía oficial <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="bi bi-diagram-3"></i></div>
              <h6>VTEX</h6>
            </div>
            <p style="font-size:12.5px;color:var(--pt-text-sec);margin:0;flex:1;">Se activa como conector ("PlaceToPayLatam") desde Afiliaciones de Gateway, y luego se asocia a una condición de pago.</p>
            <a href="https://placetopay.dev/plugins/vtex" target="_blank" rel="noopener">Ver guía oficial <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
          <article class="sdk-card">
            <div class="sdk-card-top">
              <div class="sdk-icon"><i class="bi bi-bag-heart"></i></div>
              <h6>Shopify</h6>
            </div>
            <p style="font-size:12.5px;color:var(--pt-text-sec);margin:0;flex:1;">Se instala desde la Shopify App Store; la URL de notificación queda configurada sola al activarlo.</p>
            <a href="https://placetopay.dev/plugins/shopify" target="_blank" rel="noopener">Ver guía oficial <i class="bi bi-box-arrow-up-right"></i></a>
          </article>
        </div>

        <div class="section-sub">Errores comunes al configurar un plugin</div>
        <p>La documentación oficial casi no cubre esta parte, así que aquí va un resumen de los tropiezos más típicos — vale la pena advertírselos a un comercio antes de que le pasen:</p>

        <div class="steps">
          <div class="step">
            <div class="step-index"><i class="bi bi-download" style="color:var(--accent);"></i></div>
            <div>
              <h6>Instalar una versión desactualizada del plugin</h6>
              <p>Pasa más seguido de lo que parece: el comercio instala el .zip que ya tenía guardado de antes, en vez de bajar el actual. Por ejemplo, en <strong>Magento y PrestaShop</strong> las versiones <strong>2.1.5 o anteriores</strong> del módulo solo soportan pagos en USD o COP — si el comercio necesita otra moneda, va a fallar hasta actualizar a la <strong>2.1.6+</strong>. En <strong>WooCommerce</strong>, un plugin viejo corriendo sobre un WordPress reciente puede generar errores sobre su propia definición de ruta de API interna (le falta el parámetro <code>permission_callback</code> que WordPress exige desde la versión 5.5). La solución casi siempre es la misma: descargar la versión más reciente directamente de la página oficial del plugin, no reusar una copia guardada de otro proyecto.</p>
            </div>
          </div>
          <div class="step">
            <div class="step-index"><i class="bi bi-signpost-split" style="color:var(--accent);"></i></div>
            <div>
              <h6>La ruta o el endpoint mal escrito</h6>
              <p>Varios plugins piden pegar manualmente una URL o ruta en un campo de configuración. En <strong>Jumpseller</strong>, por ejemplo, existe el campo <strong>"Payment Method URL"</strong>, que debe llevar exactamente el valor de <strong>Endpoint</strong> que PlacetoPay envió por correo — si queda incompleto, con espacios de más o apuntando al ambiente equivocado, el método de pago no aparece o falla al procesar. Algo parecido pasa en <strong>VTEX</strong> si se deja el ambiente de <strong>pruebas</strong> activo en un comercio que ya está en producción (o al revés): son endpoints distintos, y mezclarlos rompe el flujo de pago aunque las credenciales estén perfectas.</p>
            </div>
          </div>
        </div>

        <div class="info-box tip">
          <span class="info-box-icon">💡</span>
          <span><strong>Regla práctica para asesorar a un comercio:</strong> antes de dar soporte por un plugin que "no funciona", confirma primero estas dos cosas — que esté usando la <strong>última versión</strong> descargada de la página oficial del plugin, y que cada URL o credencial que haya pegado a mano coincida <strong>letra por letra</strong> (sin espacios, sin recortar) con lo que le llegó por correo. La mayoría de los casos se resuelven ahí, antes de tocar código.</span>
        </div>
      </section>

      <!-- REFERENCIA -->
      <section class="section">
        <div class="section-header">
          <div class="section-icon"><i class="bi bi-journal-bookmark"></i></div>
          <h4>Referencia oficial</h4>
        </div>
        <p>Esta guía cubre lo esencial para integrar aquí. Para el detalle completo de cada campo, códigos de error y casos avanzados, consulta siempre la documentación oficial de PlacetoPay:</p>
        <div class="plugin-row">
          <a class="btn-ghost" href="https://docs.placetopay.dev/checkout/api/reference/session/" target="_blank" rel="noopener"><i class="bi bi-book"></i> Referencia de Sesión (Checkout API)</a>
          <a class="btn-ghost" href="https://docs.placetopay.dev/autopay/authentication/" target="_blank" rel="noopener"><i class="bi bi-shield-lock"></i> Autenticación</a>
          <a class="btn-ghost" href="https://docs.placetopay.dev/checkout/plugins/" target="_blank" rel="noopener"><i class="bi bi-plug"></i> Plugins &amp; Librerías</a>
        </div>
      </section>

    </div>
  </main>
</div>

<script>
// Desplegables del sidebar
document.querySelectorAll('.nav-title').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const target = document.getElementById(this.dataset.target);
        const collapsed = this.classList.toggle('collapsed');
        this.setAttribute('aria-expanded', String(!collapsed));
        if (target) target.classList.toggle('collapsed', collapsed);
    });
});

// Scroll suave para los links del sidebar (ignora los "próximamente")
document.querySelectorAll('.nav a[href^="#"]:not(.soon)').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.nav a').forEach(a => a.classList.remove('active'));
        this.classList.add('active');
    });
});
document.querySelectorAll('.nav a.soon').forEach(function(link) {
    link.addEventListener('click', function(e) { e.preventDefault(); });
});

// Búsqueda simple
document.querySelector('.search').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.section, .callout').forEach(function(sec) {
        const text = sec.textContent.toLowerCase();
        sec.style.opacity = (!q || text.includes(q)) ? '1' : '0.25';
    });
});

// Pestañas de bloques de código (lenguaje o tipo de pago) + botón copiar
document.querySelectorAll('.code-block').forEach(function(block) {
    const tabs = block.querySelectorAll('.code-tab');
    const panels = block.querySelectorAll('.code-panel');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            const key = tab.dataset.key;
            tabs.forEach(t => t.classList.toggle('active', t === tab));
            panels.forEach(p => p.classList.toggle('active', p.dataset.key === key));
        });
    });

    const copyBtn = block.querySelector('.code-copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const active = block.querySelector('.code-panel.active');
            if (!active) return;
            navigator.clipboard.writeText(active.innerText).then(function() {
                const original = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="bi bi-check2"></i> Copiado';
                setTimeout(function() { copyBtn.innerHTML = original; }, 1400);
            });
        });
    }
});
</script>
</body>
</html>
