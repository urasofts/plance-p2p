<?php
    session_start();


    if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
        echo '<script>
            alert("Por favor, inicie sesión para acceder a esta página.");
            window.location.href = "index.php";
            </script>';

        session_destroy();
        die();

        
    }
    //aqui debo asegurarme de que tengo que poner session_destroy();


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.min.js"></script>
  
    <?php $theme_seccion = 'home'; require_once __DIR__ . '/php/theme.php'; ?>
   <!-- <link rel="stylesheet" href="assets/css/estilov=<?php echo filemtime(__DIR__ . '/assets/css/estilo'); ?>"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css">
    <link rel="stylesheet"
        href="assets/css/components/driver-theme.css?v=<?php echo filemtime(__DIR__ . '/assets/css/components/driver-theme.css'); ?>">
</head>

  <!-- Logica NAV -->
<?php
$nav_base = ''; // Ajusta esta ruta si tu navbar.php está en un subdirectorio
$nav_back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $nav_base . 'home.php';
$nav_back_text = 'Volver';

// Traer foto de perfil del usuario en sesión
$nav_avatar    = '';
$nav_initials  = '';

if (isset($_SESSION['user_id'])) {
    // Reutilizar conexión si ya existe, si no crear una
    if (!isset($conexion)) {
        $conexion = plance_db_connect();
    }
    if ($conexion) {
        $nav_uid = intval($_SESSION['user_id']);
        $nav_row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT profile_image, usuario FROM users WHERE id = '$nav_uid'"));
        if ($nav_row) {
            $nav_initials = strtoupper(substr($nav_row['usuario'] ?? 'U', 0, 1));
            $img_path     = $nav_base . 'uploads/' . ($nav_row['profile_image'] ?? '');
            if (!empty($nav_row['profile_image']) && file_exists($nav_base . 'uploads/' . $nav_row['profile_image'])) {
                $nav_avatar = $nav_base . 'uploads/' . htmlspecialchars($nav_row['profile_image']);
            }
        }
    }
}
?>


