<?php
    session_start();

    if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: index.php");
    exit();
    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesiones</title>
        <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <!-- Tu CSS -->
    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo filemtime(__DIR__ . '/assets/css/estilos.css'); ?>">
    <link rel="stylesheet" href="assets/css/styles-sesiones.css?v=<?php echo filemtime(__DIR__ . '/assets/css/styles-sesiones.css'); ?>">
    <?php $theme_seccion = 'sesiones'; require_once __DIR__ . '/php/theme.php'; ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css">
    <link rel="stylesheet"
        href="assets/css/components/driver-theme.css?v=<?php echo filemtime(__DIR__ . '/assets/css/components/driver-theme.css'); ?>">
</head>


<body class="d-flex flex-column min-vh-100">
    <?php
        $nav_back_url  = "home.php";
        $nav_back_text = "Atras";
        $nav_base      = "./";
        require_once 'php/navbar.php';
    ?>

    <?php
        $categorias = [
            [
                'title'     => 'Tiendas Online',
                'link'      => 'views/games/juegos.php',
                'icon'      => 'fa-solid fa-gamepad',
                'accent'    => '#FF6C0C',
                'secondary' => '#FF9C45',
                'servicios' => 'Web Checkout · API Gateway',
                'pago'      => 'Pago Basico · Pago Mixto',
            ],
            [
                'title'     => 'Plataformas Digitales',
                'link'      => 'views/plataformas/suscripciones.php',
                'icon'      => 'bi bi-google-play',
                'accent'    => '#00CFB4',
                'secondary' => '#2FD8C5',
                'servicios' => 'Web Checkout · API Gateway',
                'pago'      => 'Pago + Suscripcion · Suscription · Recurrencias',
            ],
            [
                'title'     => 'Textiles',
                'link'      => 'views/textil/textiles.php',
                'icon'      => 'fa-solid fa-tshirt',
                'accent'    => '#0062A8',
                'secondary' => '#3B87D7',
                'servicios' => 'API Link de Pago · WC',
                'pago'      => 'Pago Basico',
            ],
            [
                'title'     => 'Dispersiones',
                'link'      => 'views/dispersiones/tickets.php',
                'icon'      => 'bi bi-airplane-fill',
                'accent'    => '#4C5F71',
                'secondary' => '#8193A5',
                'servicios' => 'Web Checkout',
                'pago'      => 'Dispersion',
            ],
            [
                'title'     => 'Reservaciones',
                'link'      => 'views/reservaciones/hotel.php',
                'icon'      => 'fa-solid fa-calendar-check',
                'accent'    => '#7D868C',
                'secondary' => '#A8B1BA',
                'servicios' => 'Web Checkout',
                'pago'      => 'Preautorizacion',
            ],
        ];
    ?>

    <main class="container py-4 py-lg-5">
        <section class="hero-card p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="hero-badge"><i class="fa-solid fa-backward-fast"></i> Sesiones</span>
                    <h1 class="display-5 fw-bold mt-3 mb-3">Elige la sesion que vas a usar</h1>
                    <p class="lead mb-0">Cada categoria simula un flujo de pago distinto con Place to Pay.</p>
                </div>
                <div class="col-lg-4">
                    <div class="hero-panel" id="lista-integraciones">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($categorias as $categoria): ?>
                                <li><?= htmlspecialchars($categoria['title']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4" id="categorias-grid">
            <?php foreach ($categorias as $index => $categoria): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card category-card h-100">
                        <div class="card-visual"
                            style="background: linear-gradient(135deg, <?= htmlspecialchars($categoria['accent']) ?> 0%, <?= htmlspecialchars($categoria['secondary']) ?> 100%);">
                            <i class="<?= htmlspecialchars($categoria['icon']) ?>"></i>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="method-pill mb-3"<?= $index === 0 ? ' id="tour-card-method"' : '' ?>>
                                <i class="bi bi-shield-check"></i>
                                <?= htmlspecialchars($categoria['servicios']) ?>
                            </div>
                            <div class="card-title-row">
                                <span class="title-icon"><i class="<?= htmlspecialchars($categoria['icon']) ?>"></i></span>
                                <h5 class="card-title mb-0"<?= $index === 0 ? ' id="tour-card-title"' : '' ?>><?= htmlspecialchars($categoria['title']) ?></h5>
                            </div>
                            <p class="card-text mb-4"<?= $index === 0 ? ' id="tour-card-description"' : '' ?>>
                                <span class="detail-title">Pago</span>
                                <?= htmlspecialchars($categoria['pago']) ?>
                            </p>
                            <a href="<?= htmlspecialchars($categoria['link']) ?>" class="btn btn-continue mt-auto align-self-start">Ver categoria <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validaciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
    <script src="assets/js/components/driver-tours/tour-sesiones.js"></script>
</body>
</html>
