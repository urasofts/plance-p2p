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
    <link rel="stylesheet" href="assets/css/estilos?v=<?php echo filemtime(__DIR__ . '/assets/css/e'); ?>">
    <?php $theme_seccion = 'sesiones'; require_once __DIR__ . '/php/theme.php'; ?>
</head>


<style>
    body {
        /* background-image: url(assets/images/bg24.jpg);  */
        background-color: #181818;
        background-repeat: no-repeat;
        background-position: center;
        background-attachment: fixed;
        background-size: cover;

        font-family: 'Barlow', sans-serif;
    }



    /*.dashboard-card-1, .dashboard-card-2{

        

        background: rgba(20, 20, 20, 0.7);
        text-align: center;
        align-self: start;
        border-radius: 15px;
        transition: 0.3s ease;
        cursor: pointer;
        border: 1px solid rgba(255,255,255,0.05);
        margin: auto;
        width: 100%;
        height: 120%;
        color: white;
        transform: translateY(30px);
        animation: fadeIn 1.5s ease-in-out forwards;
        opacity: 0;
        transition: all 0.6s ease;
    } */


    /*.dashboard-card-1:hover{
        transform: translateY(-10px);
        box-shadow: 0 0 20px hsla(182, 75%, 49%, 0.502);
        color:  hsla(182, 75%, 49%, 0.671);
        background-color: #0a0a0a73;
        
    } */

    /*.dashboard-card-2:hover{
        transform: translateY(-10px);
        box-shadow: 0 0 20px hsla(209, 100%, 57%, 0.502);
        background-color: #0a0a0a73;
             color: hsla(209, 100%, 57%, 0.671);

        /* box-shadow: 0 0 20px hsla(209, 100%, 57%, 0.502);  BOX SHADOW ORIGINAL*/
            /* color: hsla(209, 100%, 57%, 0.671); COLOR ORIGINAL
    }*/

    .pagob {
    position: absolute;
    top: -1px;
    left: -1px;
    background-color: rgb(255, 102, 0);
    color: rgb(0, 0, 0);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    padding: 0.15rem 0.5rem;
    /*    border-radius: 0 0 10px 0; */
    border-radius: 10px;
    }

    .suscrip {
    position: absolute;
    top: -1px;
    left: -1px;
    background-color: rgb(255, 102, 0);
    color: rgb(0, 0, 0);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    padding: 0.15rem 0.5rem;
    /*    border-radius: 0 0 10px 0; */
    border-radius: 10px;
    }

    .card-img-top {
        border-radius: 15px 15px 0 0;
        height: 200px;
        width: 100%;
        object-fit: cover;
    }
    main {
        flex: 1;
    }
    .navbar {
        background-color: var(--pt-navbar, #0f0f0fa9) !important;
        backdrop-filter: blur(8px);
        color: var(--pt-text, #ffffff);
    }
    .back:hover {
        background: #ff6811f5;
        transform: translateY(-1px);
        box-shadow: 0 5px 10px rgba(255, 94, 0, 0.5);

    }
        /* ── SPEED DIAL (iconos tipo navegador) ── */
    .speed-dial-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 28px;
        padding: 10px 0 20px;
    }

    .speed-dial-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        animation: fadeIn 0.6s ease forwards;
        opacity: 0;
        position: relative;
    }
    .speed-dial-item:nth-child(1) { animation-delay: 0.1s; }
    .speed-dial-item:nth-child(2) { animation-delay: 0.2s; }
    .speed-dial-item:nth-child(3) { animation-delay: 0.3s; }

    .speed-dial-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .detalles-toggle {
        background: none;
        border: none;
        padding: 0;
        font-size: 0.78rem;
        font-weight: 600;
        color: rgb(255, 102, 0);
        text-decoration: underline;
        cursor: pointer;
    }

    .detalles-toggle:hover,
    .detalles-toggle.active {
        color: rgb(255, 150, 70);
    }

    .speed-dial-details {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: var(--pt-bg-surface, #16181c);
        border: 1px solid var(--pt-border, #2e3038);
        border-radius: 8px;
        padding: 8px 12px;
        min-width: 170px;
        display: none;
        z-index: 1000;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        margin-top: 8px;
        text-align: left;
        animation: dropFade 0.15s ease;
    }

    .speed-dial-details.show {
        display: block;
    }

    .speed-dial-details small {
        display: block;
        color: var(--pt-text-sec, rgba(255, 255, 255, 0.75));
        line-height: 1.4;
        margin: 3px 0;
    }

    .speed-dial-details .detail-title {
        color: rgb(255, 102, 0);
        font-weight: 600;
        margin-bottom: 4px;
    }

    .speed-dial-details .detail-subtitle {
        color: var(--pt-text-sec, rgba(255, 255, 255, 0.75))    ;
        margin-left: 8px;
    }

    .speed-dial-icon {
        width: 170px;
        height: 100px;
        border-radius: 18px;
        background: var(--pt-bg-card, rgba(30, 30, 32, 0.85));
        border: 1px solid var(--pt-border, rgba(255,255,255,0.08));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        transition: all 0.22s ease;
        box-shadow: 0 4px 18px rgba(0,0,0,0.4);
        backdrop-filter: blur(6px);
    }

    .speed-dial-link:hover .speed-dial-icon {
        transform: translateY(-6px) scale(1.08);
        border-color: rgba(240, 121, 41, 0.5);
        box-shadow: 0 8px 28px rgba(240, 180, 41, 0.25);
        background: rgba(240, 180, 41, 0.08);
    }

    .speed-dial-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--pt-text-sec, rgba(255,255,255,0.75));
        text-align: center;
        max-width: 150px;

        overflow: hidden;
        text-overflow: ellipsis;
        transition: color 0.2s;
    }
    .speed-dial-link:hover .speed-dial-label {
       color: rgb(255, 102, 0);
    }
    .second-title {
        background: #08080800;
        border-radius:0 12px 12px 0;
        padding:0.9rem 1.2rem;
        margin: 10px;
        gap:0.8rem;
        align-items: center;
        font-size: 1.00rem;
        color: var(--pt-text, #f0f1f3);
        line-height: 1.7;

    }
    .second-title i{color: rgb(255, 102, 0); font-size:1.2rem;flex-shrink:0;}
    .second-title strong{color: rgb(255, 102, 0);}
    .dropdown {
        position: relative;
        display: inline-block;
    }
    .dropdown-content {
        display: none;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        top: calc(100% + 6px);
        background: var(--pt-bg-surface, #16181c);
        border: 1px solid var(--pt-border, #2e3038);
        border-radius: 10px;
        min-width: 170px;
        z-index: 999;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        animation: dropFade 0.15s ease;
    }

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


<body class="d-flex flex-column min-vh-100">
        <?php
            $nav_back_url  = "home.php";
            $nav_back_text = "Atras";
            $nav_base      = "./";
            require_once 'php/navbar.php';
        ?>

    <div class="container text-center" >
       <div class="second-title ">
        <i class="fa-solid fa-backward-fast fs-3" style="color: rgb(255, 94, 0);"></i>
        <div>
            <strong>Sesiones</strong>
            <br>
            Elige la sesion en la que vas a hacer a simular tus pagos
        </div>
    </div>
    </nav><br>

        <!-- Speed Dial tipo navegador  -->
        <div class="speed-dial-grid">

            <div class="speed-dial-item" title="Juegos Mobiles">
                <a href="views/games/juegos.php" class="speed-dial-link">
                    <div class="speed-dial-icon">
                        <i class="fa-solid fa-gamepad fs-3" style = "color: rgb(255, 102, 0);"></i>
                    </div>
                    <span class="speed-dial-label">Juegos Mobiles</span>
                </a>
                <button type="button" class="detalles-toggle" data-target="detalle-juegos">Detalles</button>
                <div class="speed-dial-details" id="detalle-juegos">
                    <small class="detail-title">Servicios </small>
                    <small class="detail-subtitle">Web Checkout</small>
                    <small class="detail-subtitle">API Gateway</small>
                    <small class="detail-title">Pago </small>
                    <small class="detail-subtitle">Pago Basico</small>
                    <small class="detail-subtitle">Pago Mixto</small>
                </div>
            </div>

            <div class="speed-dial-item" title="Plataformas Digitales">
                <a href="views/plataformas/suscripciones.php" class="speed-dial-link">
                    <div class="speed-dial-icon">
                        <i class="bi bi-google-play" style="color: rgb(255, 102, 0);"></i>
                    </div>
                    <span class="speed-dial-label">Plataformas Digitales</span>
                </a>
                <button type="button" class="detalles-toggle" data-target="detalle-plataformas">Detalles</button>
                <div class="speed-dial-details" id="detalle-plataformas">
                    <small class="detail-title">Servicios </small>
                    <small class="detail-subtitle">Web Checkout</small>
                    <small class="detail-subtitle">API Gateway</small>
                    <small class="detail-title">Pago </small>
                    <small class="detail-subtitle">Pago + Suscripcion</small>
                    <small class="detail-subtitle">Suscription</small>
                    <small class="detail-subtitle">Recurrencias</small>
                </div>
            </div>

            <div class="speed-dial-item" title="Ropa">
                <a href="views/textil/textiles.php" class="speed-dial-link">
                    <div class="speed-dial-icon">
                        <i class="fa-solid fa-tshirt fs-3" style="color: rgb(255, 102, 0);"></i>
                    </div>
                    <span class="speed-dial-label">Ropa</span>
                </a>
                <button type="button" class="detalles-toggle" data-target="detalle-ropa">Detalles</button>
                <div class="speed-dial-details" id="detalle-ropa">
                    <small class="detail-title">Servicios</small>
                    <small class="detail-subtitle">API Link De Pago - WC</small>
                    <small class="detail-title">Pago</small>
                    <small class="detail-subtitle">Pago Basico</small>
                </div>
            </div>

            <div class="speed-dial-item" title="Reservaciones">
                <a href="views/reservaciones/reservas.php" class="speed-dial-link">
                    <div class="speed-dial-icon">
                        <i class="fa-solid fa-calendar-check fs-3" style="color: rgb(255, 102, 0);"></i>
                    </div>
                    <span class="speed-dial-label">Reservaciones</span>
                </a>
                <button type="button" class="detalles-toggle" data-target="detalle-reservaciones">Detalles</button>
                <div class="speed-dial-details" id="detalle-reservaciones">
                    <small class="detail-title">Servicios </small>
                    <small class="detail-subtitle">Web Checkout</small>
                    <small class="detail-title">Pago </small>
                    <small class="detail-subtitle">Preautorizacion</small>
                </div>
            </div>

            <div class="speed-dial-item" title="Dispersiones">
                <a href="views/dispersiones/dispersion.php" class="speed-dial-link">
                    <div class="speed-dial-icon">
                        <i class="fa-solid fa-chart-line fs-3" style="color: rgb(255, 102, 0);"></i>
                    </div>
                    <span class="speed-dial-label">Dispersiones</span>
                </a>
                <button type="button" class="detalles-toggle" data-target="detalle-dispersiones">Detalles</button>
                <div class="speed-dial-details" id="detalle-dispersiones">
                    <small class="detail-title">Servicios </small>
                    <small class="detail-subtitle">Web Checkout</small>
                    <small class="detail-title">Pago </small>
                    <small class="detail-subtitle">Dispersion</small>
                </div>
            </div>
        </div>
    </div>


        <!-- <section>
            <div class="row g-4 justify-content-center text-center">
                <div class= "col-md-3 "> 
                    <a href="views/games/juegos.php" style="text-decoration: none;">
                            <div class="dashboard-card-1 p-1" >
                            <i class="fa-solid fa-gamepad fs-3 text-warning"></i>
                            
                            <h5 class="">Juegos</h4>
                            <small class="text" style="color: rgb(255, 196, 0);">Compra recargas y productos en linea </small>
                        </div>
                    </a>
                </div>       
                <div class="col-md-3">
                    <a href="views/plataformas/suscripciones.php" style="text-decoration: none;">
                        <div class="dashboard-card-2 p-1">
                            <i class="bi bi-google-play fs-3 text-warning"></i>
                            <h6 class="">Plataformas Digitales</h6>
                            <small class="text" style="color: rgb(255, 196, 0);" >Paga tus plataformas de streaming</small>
                        </div>
                    </a>
                </div>
            </div>
    </section> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/validaciones.js"></script>
    <script>
        document.querySelectorAll('.detalles-toggle').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var target = document.getElementById(btn.dataset.target);
                var isOpen = target.classList.contains('show');

                document.querySelectorAll('.speed-dial-details.show').forEach(function (el) {
                    el.classList.remove('show');
                });
                document.querySelectorAll('.detalles-toggle.active').forEach(function (el) {
                    el.classList.remove('active');
                });

                if (!isOpen) {
                    target.classList.add('show');
                    btn.classList.add('active');
                }
            });
        });

        document.addEventListener('click', function () {
            document.querySelectorAll('.speed-dial-details.show').forEach(function (el) {
                el.classList.remove('show');
            });
            document.querySelectorAll('.detalles-toggle.active').forEach(function (el) {
                el.classList.remove('active');
            });
        });
    </script>
</body>
</html>