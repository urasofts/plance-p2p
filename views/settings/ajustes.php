<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../php/conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
}

$es_invitado = !isset($_SESSION['usuario']);

$user_id = intval($_SESSION['user_id'] ?? 0);
$row = null;

if (!$es_invitado) {
    $row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM users WHERE id = '$user_id'"));

    if (!$row) {
        header("Location: ../../index.php");
        exit();
    }
} else {
    $row = [
        'id'            => 0,
        'usuario'       => 'Invitado',
        'correo'        => '',
        'location'      => '',
        'bio'           => '',
        'profile_image' => ''
    ];
}

$alerta = '';
$alerta_tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$es_invitado) {
    $bio = trim($_POST['bio'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $bio = mysqli_real_escape_string($conexion, $bio);
    $location = mysqli_real_escape_string($conexion, $location);

    $update = mysqli_query($conexion, "
        UPDATE users 
        SET bio = '$bio', location = '$location'
        WHERE id = '$user_id'
    ");

    if ($update) {
        $alerta = 'Configuración actualizada correctamente.';
        $alerta_tipo = 'success';

        $row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM users WHERE id = '$user_id'"));
    } else {
        $alerta = 'No se pudo actualizar la configuración.';
        $alerta_tipo = 'error';
    }
}

$correo = mysqli_real_escape_string($conexion, $row['correo']);

// ══════════════════════════════════════════
// APARIENCIA — tema claro/oscuro y fondos por sección
// ══════════════════════════════════════════
$alerta_apariencia = '';
$alerta_apariencia_tipo = '';

$secciones_bg = ['home', 'sesiones', 'historial', 'juegos', 'tiendas', 'perfil'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$es_invitado && ($_POST['form_type'] ?? '') === 'appearance') {
    $tema_post = ($_POST['tema'] ?? 'oscuro') === 'claro' ? 'claro' : 'oscuro';
    $tema_safe = mysqli_real_escape_string($conexion, $tema_post);

    $set_parts = ["tema = '$tema_safe'"];
    foreach ($secciones_bg as $sec) {
        $val = trim($_POST['bg_' . $sec] ?? '');
        $val_safe = mysqli_real_escape_string($conexion, $val);
        $set_parts[] = "bg_$sec = " . ($val === '' ? 'NULL' : "'$val_safe'");
    }
    $set_sql = implode(', ', $set_parts);

    $existe = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT id FROM user_preferences WHERE usuario_correo = '$correo'"));
    if ($existe) {
        $ok = mysqli_query($conexion, "UPDATE user_preferences SET $set_sql WHERE usuario_correo = '$correo'");
    } else {
        $ok = mysqli_query($conexion, "INSERT INTO user_preferences (usuario_correo, tema) VALUES ('$correo', '$tema_safe')");
        if ($ok) {
            mysqli_query($conexion, "UPDATE user_preferences SET $set_sql WHERE usuario_correo = '$correo'");
        }
    }

    $alerta_apariencia = $ok ? '¡Apariencia actualizada! Los cambios se verán al recargar.' : 'No se pudo guardar la apariencia.';
    $alerta_apariencia_tipo = $ok ? 'success' : 'error';
}

// Cargar preferencias actuales
$prefs = null;
if (!$es_invitado) {
    $prefs = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM user_preferences WHERE usuario_correo = '$correo'"));
}
$tema_actual = $prefs['tema'] ?? 'oscuro';

// Leer imágenes disponibles en assets/images
$carpeta_imgs = __DIR__ . '/../assets/images';
$imagenes_disponibles = [];
if (is_dir($carpeta_imgs)) {
    foreach (glob($carpeta_imgs . '/*.{jpg,jpeg,png,webp}', GLOB_BRACE) as $imgPath) {
        $imagenes_disponibles[] = basename($imgPath);
    }
    sort($imagenes_disponibles);
}

/*
|--------------------------------------------------------------------------
| ACTIVIDAD
|--------------------------------------------------------------------------
| OJO:
| Mantengo esta lógica usando únicamente nombres confirmados en tu archivo.
| Si luego me confirmas cómo se relaciona `ordenes` con el usuario,
| te lo ajusto para que todo sea por usuario logueado.
*/
$filtro_hoy = $es_invitado ? " WHERE DATE(created_at) = CURDATE()" : "";
$cond_hoy   = $es_invitado ? " AND DATE(created_at) = CURDATE()" : "";

$total_ordenes = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes" . $filtro_hoy))['total'] ?? 0;
$total_aprobadas = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes WHERE estado = 'aprobada'" . $cond_hoy))['total'] ?? 0;
$total_rechazadas = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes WHERE estado = 'rechazada'" . $cond_hoy))['total'] ?? 0;
$total_pendientes = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM ordenes WHERE estado = 'pendiente'" . $cond_hoy))['total'] ?? 0;

$avatar = '';
if (!empty($row['profile_image']) && file_exists('../uploads/' . $row['profile_image'])) {
    $avatar = '../uploads/' . htmlspecialchars($row['profile_image']);
}

$initial = strtoupper(substr($row['usuario'] ?? 'U', 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajustes | <?= htmlspecialchars($row['usuario'] ?? 'Invitado') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.min.js"></script>
    <?php require_once __DIR__ . '/../../php/theme.php'; ?>

    <style>
        :root {
            --bg-main: var(--pt-bg-base, #161616);
            --bg-sidebar:  var(--pt-bg-surface, #202122);
            --bg-card: var(--pt-bg-card, #0e0f0f);
            --bg-card-soft: var(--pt-bg-card, #252b31);
            --border: var(--pt-border, rgba(255,255,255,0.08));
            --text-main: var(--pt-text, #f5f7fa);
            --text-soft: var(--pt-text-sec, #98a2ad);
            --text-muted: var(--pt-text-sec, #6f7a85);
            --yellow: #d39b17;
            --blue-strong: #3f739a;
            --orange: #ff6a00;
            --success: #49c774;
            --shadow: 0 12px 30px rgba(0,0,0,0.22);
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--bg-main);
            color: var(--text-main);
            font-family: 'Barlow', sans-serif;
        }

        a {
            text-decoration: none;
        }

        .settings-page {
            max-width: 1280px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 22px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            color: var(--text-main);
            font-weight: 700;
            font-size: .95rem;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg-card);
            transition: .2s ease;
        }

        .back-link:hover {
            color: var(--yellow);
            border-color: var(--yellow);
            background: var(--bg-card);
        }

        .topbar-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0;
        }

        .topbar-subtitle {
            margin: 2px 0 0;
            color: var(--text-soft);
            font-size: .95rem;
        }

        .settings-layout {
            
            display: grid;
            grid-template-columns: 270px 1fr;
            gap: 20px;
            align-items: start;
        }

        .settings-main {
            display: grid;
            gap: 20px;
        }

        .overview-grid {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 20px;
        }

        .card-panel {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
            opacity: 0;
            color: var(--pt-text);
        }

        .card-header {
            background-color: var(--pt-hover);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .card-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--pt-text);
        }

        .card-arrow {
            color: var(--pt-text-sec);
            font-size: 1rem;
        }

        .profile-card-body {
            padding: 20px;
        }

        .profile-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .profile-user {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .avatar-img {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }

        .avatar-fallback {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0b429, #d88e11);
            color: #111;
            font-size: 1.45rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        .profile-name {
            margin: 0 0 4px;
            font-size: 1.2rem;
            font-weight: 800;
        }

        .profile-id {
            color: var(--text-soft);
            font-size: .88rem;
        }

        .btn-profile {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.12);
            color: var(--pt-text);
            font-weight: 700;
            background: var(--pt-hover);
            transition: .2s ease;
            white-space: nowrap;
        }

        .btn-profile:hover {
            border-color: rgba(255,255,255,0.22);
            background: rgba(255,255,255,0.05);
            color: var(--pt-text);
        }

        .profile-info-grid {
            display: grid;
            gap: 12px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 170px 1fr;
            gap: 12px;
            align-items: center;
        }

        .info-label {
            color: var(--text-soft);
            font-size: .92rem;
        }

        .info-value {
            color: var(--pt-text);
            font-size: .96rem;
            font-weight: 600;
            word-break: break-word;
        }
        .info-value verified-badge{
            color: var(--pt-text);
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .verified-badge i {
            color: #8fe26a;
        }

        .config-link {
            color: var(--orange);
            font-weight: 700;
        }

        .activity-card-body {
            padding: 20px;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .activity-item {
            text-align: center;
            padding: 14px 10px;
            border-radius: 12px;
            background: var(--bg-card-soft);
            border: 1px solid rgba(255,255,255,0.06);
        }

        .activity-number {
            font-size: 2rem;
            line-height: 1;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--pt-text) ;
        }

        .activity-label {
            font-size: .88rem;
            color: var(--pt-text-sec);
        }

        .settings-card {
            padding: 20px;
        }

        .settings-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 18px;
        }

        .settings-card-title {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .settings-card-desc {
            margin: 6px 0 0;
            color: var(--text-soft);
            font-size: .94rem;
        }

        .alert-box {
            margin-bottom: 16px;
            border-radius: 12px;
            padding: 12px 14px;
            font-weight: 600;
            font-size: .95rem;
        }

        .alert-box.success {
            background: rgba(73,199,116,0.12);
            border: 1px solid rgba(73,199,116,0.28);
            color: #8ee4a9;
        }

        .alert-box.error {
            background: rgba(255,106,0,0.10);
            border: 1px solid rgba(255,106,0,0.25);
            color: #ffb07a;
        }

        .settings-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: .92rem;
            font-weight: 700;
            color: #dbe4ec;
        }

        .form-control-custom {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.08);
            background: var(--bg-card-soft);
            color: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            outline: none;
            transition: .2s ease;
        }

        .form-control-custom:focus {
            border-color: var(--blue-strong);
            box-shadow: 0 0 0 3px rgba(63,115,154,0.18);
        }

        textarea.form-control-custom {
            min-height: 120px;
            resize: vertical;
        }

        .readonly-note {
            color: var(--text-muted);
            font-size: .82rem;
            margin-top: 6px;
        }

        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
        }

        .btn-save {
            border: none;
            background: #d39b17;
            color: #fff;
            font-weight: 800;
            border-radius: 12px;
            padding: 12px 18px;
            transition: .2s ease;
        }

        .btn-save:hover {
            background: #e0a61a;
        }

        @media (max-width: 1100px) {
            .overview-grid {
                grid-template-columns: 1fr;
            }

            .activity-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 900px) {
            .settings-layout {
                grid-template-columns: 1fr;
            }

            .settings-sidebar {
                position: static;
            }

            .settings-form {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .profile-top {
                flex-direction: column;
                align-items: stretch;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 4px;
            }

            .activity-grid {
                grid-template-columns: 1fr 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="settings-page">
        <div class="topbar">
            <div>
                <h1 class="topbar-title">Ajustes</h1>
                <p class="topbar-subtitle">Administra tu cuenta y la configuración principal del perfil.</p>
            </div>

            <a href="../../home.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Atras
            </a>
        </div>

        <div class="settings-layout">
            <?php $settings_base = ''; require_once __DIR__ . '/sidebar-settings.php'; ?>

            <main class="settings-main">
                <section id="mi-cuenta" class="overview-grid">
                    <article class="card-panel">
                        <div class="card-header">
                            <h2 class="card-title">Mi Perfil</h2>
                            <span class="card-arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>

                        <div class="profile-card-body">
                            <?php if ($es_invitado): ?>
                                <div style="text-align: center; padding: 26px 12px;">
                                    <div class="avatar-fallback" style="margin: 0 auto 16px;">I</div>
                                    <h3 class="profile-name">Invitado</h3>
                                    <p style="color: var(--text-soft); margin: 10px 0 22px; line-height: 1.6;">
                                        Estás navegando como invitado.<br>
                                        Inicia sesión o crea una cuenta para administrar tu perfil y guardar tu actividad.
                                    </p>
                                    <a href="../../index.php" class="btn-profile" style="background: var(--yellow); color: #000; border: none;">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                        Iniciar sesión / Crear cuenta
                                    </a>
                                </div>
                            <?php else: ?>
                            <div class="profile-top">
                                <div class="profile-user">
                                    <?php if ($avatar): ?>
                                        <img src="<?= $avatar ?>" alt="Avatar" class="avatar-img">
                                    <?php else: ?>
                                        <div class="avatar-fallback"><?= $initial ?></div>
                                    <?php endif; ?>
                                     <div>
                                        <h3 class="profile-name"><?= htmlspecialchars($row['usuario']) ?></h3>
                                        <div class="profile-id">ID de usuario: <?= htmlspecialchars($row['id']) ?></div>
                                    </div>
                                </div>

                                <a href="../profile/index.php" class="btn-profile">
                                    <i class="bi bi-person-gear"></i>
                                    Perfil de usuario
                                </a>
                            </div>

                            <div class="profile-info-grid">
                                <div class="info-row">
                                    <div class="info-label">Correo electrónico</div>
                                    <div class="info-value verified-badge">
                                        <span><?= htmlspecialchars($row['correo']) ?></span>
                                        <i class="bi bi-patch-check-fill"></i>
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">Teléfono</div>
                                    <div class="info-value">
                                        <span style="color: var(--text-soft);">No configurado</span>
                                        <a href="#configuracion" class="config-link" style="margin-left: 10px;">Agregar ahora</a>
                                    </div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">Ubicación</div>
                                    <div class="info-value">
                                        <?= !empty($row['location']) ? htmlspecialchars($row['location']) : '<span style="color: var(--text-soft);">No definida</span>' ?>
                                    </div>
                                </div>

                                <!-- <div class="info-row">
                                    <div class="info-label">Miembro desde</div>
                                    <div class="info-value">
                                        <?= htmlspecialchars(substr($row['created_at'] ?? 'N/A', 0, 10)) ?>
                                    </div>
                                </div> -->

                                <!--<?php if (!empty($row['bio'])): ?>
                                    <div class="info-row">
                                        <div class="info-label">Bio</div>
                                        <div class="info-value"><?= htmlspecialchars($row['bio']) ?></div>
                                    </div>
                                <?php endif; ?> -->
                            </div>
                            <?php endif; ?>
                        </div>
                    </article>

                    <article class="card-panel">
                        <div class="card-header">
                            <h2 class="card-title"><?= $es_invitado ? 'Transacciones de hoy' : 'Mi Actividad' ?></h2>
                            <span class="card-arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>

                        <div class="activity-card-body">
                            <div class="activity-grid">
                                <div class="activity-item">
                                    <div class="activity-number"><?= $total_pendientes ?></div>
                                    <div class="activity-label">Pendiente</div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-number"><?= $total_ordenes ?></div>
                                    <div class="activity-label">Total órdenes</div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-number"><?= $total_aprobadas ?></div>
                                    <div class="activity-label">Aprobado</div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-number"><?= $total_rechazadas ?></div>
                                    <div class="activity-label">Rechazado</div>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>


                <style>
                    .theme-toggle-wrap { display:flex; gap:10px; }
                    .theme-opt {
                        flex:1; display:flex; align-items:center; justify-content:center; gap:8px;
                        padding:12px; border-radius:10px; border:1.5px solid var(--border,#2e3038);
                        background:var(--bg-card,#1e2128); color:var(--text-soft,#8a8d96);
                        cursor:pointer; font-weight:600; transition:all .2s;
                    }
                    .theme-opt:hover { border-color:#f0b429; }
                    .theme-opt.active { border-color:#f0b429; background:rgba(240,180,41,0.1); color:#f0b429; }

                    .bg-gallery-grid {
                        display:grid; grid-template-columns:repeat(auto-fill, minmax(90px, 1fr));
                        gap:8px; max-height:280px; overflow-y:auto; padding:4px;
                    }
                    .bg-thumb {
                        aspect-ratio:1/1; border-radius:8px; background-size:cover; background-position:center;
                        border:2px solid transparent; cursor:pointer; transition:all .15s; position:relative;
                    }
                    .bg-thumb:hover { transform:scale(1.04); }
                    .bg-thumb.selected { border-color:#f0b429; box-shadow:0 0 0 1px #f0b429; }
                    .bg-thumb-none {
                        display:flex; flex-direction:column; align-items:center; justify-content:center;
                        gap:4px; background:var(--bg-card,#1e2128); color:var(--text-soft,#8a8d96); font-size:0.65rem;
                        border:2px dashed var(--border,#2e3038);
                    }
                    .bg-thumb-none.selected { border-color:#f0b429; color:#f0b429; }
                </style>

                <script>
                    function selectBg(section, filename) {
                        document.getElementById('bg_input_' + section).value = filename;
                        document.querySelectorAll('[data-section-panel="' + section + '"] .bg-thumb').forEach(function(t) {
                            t.classList.remove('selected');
                        });
                        event.currentTarget.classList.add('selected');
                    }

                    document.getElementById('bgSectionSelect')?.addEventListener('change', function() {
                        document.querySelectorAll('.bg-gallery-panel').forEach(function(p) {
                            p.style.display = (p.getAttribute('data-section-panel') === this.value) ? '' : 'none';
                        }.bind(this));
                    });

                    document.querySelectorAll('.theme-opt').forEach(function(opt) {
                        opt.addEventListener('click', function() {
                            document.querySelectorAll('.theme-opt').forEach(o => o.classList.remove('active'));
                            this.classList.add('active');
                            this.querySelector('input[type="radio"]').checked = true;
                        });
                    });
                </script>
        </div>
    </div>

    <script>
        const sidebarLinks = document.querySelectorAll('.sidebar-link');

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function () {
                sidebarLinks.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });

        window.addEventListener('scroll', () => {
            const cuenta = document.getElementById('mi-cuenta');
            const configuracion = document.getElementById('configuracion');

            const cuentaTop = cuenta.getBoundingClientRect().top;
            const configTop = configuracion.getBoundingClientRect().top;

            sidebarLinks.forEach(item => item.classList.remove('active'));

            if (configTop <= 140) {
                sidebarLinks[1].classList.add('active');
            } else {
                sidebarLinks[0].classList.add('active');
            }
        });
    </script>

    <script>
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.card-panel',
                opacity: [0, 1],
                translateY: [16, 0],
                delay: anime.stagger(100, { start: 100 }),
                duration: 550,
                easing: 'easeOutQuad'
            });
        }
    </script>
</body>
</html>