<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- Tu CSS -->
    <link rel="stylesheet" href="assets/css/styles-welcome.css?v=<?php echo filemtime(__DIR__ . '/assets/css/styles-welcome.css'); ?>">
</head>

<body class="welcome">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-welcome px-2 fixed-top">
        <div class="container">

            <!-- Logo -->
            <a class="navbar-brand fw-bold d-flex align-items-center " href="welcome.php">
                <img src="assets/icons/icono.png" alt="Icono de Plance" style="height: 35px;">
            </a>

            <!-- Botón responsive -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="background-color: orange;">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse  " style="background-color: #3535357e; border-radius: 10px" id="navbarNav">

                <!-- IZQUIERDA (pegado al logo) -->
                <ul class="navbar-nav ms-3">
                    <li class="nav-item dropdown position-static">
                        <a class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown" style="color: rgb(255, 102, 0) ; border-radius: 5px; padding: 5px 10px;">
                            Plance
                        </a>

                        <div class="dropdown-menu w-100 mt-0 border-0 shadow">
                            <div class="container py-4">
                                <div class="row">

                                    <div class="col-md-4">
                                        <h6 class="fw-bold" style="color: orange">Conoce la plataforma</h6>
                                        <a class="dropdown-item" href="views/info/gestion.php">Gestión</a>
                                        <a class="dropdown-item" href="views/info/registros.php">Registros</a>
                                        <a class="dropdown-item" href="views/info/experiencia.php">Experiencia</a>
                                    </div>

                                    <div class="col-md-4">
                                        <h6 class="fw-bold" style="color: orange">Formas de pago</h6>
                                        <a class="dropdown-item" href="views/info/gestion.php">Web Checkout</a>
                                        <a class="dropdown-item" href="views/info/gestion.php">API Gateway</a>
                                        <a class="dropdown-item" href="views/info/registros.php">Estados de pago</a>
                                    </div>

                                    <div class="col-md-4">
                                        <h6 class="fw-bold" style="color: orange">Acceso</h6>
                                        <a class="dropdown-item" href="index.php">Iniciar sesión</a>
                                        <a class="dropdown-item" href="index.php">Registrarse</a>
                                        <a class="dropdown-item" href="php/entrar_invitado.php">Continuar como invitado</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- DERECHA (empujado automáticamente) -->
                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="php/entrar_invitado.php" class="btn-guest-nav">
                        <i class="bi bi-person"></i> Continuar como invitado
                    </a>
                    <a href="index.php" class="log btn px-4" style="background-color: rgb(255, 102, 0) ;;  border-radius: 5px; color: #1c1c1c">
                        Iniciar sesión
                    </a>
                </div>

            </div>
        </div>
    </nav>

    <!-- CONTENIDO -->
    <section class="welcome-content">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Bienvenido 👋</h1>
            <p class="lead mb-4">Tu panel ya está listo para comenzar.</p>

            <div class="welcome-cards-grid">
                <div class="welcome-feature-card">
                    <div class="wf-icon"><i class="fa-solid fa-table-cells-large"></i></div>
                    <h3>Gestión</h3>
                    <p>Administra tus transacciones y descubre cómo fluyen tus pagos entre diferentes servicios de Placetopay</p>
                    <a href="views/info/gestion.php" class="wf-link">Ver más <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="welcome-feature-card">
                    <div class="wf-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                    <h3>Registros</h3>
                    <p>Consulta el estado de cada transacción y revisa tu historial completo.</p>
                    <a href="views/info/registros.php" class="wf-link">Ver más <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <div class="welcome-feature-card">
                    <div class="wf-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                    <h3>Experiencia</h3>
                    <p>Descubre el ecosistema completo: perfil, seguridad, tokenización y todo lo que hace fluida la experiencia de pago.</p>
                    <a href="views/info/experiencia.php" class="wf-link">Ver más <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>



    <br><br><br><footer class=" text-white text-center py-3" style="background-color: #1a1919c0; position: fixed; bottom: 0; width: 100%; " >
        <p style="color: #ff5100;"> &copy; Evertec Placetopay SAS - Medellin | Jair Stiven| SAQ </p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
