<?php
session_start();

if (!isset($_SESSION['usuario']) && empty($_SESSION['invitado'])) {
    header("Location: ../../index.php");
    exit();
}

// ── PANEL DE INVITADO ──
if (!isset($_SESSION['usuario']) && !empty($_SESSION['invitado'])) {
    // Mostrar solo el panel de invitado y luego terminar
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Perfil — Invitado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
        <style>
            body {
                background-color: #19191a;
                color: #f0f1f3;
                font-family: 'Barlow', sans-serif;
                min-height: 100vh;
            }
            .navbar {
                background-color: #0f0f0fa9 !important;
                backdrop-filter: blur(8px);
                border-bottom: 1px solid #2e3038;
            }
            .guest-panel {
                max-width: 500px;
                margin: 3rem auto;
                padding: 2rem;
                background: #212224;
                border: 1px solid #2e3038;
                border-radius: 14px;
                text-align: center;
            }
            .guest-avatar {
                width: 80px;
                height: 80px;
                margin: 0 auto 1.5rem;
                background: linear-gradient(135deg, #f04729, #c96910);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.5rem;
                font-weight: 800;
                border: 3px solid #ff702d;
            }
            .guest-panel h2 {
                margin-bottom: 1rem;
                color: #ffffff;
                font-weight: 700;
            }
            .guest-panel p {
                color: #8a8d96;
                margin-bottom: 2rem;
                line-height: 1.6;
            }
            .btn-login {
                background: linear-gradient(135deg, #ff7139, #ff5722);
                color: white;
                border: none;
                padding: 0.7rem 2rem;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .btn-login:hover {
                color: white;
                text-decoration: none;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(255, 113, 57, 0.4);
            }
        </style>
    </head>
    <body>
        <?php
            $nav_base = '../../';
            $nav_back_url = '../../home.php';
            $nav_back_text = 'Volver';
            require_once '../../php/navbar.php';
        ?>
        
        <div class="guest-panel">
            <div class="guest-avatar">I</div>
            <h2>Invitado</h2>
            <p>Estás navegando como invitado. Crea una cuenta o inicia sesión para personalizar tu perfil y acceder a más características.</p>
            <a href="../../index.php" class="btn-login">Iniciar sesión</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

require_once '../../php/conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$user_id = intval($_SESSION['user_id'] ?? 0);
$row     = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM users WHERE id = '$user_id'"));

if (!$row) {
    header("Location: ../../index.php");
    exit();
}

$correo   = mysqli_real_escape_string($conexion, $row['correo']);

$total_ordenes        = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes WHERE correo = '$correo'"))['total'];
$total_aprobadas_ord   = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes WHERE correo = '$correo' AND estado = 'aprobada'"))['total'];
$total_rechazadas_ord  = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes WHERE correo = '$correo' AND estado = 'rechazada'"))['total'];



$total_subs           = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM suscripciones WHERE usuario_id = '$correo'"))['total'];
$total_aprobadas_subs  = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM suscripciones WHERE usuario_id = '$correo' AND estado = 'aprobada'"))['total'];
$total_rechazadas_subs = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM suscripciones WHERE usuario_id = '$correo' AND estado = 'rechazada'"))['total'];

$total_recurrencias      = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM recurrencias WHERE usuario_id = '$correo'"))['total'];
$total_aprobadas_rec     = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM recurrencias WHERE usuario_id = '$correo' AND estado = 'aprobada'"))['total'];
$total_rechazadas_rec    = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM recurrencias WHERE usuario_id = '$correo' AND estado = 'rechazada'"))['total'];

$total_ordenes_pago       = $total_ordenes + $total_subs + $total_recurrencias;
$total_aprobadas          = $total_aprobadas_ord + $total_aprobadas_subs + $total_aprobadas_rec;
$total_ordenes_rechazadas = $total_rechazadas_ord + $total_rechazadas_subs + $total_rechazadas_rec;

$total_pendientes = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes WHERE correo = '$correo' AND estado = 'pendiente'"))['total'];

// ── Gateway ──
$total_gw_ordenes   = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM gateway_ordenes WHERE correo = '$correo'"))['total'] ?? 0;
$total_gw_subs      = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM gateway_suscripciones WHERE correo = '$correo'"))['total'] ?? 0;
$total_gw_suscription = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM gateway_suscription WHERE correo = '$correo'"))['total'] ?? 0;





// ── Datos para el calendario (últimos 365 días) ──
// Traer fechas de ordenes y suscripciones del usuario
$actividad_dias = [];

$res_ord = mysqli_query($conexion, "SELECT DATE(created_at) as dia, COUNT(*) as cnt FROM ordenes WHERE correo = '$correo' AND created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY) GROUP BY dia");
while ($r = mysqli_fetch_assoc($res_ord)) {
    $actividad_dias[$r['dia']] = ($actividad_dias[$r['dia']] ?? 0) + intval($r['cnt']);
}

$res_sub = mysqli_query($conexion, "SELECT DATE(created_at) as dia, COUNT(*) as cnt FROM suscripciones WHERE usuario_id = '$correo' AND created_at >= DATE_SUB(NOW(), INTERVAL 365 DAY) GROUP BY dia");
while ($r = mysqli_fetch_assoc($res_sub)) {
    $actividad_dias[$r['dia']] = ($actividad_dias[$r['dia']] ?? 0) + intval($r['cnt']);
}

$actividad_json = json_encode($actividad_dias);

$msg      = $_SESSION['profile_msg']      ?? '';
$msg_type = $_SESSION['profile_msg_type'] ?? '';
unset($_SESSION['profile_msg'], $_SESSION['profile_msg_type']);


// Valores por defecto
$nav_back_url  = $nav_back_url  ?? 'home.php';
$nav_back_text = $nav_back_text ?? 'volver';
$nav_base      = $nav_base      ?? '../../';

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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil — <?= htmlspecialchars($row['usuario']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://googleapis.com" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    :root {
        --bg-base:        #19191a;
        --bg-surface:     #212224;
        --bg-card:        #1d1d1d;
        --border:         #2e3038;
        --accent:         #ff7139;
        --accent-dark:    rgb(255, 72, 0);
        --text-primary:   #f0f1f3;
        --text-secondary: #8a8d96;
        --text-muted:     #555860;
        --green:          #3ecf8e;
        --font-display:   'Barlow', sans-serif;
        --font-body:      'Barlow', sans-serif;
        --radius-md:      10px;
        --radius-lg:      14px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { 

        /* background-image: url(../assets/images/bg7.jpg); */
        background-color: var(--pt-bg-base);
        
        color: var(--pt-text);
        font-family: var(--font-body);
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;


        background-repeat: no-repeat;
        background-position: center;
        background-attachment: fixed;
        background-size: cover;

    }
    .navbar {
        background-color: var(--pt-navbar, rgba(255,255,255,0.85)) !important;
        backdrop-filter: blur(8px);
        /* border-bottom: 1px solid var(--border); */
    }
        /* ── DROPDOWN ── */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        background: hsla(120, 2%, 10%, 0.84);
        color: #ff4314;
        border: 1.5px solid rgba(240, 180, 41, 0.3);
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
        border-color: #ff8843;
        background: rgba(240, 180, 41, 0.1);
    }

    .dropdown-content {
        display: none;
        position: absolute;
        left: 0;
        top: calc(100% + 6px);
        background: #16181c;
        border: 1px solid #2e3038;
        border-radius: 10px;
        min-width: 170px;
        z-index: 999;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        animation: dropFade 0.15s ease;
    }

    @keyframes dropFade {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dropdown-content a {
        display: block;
        padding: 0.6rem 1rem;
        color: #f0f1f3;
        font-size: 0.87rem;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .dropdown-content a:hover {
        background: rgba(240, 180, 41, 0.1);
        color: rgb(255, 103, 2);
        text-decoration: none;
    }

    .dropdown-content hr {
        border-color: #2e3038;
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


    .profile-layout {
        max-width: 860px;
        margin: 2rem auto;
        padding: 0 1.2rem 3rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }
    .pcard {
        background: var(--pt-boxitem);
        /* border: 1px solid var(--border); */
        border-radius: var(--radius-lg);
        padding: 1.4rem 1.6rem;
    }
    .resumencard {
        background: var(--pt-boxitem);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: var(--radius-lg);
        padding: 1.4rem 1.6rem;
    }

    .pcard-title {
        font-family: var(--font-display);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--pt-text);
        margin-bottom: 1.1rem;
    }

    /* PERFIL HEADER */
    .profile-header { display: flex; align-items: flex-start; gap: 1.5rem; color: var(--pt-text) }

    .avatar-img {
        width: 90px; height: 90px;
        border-radius: 50%; object-fit: cover;
        border: 2.5px solid var(--accent); flex-shrink: 0;
    }
    .avatar-initials {
        width: 90px; height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-size: 2rem; font-weight: 800;
        color: #0d0e10; border: 2.5px solid var(--accent); flex-shrink: 0;
    }

    .profile-info { flex: 1; }

    .profile-top-row {
        display: flex; align-items: center;
        justify-content: space-between;
        gap: 1rem; flex-wrap: wrap; margin-bottom: 0.3rem;
    }

    .profile-name {
        font-family: var(--font-display);
        font-size: 1.8rem; font-weight: 800;
        color: var(--pt-text); line-height: 1;
    }

    .btn-edit {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: var(--bg-card); border: 1.5px solid var(--border);
        color: var(--text-primary); border-radius: 8px;
        padding: 0.4rem 1rem;
        font-family: var(--font-display); font-size: 0.88rem;
        font-weight: 700; letter-spacing: 0.04em;
        text-decoration: none; transition: all 0.2s; white-space: nowrap;
    }
    .btn-edit:hover { border-color: var(--accent); color: var(--accent); text-decoration: none; }

    .profile-correo { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; }

    .profile-bio {
        font-size: 0.88rem; color: var(--text-secondary);
        margin-bottom: 0.6rem; /* font-style: italic; */
    }

    .profile-meta { display: flex; gap: 1.2rem; flex-wrap: wrap; }

    .meta-item {
        font-size: 0.78rem; color: var(--text-muted);
        display: flex; align-items: center; gap: 0.3rem;
    }

    /* ACTIVIDAD */
    .activity-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.8rem;
    }
    .activity-box {
        background: var(--pt-th2); /* border: 1px solid var(--border);*/
        border-radius: var(--radius-md); 
        padding: 1rem;
        text-align: center; transition: border-color 0.2s;
    }
    .activity-box:hover { border-color: var(--accent); }
    .activity-num {
        font-family: var(--font-display); font-size: 2rem;
        font-weight: 800; color: var(--accent); line-height: 1;
    }
    .activity-label { font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.3rem; }


     /* RESUMEN */
     .summary-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.8rem;
    }
    .summary-box {
        background: var(--pt-th2); 
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: var(--radius-md); padding: 1rem;
        text-align: center; transition: border-color 0.2s;
    }
    .summary-box:hover { border-color: var(--accent); }
    .summary-num {
        font-family: var(--font-display); font-size: 2rem;
        font-weight: 800; color: var(--accent); line-height: 1;
    }
    .summary-label { font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.3rem; }


    /* Alerts */
    .alert-custom { padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.88rem; }
    .alert-success { background: rgba(62,207,142,0.12); color: #3ecf8e; border: 1px solid rgba(62,207,142,0.3); }
    .alert-error   { background: rgba(224,82,82,0.12);  color: #e05252; border: 1px solid rgba(224,82,82,0.3); }

    /* CALENDARIO */
    .calendar-header {
        display: flex; justify-content: space-between;
        align-items: center; margin-bottom: 0.8rem;
    }
    .calendar-total {
        font-size: 0.82rem; color: var(--text-secondary);
    }
    .calendar-scroll {
        overflow-x: auto;
        padding-bottom: 0.4rem;
    }
    .calendar-grid {
        display: flex; gap: 3px;
        min-width: max-content;
    }
    .cal-col {
        display: flex; flex-direction: column; gap: 3px;
        background: var(--pt-th); 
    }
    .cal-cell {
        width: 13px; height: 13px;
        border-radius: 2px;
        background: var(--bg-card);
        border: 1px solid rgba(255,255,255,0.04);
        cursor: default;
        transition: transform 0.1s;
        position: relative;
    }
    .cal-cell:hover { transform: scale(1.3); z-index: 10; }
    .cal-cell.level-1 { background: rgba(240, 87, 41, 0.25); }
    .cal-cell.level-2 { background: rgba(240, 97, 41, 0.5);  }
    .cal-cell.level-3 { background: rgba(240, 97, 41, 0.75); }
    .cal-cell.level-4 { background: #f06129; }
    .cal-cell.empty   { background: transparent; border-color: transparent; cursor: default; }

    .cal-months {
        display: flex; gap: 3px;
        margin-bottom: 4px;
        min-width: max-content;
        padding-left: 0;
    }
    .cal-month-label {
        font-size: 0.68rem; color: var(--text-muted);
        white-space: nowrap;
    }

    .cal-legend {
        display: flex; align-items: center; gap: 0.4rem;
        margin-top: 0.6rem; justify-content: flex-end;
    }
    .cal-legend span { font-size: 0.72rem; color: var(--text-muted); }
    .cal-legend-cell {
        width: 12px; height: 12px; border-radius: 2px;
    }

    @media (max-width: 600px) {
        .profile-header  { flex-direction: column; }
        .activity-grid   { grid-template-columns: repeat(2, 1fr); }
        .profile-top-row { flex-direction: column; align-items: flex-start; }
    }
</style>
<body>

    <nav class="navbar navbar-dark navbar-expand-lg px-2">
        <a class="navbar-brand fw-bold" href="../../home.php" style="color: orange;">
            <img src="../assets/icons/icono.png" alt="Logo" style="width: 30px;">
        </a>
        
        <a href="../../home.php" class="btn" style="color: #ff4f2f;"><i class="fa-solid fa-circle-arrow-left"></i> Atras</a>
        <div class="ms-auto">
            <span style="background-color: hsla(120,2%,10%,0.84); padding: 5px 10px; border-radius: 5px; font-weight: bold; color: #fff;">
                <?= isset($_SESSION['usuario']) ? "Hola, " . htmlspecialchars($_SESSION['usuario']) : "Invitado" ?>
                <i class="bi bi-circle-fill" style="color: #51ff00;"></i> <!-- Indicador de usuario activo (PERO ES UN ICONO ESTATICO) -->
            </span>


            <!-- El desplegable a la derecha -->
            <div class="dropdown">
                <button class="dropbtn">Opciones</button>
                <div class="dropdown-content">
                    <a href="<?= $nav_base ?>contactos.php">Contactos</a>
                    <a href="<?= $nav_base ?>php/cerrar_sesion.php" class="cerrar-sesion">Cerrar sesión</a>
                </div>
            </div>  

            <!--<a href="../../php/cerrar_sesion.php" class="btn btn-sm btn-outline ms-3" style="background: #ff5e00f5;">Cerrar sesión</a> -->
        </div>
    </nav>

    <div class="profile-layout">

        <?php if ($msg): ?>
        <div class="alert-custom alert-<?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <!-- PERFIL -->
        <div class="pcard">
            <div class="pcard-title">Mi Perfil</div>
            <div class="profile-header">

                <?php if (!empty($row['profile_image']) && file_exists('../../uploads/' . $row['profile_image'])): ?>
                    <img src="../../uploads/<?= htmlspecialchars($row['profile_image']) ?>" class="avatar-img" alt="Foto">
                <?php else: ?>
                    <div class="avatar-initials"><?= strtoupper(substr($row['usuario'], 0, 1)) ?></div>
                <?php endif; ?>

                <div class="profile-info">
                    <div class="profile-top-row">
                        <div class="profile-name"><?= htmlspecialchars($row['usuario']) ?></div>
                        <a href="edit_profile.php" class="btn-edit">
                            <i class="bi bi-pencil-fill"></i> Editar perfil
                        </a>
                    </div>

                    <div class="profile-correo"><?= htmlspecialchars($row['correo']) ?></div>

                    <?php if (!empty($row['bio'])): ?>
                    <div class="profile-bio">"<?= htmlspecialchars($row['bio']) ?>"</div>
                    <?php endif; ?>

                    <div class="profile-meta">
                        <span class="meta-item"><i class="bi bi-person-badge"></i> ID: <?= htmlspecialchars($row['id']) ?></span>
                        <?php if (!empty($row['location'])): ?>
                        <span class="meta-item"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($row['location']) ?></span>
                        <?php endif; ?>
                        <span class="meta-item"><i class="bi bi-calendar3"></i> Unido: <?= htmlspecialchars(substr($row['created_at'] ?? 'N/A', 0, 10)) ?></span>
                    </div>
                </div>
            </div>
        </div>                        
        <!-- ACTIVIDAD -->
        <div class="pcard">
            <div class="pcard-title">Actividad</div>
            <div class="activity-grid">
                <div class="activity-box">
                    <div class="activity-num"><?= $total_ordenes_pago ?></div>
                    <div class="activity-label"><i class="bi bi-wallet-fill "></i> Total de ordenes</div>
                </div>
                <div class="activity-box">
                    <div class="activity-num"><?= $total_aprobadas ?></div>
                    <div class="activity-label"><i class="bi bi-cart-check-fill"></i> Total Pagos Aprobados</div>
                </div>
                <div class="activity-box">
                    <div class="activity-num"><?= $total_ordenes_rechazadas?></div>
                    <div class="activity-label"><i class="fa-solid fa-xmark "></i> Total Pagos Rechazados</div>
                </div>

            </div>
        </div>
        <!-- RESUMEN -->
        <div class="resumencard">
            <div class="pcard-title">Resumen</div>
            <div class="summary-grid">
                <div class="summary-box">
                    <div class="summary-num"><?= $total_ordenes ?></div>
                    <div class="summary-label">Total Recargas</div>
                </div>
                <div class="summary-box">
                    <div class="summary-num"><?= $total_subs ?></div>
                    <div class="summary-label">Suscripciones</div>
                </div>
                <div class="summary-box">
                    <div class="summary-num"><?= $total_recurrencias ?></div>
                    <div class="summary-label">Recurrencias</div>
                </div>
                <div class="summary-box">
                    <div class="summary-num"><?= $total_gw_ordenes ?></div>
                    <div class="summary-label">⚡ Gateway Pagos</div>
                </div>
                <div class="summary-box">
                    <div class="summary-num"><?= $total_gw_subs ?></div>
                    <div class="summary-label">⚡ Gateway Suscripciones</div>
                </div>
                <div class="summary-box">
                    <div class="summary-num"><?= $total_gw_suscription ?></div>
                    <div class="summary-label">⚡ Gateway Suscripción pura</div>
                </div>
            </div>
        </div>

        <!-- CALENDARIO DE ACTIVIDAD -->
        <div class="pcard">
            <div class="calendar-header">
                <div class="pcard-title" style="margin-bottom:0;">Historial de actividad</div>
                <div class="calendar-total" id="calTotal"></div>
            </div>
            <div class="calendar-scroll">
                <div class="cal-months" id="calMonths"></div>
                <div class="calendar-grid" id="calGrid"></div>
            </div>
            <div class="cal-legend">
                <span>Menos</span>
                <div class="cal-legend-cell" style="background:var(--bg-card); border:1px solid rgba(255,255,255,0.04);"></div>
                <div class="cal-legend-cell level-1"></div>
                <div class="cal-legend-cell level-2"></div>
                <div class="cal-legend-cell level-3"></div>
                <div class="cal-legend-cell level-4"></div>
                <span>Más</span>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

    <!-- Script para renderizar el calendario de actividad -->
    (function() {
        const data     = <?= $actividad_json ?>;
        const grid     = document.getElementById('calGrid');
        const months   = document.getElementById('calMonths');
        const calTotal = document.getElementById('calTotal');

        // Calcular total de actividades
        const total = Object.values(data).reduce((a, b) => a + b, 0);
        calTotal.textContent = total + ' actividad' + (total !== 1 ? 'es' : '') + ' en el último año';

        // Construir 52 semanas + días restantes
        const today    = new Date();
        const end      = new Date(today);
        // Empezar desde el domingo de hace ~52 semanas
        const start    = new Date(today);
        start.setDate(start.getDate() - 364);
        // Ajustar al domingo anterior
        start.setDate(start.getDate() - start.getDay());

        const DAYS   = ['D','L','M','X','J','V','S'];
        const MONTHS = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

        function toKey(d) {
            return d.getFullYear() + '-' +
                   String(d.getMonth()+1).padStart(2,'0') + '-' +
                   String(d.getDate()).padStart(2,'0');
        }

        function getLevel(cnt) {
            if (!cnt) return 0;
            if (cnt === 1) return 1;
            if (cnt === 2) return 2;
            if (cnt <= 4) return 3;
            return 4;
        }

        let monthLabels   = [];
        let lastMonth     = -1;
        let colIndex      = 0;

        const cur = new Date(start);
        while (cur <= end) {
            const col = document.createElement('div');
            col.className = 'cal-col';

            // Registrar etiqueta del mes para esta columna
            const m = cur.getMonth();
            if (m !== lastMonth) {
                monthLabels.push({ col: colIndex, label: MONTHS[m] });
                lastMonth = m;
            }

            for (let d = 0; d < 7; d++) {
                const cell = document.createElement('div');

                if (cur > end) {
                    cell.className = 'cal-cell empty';
                } else {
                    const key   = toKey(cur);
                    const cnt   = data[key] || 0;
                    const level = getLevel(cnt);
                    cell.className = 'cal-cell' + (level ? ' level-' + level : '');

                    // Tooltip
                    const label = cnt
                        ? cnt + ' actividad' + (cnt > 1 ? 'es' : '') + ' — ' + key
                        : 'Sin actividad — ' + key;
                    cell.title = label;
                }

                col.appendChild(cell);
                cur.setDate(cur.getDate() + 1);
            }

            grid.appendChild(col);
            colIndex++;
        }

        // Renderizar etiquetas de meses
        // Calculamos ancho por columna: 13px celda + 3px gap = 16px
        const colW = 16;
        let lastLabelRight = -999;
        monthLabels.forEach(({ col, label }) => {
            const left = col * colW;
            if (left - lastLabelRight < 28) return; // evitar solapamiento
            const span = document.createElement('div');
            span.className = 'cal-month-label';
            span.style.width = '28px';
            span.textContent = label;
            months.appendChild(span);
            lastLabelRight = left + 28;
        });

    })();
    </script>
</body>
</html>