<!-- ESTILOS PROPIOS -->
<style>

 
    body {
        /*background-image: url(assets/images/bg25.jpg); */
        background-color: #0a0a0a;
        color: white;
        
        background-repeat: no-repeat;
        background-position: center;
        background-attachment: fixed;
        background-size: cover;
        font-family: calibri, sans-serif;

    }
    .home-card {
        background: transparent;
        border-radius: 20px;
        margin: 0;
        padding-bottom: 10px;
    }
    #welcomeInner { overflow: hidden; }
    #welcomeTitle, #welcomeSubtitle { opacity: 0; }
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(255, 94, 0, 0.5);
    }
    .card-img-top {
        border-radius: 15px 15px 0 0;
        height: 200px;
        width: 100%;
        object-fit: cover;
    }
    .navbar-welcome.scrolled {
    background-color: rgba(15, 23, 42, 0.95) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
    }

    .img-logo {
        margin-bottom: 20px;
        align-self: center;
    }

    .navbar.scrolled {
        background-color: rgba(15, 23, 42, 0.95) !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5); 
    }
    /* ── TARJETAS DE ACCESO RÁPIDO ── */
    .home-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        padding: 10px 0 20px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .home-nav-card {
        display: flex;
        flex-direction: column;
        text-align: left;
        text-decoration: none;
        cursor: pointer;
        opacity: 0;
        padding: 22px;
        border-radius: 18px;
        background: var(--pt-bg-card, rgba(30, 30, 32, 0.85));
        border: 1px solid var(--pt-border, rgba(255,255,255,0.08));
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .home-nav-card:hover {
        transform: translateY(-6px);
        border-color: rgba(240, 114, 41, 0.5);
        box-shadow: 0 12px 28px rgba(0,0,0,0.35), 0 0 0 1px rgba(240, 180, 41, 0.1);
    }

    .home-nav-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: rgba(240, 180, 41, 0.08);
        border: 1px solid rgba(240, 180, 41, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 14px;
        transition: all 0.22s ease;
    }

    .home-nav-card:hover .home-nav-icon {
        background: rgba(240, 180, 41, 0.16);
        transform: scale(1.06);
    }

    .home-nav-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--pt-text, #f0f1f3);
        margin-bottom: 6px;
    }

    .home-nav-desc {
        font-size: 0.85rem;
        color: var(--pt-text-sec, rgba(255,255,255,0.75));
        line-height: 1.5;
        margin-bottom: 0;
    }

    .home-nav-card:hover .home-nav-title {
        color: #f07229;
    }

    main {
        flex: 1;
    }
    .navbar {
        background-color: var(--pt-navbar, #252424a9) !important;
        backdrop-filter: blur(8px);
        color: var(--pt-text, #ffff);
    }

    /* Estilos para el avatar en la barra de navegación */
    .nav-avatar-wrap {
        display: inline-block;
        border-radius: 50%;
        overflow: hidden;
    }
    .nav-avatar-img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f07229;
        transition: border-color 0.2s, transform 0.2s;
    }

    .nav-avatar-wrap {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    .nav-avatar-wrap:hover { text-decoration: none; }

    .nav-avatar-img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f06829;
        transition: border-color 0.2s, transform 0.2s;
    }
    .nav-avatar-img:hover { border-color: #fff; transform: scale(1.08); }

    .nav-avatar-initials {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f06129, #df6f24);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.8rem;
        color: #0d0e10;
        border: 2px solid #f06129;
        transition: border-color 0.2s, transform 0.2s;
        flex-shrink: 0;
    }
    .nav-avatar-initials:hover { border-color: #fff; transform: scale(1.08); }

    .nav-username {
        background-color: var(--pt-bg-card, hsla(120, 2%, 10%, 0.84));
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        color: var(--pt-text, #ffff);
    }    
    /* ── DROPDOWN ── */
    .dropdown {
        position: relative;
        display: inline-block;

    }

    .dropbtn {
        background: hsla(120, 2%, 10%, 0.84);
        color: #f07929;
        border: 1.5px solid rgba(240, 104, 41, 0.3);
        border-radius: 8px;
        padding: 6px 14px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .dropbtn:hover {
        border-color: #f07929;
        background: rgba(240, 180, 41, 0.1);
    }

    .dropdown-content {
        display: none;
        position: absolute;
        left: 0;
        top: calc(100% + 6px);
        background: var(--pt-bg-surface, #16181c);
        border: 1px solid var(--pt-border, #2e3038);
        border-radius: 10px;
        min-width: 170px;
        z-index: 999;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        animation: dropFade 0.15s ease;
        margin-top: 1px;
    }

    @keyframes dropFade {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dropdown-content a {
        display: block;
        padding: 0.6rem 1rem;
        color: var(--pt-text, #f0f1f3);
        font-size: 0.87rem;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .dropdown-content a:hover {
        background: rgba(240, 180, 41, 0.1);
        color: #f07229;
        text-decoration: none;
    }

    .dropdown-content hr {
        border-color: var(--pt-border, #2e3038);
        margin: 0.2rem 0;
    }

    .dropdown-content .cerrar-sesion {
        color: #e05252 !important;
    }
    .dropdown-content .cerrar-sesion:hover {
        background: rgba(224, 82, 82, 0.1) !important;
        color: #e05252 !important;
    }

    .dropdown:hover .dropdown-content { display: block; }

    @keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px); /* opcional: pequeño movimiento */
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>




<body class="home d-flex flex-column min-vh-100 ">
    <nav class="navbar navbar-dark navbar-expand-lg px-2">
        <a class="navbar-brand fw-bold" href="<?= $nav_base ?>home.php" style="color: orange;">
            <img src="<?= $nav_base ?>assets/icons/icono.png" alt="Logo" style="width: 30px;">
        </a>

        <!-- BOTON DE RETROSESO -->
        <!--<a href="<​?= htmlspecialchars($nav_back_url) ?>" class="btn" style="color: orangered;">
            <i class="bi bi-backspace-fill"></i> <?= htmlspecialchars($nav_back_text) ?>
        </a>  -->

        <div class="ms-auto d-flex align-items-center gap-2">

            <!-- Nombre del usuario -->
            <span class="nav-username">
                <?= isset($_SESSION['usuario']) ? "Hola, " . htmlspecialchars($_SESSION['usuario']) : "Invitado" ?>
            </span>

            <!-- Avatar clickeable → perfil -->
            <a href="<?= $nav_base ?>../views/profile/index.php" class="nav-avatar-wrap" title="Mi perfil">
                <?php if ($nav_avatar): ?>
                    <img src="<?= $nav_avatar ?>" class="nav-avatar-img" alt="Perfil">
                <?php else: ?>
                    <div class="nav-avatar-initials"><?= $nav_initials ?: 'U' ?></div>
                <?php endif; ?>
            </a>
            <!-- El desplegable a la derecha -->
            <div class="dropdown">
                <button class="dropbtn">Opciones</button>
                <div class="dropdown-content">
                    <a href="<?= $nav_base ?>profile/index.php">Perfiles</a>
                    <a href="<?= $nav_base ?>contactos.php">Contactos</a>
                    <hr>
                    <a href="<?= $nav_base ?>php/cerrar_sesion.php" class="cerrar-sesion">Cerrar sesión</a>
                </div>
            </div> 

            <!--<a href="<?= $nav_base ?>php/cerrar_sesion.php" class="btn btn-sm btn-outline ms-1" style="background: #ff5e00f5;">
                Cerrar sesión
            </a> -->
        </div>
    </nav>
        <div class="container-fluid px-2 py-5">
        <div class="home-card text-center p-5 mb-4" id="welcomeCard">
            <div id="welcomeInner">
                <h1 class="fw-bold" id="welcomeTitle">¡Bienvenido<?= isset($_SESSION['usuario']) ? ', ' . htmlspecialchars($_SESSION['usuario']) : '' ?>! 👋</h1>
                <p class="text" style="color: white" id="welcomeSubtitle">Listo para continuar tu progreso</p>
            </div>
        </div>

        <!-- Tarjetas de acceso rápido -->
        <div class="home-cards-grid" id="home-cards-grid">

            <a href="sesiones.php" class="home-nav-card" title="Sesiones">
                <div class="home-nav-icon">
                    <i class="bi bi-cart-plus-fill" style="color: rgb(255, 102, 0);"></i>
                </div>
                <span class="home-nav-title">Sesiones</span>
                <p class="home-nav-desc">Simula pagos con distintos flujos: Pagos Rapidos, Suscripciones, Reservas y más.</p>
            </a>

            <a href="views/historial/historial.php" class="home-nav-card" title="Historial">
                <div class="home-nav-icon">
                    <i class="bi bi-file-text-fill" style="color: rgb(255, 102, 0);"></i>
                </div>
                <span class="home-nav-title">Historial</span>
                <p class="home-nav-desc">Revisa todas tus transacciones y consulta el estado de cada una.</p>
            </a>

            <a href="views/guias/guia.php" class="home-nav-card" title="Guia">
                <div class="home-nav-icon">
                    <i class="bi bi-book-half" style="color: rgb(255, 102, 0);"></i>
                </div>
                <span class="home-nav-title">Guía</span>
                <p class="home-nav-desc">Aprende paso a paso cómo funciona la plataforma.</p>
            </a>

            <a href="views/settings/ajustes.php" class="home-nav-card" title="Configuración">
                <div class="home-nav-icon">
                    <i class="bi bi-gear-fill" style="color: rgb(255, 102, 0);"></i>
                </div>
                <span class="home-nav-title">Ajustes</span>
                <p class="home-nav-desc">Personaliza tu cuenta y tus preferencias.</p>
            </a>

        </div>
    </div>
    
    <script>
        (function(){
            // ── Animación de bienvenida temporal ──
            if (typeof anime !== 'undefined') {
                const welcomeInner = document.getElementById('welcomeInner');
                const welcomeStartHeight = welcomeInner ? welcomeInner.scrollHeight : 0;

                anime.timeline({ easing: 'easeOutExpo' })
                    .add({
                        targets: '#welcomeTitle',
                        opacity: [0, 1],
                        translateY: [-16, 0],
                        duration: 700
                    })
                    .add({
                        targets: '#welcomeSubtitle',
                        opacity: [0, 1],
                        translateY: [-10, 0],
                        duration: 600
                    }, '-=450')
                    .add({
                        targets: '#welcomeInner',
                        opacity: [1, 0.15],
                        translateY: [0, -14],
                        height: [welcomeStartHeight, 0],
                        marginBottom: [null, 0],
                        duration: 700,
                        easing: 'easeInOutQuad',
                        delay: 2600
                    });

                // Stagger de accesos rápidos
                anime({
                    targets: '.home-nav-card',
                    opacity: [0, 1],
                    translateY: [18, 0],
                    delay: anime.stagger(90, { start: 300 }),
                    duration: 550,
                    easing: 'easeOutQuad'
                });
            }
        })();
    </script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
    <script src="assets/js/components/driver-tours/tour-home.js"></script>
</body>
</html>