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

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispersiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php $theme_seccion = 'dispersion'; require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    body {
        background-color: #000000;
        color: white;
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
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.2rem;
        justify-content: center;
    }
    /* Carta en forma de cuadro */
    .tienda-card {
        position: relative;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 16px;
        padding: 1.4rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-end;
        text-align: left;
        text-decoration: none;
        transition: all 0.2s ease;
        overflow: hidden;
        aspect-ratio: 1 / 1;
        background-image: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(10,10,10,0.92) 100%),
                           var(--card-img, linear-gradient(135deg, var(--card-bg), rgba(255,255,255,0.03)));
        background-size: cover;
        background-position: center;
    }
    .tienda-card:hover {
        border-color: var(--card-color);
        transform: translateY(-4px);
        box-shadow: 0 6px 24px rgba(0,0,0,0.4);
        text-decoration: none;
    }
    .card-image-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(10,10,10,0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        backdrop-filter: blur(4px);
        z-index: 2;
    }
    .card-name {
        font-size: 1.05rem;
        font-weight: 700;
        color: #f0f1f3;
        margin-bottom: 0.3rem;
        position: relative;
        z-index: 2;
    }
    .card-desc {
        font-size: 0.8rem;
        color: #c7c9cf;
        position: relative;
        z-index: 2;
    }
     .second-title {
        background: linear-gradient(135deg, rgba(10, 255, 51, 0.20), rgba(65, 141, 34, 0.10));
        border: 1px solid rgba(10, 255, 51, 0.35);
        border-radius: 14px;
        padding: 1.25rem 1.75rem;
        margin: 0 10px 20px;
        gap: 1rem;
        align-items: center;
        font-size: 1.05rem;
        color: var(--pt-text);
        line-height: 1.5;
        box-shadow: 0 6px 22px rgba(10, 255, 51, 0.18);
    }
    .second-title i{color: hsl(130, 100%, 52%);font-size:1.7rem;flex-shrink:0;}
    .second-title strong{color: rgb(65, 141, 34); font-size: 1.12rem;}
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
        <i class="fa-solid fa-plane-departure fs-3"></i>
        <div>
            <strong>Bienvenido a la tienda de ticketes de avion</strong>
            <br>
            Elige tu destino y reserva tu vuelo
        </div>
    </div>

    <div class="panel-container">
        <div class="cards-grid">
            <!-- carta en forma de cuadro -->
            <!-- Personaliza agregando --card-img: url('../assets/images/tu-imagen.jpg'); en el style -->
            <a href="tickets.php" class="tienda-card" style="--card-color: #ffffff; --card-bg: hsla(194, 100%, 50%, 0.12); --card-img: url('/assets/images/img/vuelos.jpg');">
                <span class="card-image-badge">
                    <i class="fa-solid fa-plane" style="color: #4aa8ff;"></i>
                </span>
                <div class="card-name">Ticketes de avion</div>
                <div class="card-desc">Compra tu boleto de avion al mejor precio</div>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
