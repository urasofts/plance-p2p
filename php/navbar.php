<?php

/**
 * navbar.php — Navbar reutilizable
 * 
 * Variables que puedes definir ANTES de incluir este archivo:
 * $nav_back_url  → URL del botón "Volver"        (default: home.php)
 * $nav_back_text → Texto del botón "Volver"       (default: "Volver")
 * $nav_base      → Ruta base hacia la raíz        (default: "../")
 *
 * Ejemplo de uso en cualquier página:
 *   $nav_back_url  = "../home.php";
 *   $nav_back_text = "Volver";
 *   $nav_base      = "../";
 *   require_once '../php/navbar.php';
 */

// Valores por defecto
$nav_back_url  = $nav_back_url  ?? 'home.php';
$nav_back_text = $nav_back_text ?? 'volver';
$nav_base      = $nav_base      ?? '../';

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

// Modo invitado: inicial "I"
if (!isset($_SESSION['usuario'])) {
    $nav_initials = 'I';
}
?>

<style>
    .navbar {
        background-color: var(--pt-navbar, #2e2e2ea9) !important;
        backdrop-filter: blur(8px);
    }


    /* ── NAVBAR AVATAR ── */
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
        background: linear-gradient(135deg, #f04729, #c96910);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.8rem;
        color: #0d0e10;
        border: 2px solid #ff702d;
        transition: border-color 0.2s, transform 0.2s;
        flex-shrink: 0;
    }
    .nav-avatar-initials:hover { border-color: #fff; transform: scale(1.08); }

    .nav-username {
        background-color: var(--pt-bg-card, hsla(120, 2%, 10%, 0.84));
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        color: var(--pt-text, #ffffff);
    }

    /* ── DROPDOWN ── */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        background: hsla(120, 2%, 10%, 0.84);
        color: #f07229;
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
        border-color: #f0b429;
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
        color: #f06829;
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



</style>

<nav class="navbar navbar-dark navbar-expand-lg px-2">
    <a class="navbar-brand fw-bold" href="<?= $nav_base ?>home.php" style="color: orange;">
        <img src="<?= $nav_base ?>assets/icons/icono.png" alt="Logo" style="width: 30px;">
    </a>

 
  
    <!-- BOTON DE RETROSESO -->
    <a href="<?= htmlspecialchars($nav_back_url) ?>" class="btn" style="color: black; color: #f06129;">
        <i class="fa-solid fa-circle-arrow-left fs-6"></i> <?= htmlspecialchars($nav_back_text) ?>
    </a>  <!---->
    <div class="ms-auto d-flex align-items-center gap-2">

        <!-- Nombre del usuario -->
        <span class="nav-username">
            <?= isset($_SESSION['usuario']) ? "Hola, " . htmlspecialchars($_SESSION['usuario']) : "Invitado" ?>
        </span>

        <!-- Avatar clickeable → perfil -->
        <a href="<?= $nav_base ?>views/profile/index.php" class="nav-avatar-wrap" title="Mi perfil">
            <?php if ($nav_avatar): ?>
                <img src="<?= $nav_avatar ?>" class="nav-avatar-img" alt="Perfil">
            <?php else: ?>
                <div class="nav-avatar-initials"><?= $nav_initials ?: 'U' ?></div>
            <?php endif; ?>
        </a>
         <!-- El desplegable a la derecha -->
        <div class="dropdown">
            <button class="dropbtn">Opciones ▼</button>
            <div class="dropdown-content">
                <a href="<?= $nav_base ?>views/profile/index.php">Perfil</a>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="<?= $nav_base ?>contactos.php">Contactos</a>
                    <hr>
                    <a href="<?= $nav_base ?>php/cerrar_sesion.php" class="cerrar-sesion">Cerrar sesión</a>
                <?php else: ?>
                    <hr>
                    <a href="<?= $nav_base ?>index.php" style="color: #ff9544 !important;">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </div> 
       <!-- <a href="<?= $nav_base ?>php/cerrar_sesion.php" class="btn btn-sm btn-outline ms-1" style="background: #ff5e00f5;">
            Cerrar sesión
        </a> -->
    </div>
</nav>