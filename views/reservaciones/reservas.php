<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../php/conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

// Contar registros del usuario actual
$correo_sesion = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

$total_ordenes = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes"))['total'];
$total_subs    = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM suscripciones WHERE usuario_id = '$correo_sesion'"))['total'];
$total_recs    = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM recurrencias WHERE usuario_id = '$correo_sesion'"))['total'];

$total_pagos = $total_ordenes + $total_subs + $total_recs; 
$total_pagos= number_format($total_pagos, 0, ',', '.');



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historiales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php $theme_seccion = 'preautor'; require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>

</head>
<style>
    body {
        /* background-image: url(../assets/images/bg26.jpg); */
        background-color: #000000;
        color: var(--pt-text);
        background-repeat: no-repeat;
        background-position: center;
        background-attachment: fixed;
        background-size: cover;
        min-height: 100vh;
        font-family: 'Barlow', sans-serif;
    }
    .navbar {
        background-color: #0f0f0fa9 !important;
        backdrop-filter: blur(8px);
    }
    .panel-container {
        max-width: 700px;
        margin: 3rem auto;
        padding: 0 1rem;
    }
    .panel-titulo {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 0.4rem;
        color: var(--pt-text);
    }
    .panel-subtitulo {
        font-size: 0.9rem;
        color: var(--pt-text);
        margin-bottom: 2rem;
    }
    .historial-card {
        background: var(--pt-boxitem);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 0;
        margin-bottom: 1rem;
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        text-decoration: none;
        transition: all 0.2s ease;
        backdrop-filter: blur(8px);
        overflow: hidden;
        min-height: 140px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.5);
    }
    .historial-card:hover {
        border-color: var(--card-color);
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(0,0,0,0.4);
        text-decoration: none;
    }
    /* Zona de imagen (variable estructural).
       Para poner tu imagen, define --card-img en el <a>:
       style="--card-img:url('ruta/a/tu/imagen.jpg')"
       Si no la defines, se usa el color/degradado de respaldo (--card-bg). */
    .card-image {
        width: 42%;
        min-width: 130px;
        max-width: 220px;
        flex-shrink: 0;
        position: relative;
        background-image: var(--card-img, none);
        background-size: cover;
        background-position: center;
        background-color: var(--card-bg);
    }
    .card-image::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(0,0,0,0) 55%, rgba(15,15,15,0.9) 100%);
    }
    .card-image-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(10,10,10,0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: var(--card-color);
        backdrop-filter: blur(4px);
        z-index: 2;
    }
    .card-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.2rem 1.6rem;
        flex: 1;
    }
    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        background: var(--card-bg);
    }
    .card-icon2 {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        background-color: #f0b429;
    }
    .card-name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--pt-text);
        margin-bottom: 0.1rem;
    }
    .card-desc {
        font-size: 0.8rem;
        color: #8a8d96;
    }
    .card-badge {
        background: var(--card-bg);
        color: var(--card-color);
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .card-arrow {
        color: #8a8d96;
        font-size: 1.1rem;
        margin-left: 0.5rem;
    }
     .second-title {
        background: linear-gradient(135deg, rgba(61, 168, 255, 0.20), rgba(38, 201, 241, 0.08));
        border: 1px solid rgba(61, 168, 255, 0.35);
        border-radius: 14px;
        padding: 1.25rem 1.75rem;
        margin: 0 10px 20px;
        gap: 1rem;
        align-items: center;
        font-size: 1.05rem;
        color: var(--pt-text);
        line-height: 1.5;
        box-shadow: 0 6px 22px rgba(61, 168, 255, 0.18);
    }
    .second-title i{color: #3da8ff;font-size:1.7rem;flex-shrink:0;}
    .second-title strong{color: rgb(38, 201, 241); font-size: 1.12rem;}



    .historial-divider { height: 1px; background: var(--border); margin: 0.8rem 0; }
</style>
<body>
    <?php
        $nav_back_url  = "../../sesiones.php";
        $nav_back_text = "Atrás";
        $nav_base      = "../../";
        require_once '../../php/navbar.php';
    ?>

    <div class="container-fluid text-center px-4" >
       <div class="second-title ">
        <i class="fa-solid fa-calendar-check fs-3"></i>
        <div>
            <strong>Bienvenido a la sesion de reservaciones</strong>
            <br>
            Elige algun negocio para hacer tu reservacion
        </div>
    </div>

    <div class="panel-container text-start">
        <!-- enlaces-->
        <!-- Personaliza esta tarjeta agregando --card-img: url('../assets/images/tu-imagen.jpg'); en el style -->
        <a href="hotel.php" class="historial-card" style="--card-color: #ffffff; --card-bg: hsla(194, 100%, 50%, 0.12); --card-img: url('/assets/images/img/hoteles.jpg');">
            <div class="card-image">
                <span class="card-image-badge">
                    <i class="bi bi-building-fill"></i>
                </span>
            </div>
            <div class="card-left">
                <div>
                    <div class="card-name">Cuarto de hotel</div>
                    <div class="card-desc">Rerserva la habitacion donde vas a hospedarte</div>
                </div>
            </div>
        </a>

        <!--<a href="reg-sus.php" class="historial-card" style="--card-color: hsl(44, 100%, 70%); --card-bg: rgba(247, 144, 85, 0.12);">
            <div class="card-left">
                <div class="card-icon">
                    <i class="fa-solid fa-tshirt" style="color: hsl(44, 100%, 77%);"></i>
                </div>
                <div>
                    <div class="card-name">Camisetas</div>
                    <div class="card-desc">Compra camisetas que mas te gusten</div>
                </div>
            </div>
        </a> -->

        <div class="historial-divider"></div>
        
    </div>

        

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>