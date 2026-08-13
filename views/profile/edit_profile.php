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

$user_id = intval($_SESSION['user_id'] ?? 0);
$row     = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM users WHERE id = '$user_id'"));

if (!$row) {
    header("Location: ../../index.php");
    exit();
}

$msg      = $_SESSION['profile_msg']      ?? '';
$msg_type = $_SESSION['profile_msg_type'] ?? '';
unset($_SESSION['profile_msg'], $_SESSION['profile_msg_type']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil — <?= htmlspecialchars($row['usuario']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css?family=Barlow:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <?php require_once dirname(__DIR__, 2) . '/php/theme.php'; ?>
</head>
<style>
    :root {
        --bg-base:        #0d0e10;
        --bg-surface:     #16181c;
        --bg-card:        #1e2128;
        --border:         #2e3038;
        --accent:         #ff5e00;
        --accent-dark:    rgb(255, 102, 0);
        --text-primary:   #f0f1f3;
        --text-secondary: #8a8d96;
        --text-muted:     #555860;
        --font-display:   'Barlow', sans-serif;
        --font-body:      'Barlow', sans-serif;
        --radius-md:      10px;
        --radius-lg:      14px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        background-color: var(--pt-bg-base);
        color: var(--pt-text-primary);
        font-family: var(--pt-font-body);
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
    }
    .navbar {
        background-color: var(--pt-navbar, rgba(255,255,255,0.85)) !important;
        backdrop-filter: blur(8px);
        border-bottom: 1px solid var(--pt-border);
    }

    .edit-layout {
        max-width: 680px;
        margin: 2rem auto;
        padding: 0 1.2rem 3rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .page-title {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pcard {
        background: var(--pt-boxitem);
        /* border: 1px solid var(--border); */
        border-radius: var(--radius-lg);
        padding: 1.4rem 1.6rem;
    }

    .pcard-title {
        font-family: var(--font-display);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 1.1rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px solid var(--pt-border);
    }

    /* FOTO */
    .foto-section {
        display: flex;
        align-items: center;
        gap: 1.2rem;
    }

    .avatar-img {
        width: 72px; height: 72px;
        border-radius: 50%; object-fit: cover;
        border: 2.5px solid var(--accent); flex-shrink: 0;
    }
    .avatar-initials {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-display); font-size: 1.6rem; font-weight: 800;
        color: #0d0e10; border: 2.5px solid var(--accent); flex-shrink: 0;
    }

    .foto-info { flex: 1; }
    .foto-info p { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.4rem; }

    .btn{
        font-family : var(--font-display);
    }
    .btn-upload {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: var(--bg-card); border: 1.5px solid var(--border);
        color: var(--text-primary); border-radius: 8px;
        padding: 0.45rem 1rem;
        font-family: var(--font-display); font-size: 0.85rem;
        font-weight: 700; cursor: pointer; transition: all 0.2s;
    }
    .btn-upload:hover { border-color: var(--accent); color: var(--accent); }
    #fotoInput { display: none; }

    /* FORM */
    .form-group { margin-bottom: 1rem; }

    .form-label-custom {
        font-family: var(--font-display);
        font-size: 0.8rem; font-weight: 600;
        color: var(--pt-text); margin-bottom: 0.4rem; display: block;
    }

    .form-input {
        width: 100%;
        background: var(--pt-boxitem); border: 1.5px solid var(--pt-border);
        border-radius: 8px; color: var(--pt-text);
        font-family: var(--font-body); font-size: 0.9rem;
        padding: 0.5rem 0.85rem; outline: none; transition: border-color 0.2s;
    }
    .form-input:focus { border-color: var(--accent); }
    .form-input::placeholder { color: var(--pt-text); }

    textarea.form-input { resize: vertical; min-height: 90px; }

    .btn-save {
        background: var(--accent); color: #0d0e10;
        border: none; border-radius: 8px;
        font-family: var(--font-display); font-size: 0.95rem;
        font-weight: 800; letter-spacing: 0.05em;
        padding: 0.55rem 1.4rem; cursor: pointer;
        transition: background 0.2s, transform 0.15s;
        text-transform: uppercase;
    }
    .btn-save:hover { background: var(--accent-dark); transform: translateY(-1px); }

    .btn-back {
        display: inline-flex; align-items: center; gap: 0.4rem;
        background: transparent; border: 1.5px solid var(--border);
        color: var(--text-secondary); border-radius: 8px;
        padding: 0.55rem 1.2rem;
        font-family: var(--font-display); font-size: 0.9rem;
        font-weight: 700; text-decoration: none; transition: all 0.2s;
    }
    .btn-back:hover { border-color: var(--accent); color: var(--accent); text-decoration: none; }

    /* Alerts */
    .alert-custom { padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.88rem; }
    .alert-success { background: rgba(62,207,142,0.12); color: #3ecf8e; border: 1px solid rgba(62,207,142,0.3); }
    .alert-error   { background: rgba(224,82,82,0.12);  color: #e05252; border: 1px solid rgba(224,82,82,0.3); }
</style>
<body>

    <nav class="navbar navbar-dark navbar-expand-lg px-2 " style="font-family: var(--font-display);">
        <a class="navbar-brand fw-bold" href="../../home.php" style="color: orange;">
            <img src="../assets/icons/icono.png" alt="Logo" style="width: 30px;">
        </a>
        <a href="index.php" class="btn" style="color: rgba(255, 115, 0, 0.96); font-family: var(--font-display);"><i class="bi bi-backspace-fill"></i> Volver al perfil</a>
        <div class="ms-auto">
            <span style="background-color: hsla(120,2%,10%,0.84); padding: 5px 10px; border-radius: 5px; font-weight: bold; color: #fff;">
                <?= isset($_SESSION['usuario']) ? "Hola, " . htmlspecialchars($_SESSION['usuario']) : "Invitado" ?>
                <i class="bi bi-circle-fill" style="color: #51ff00; font-family: var(--font-display);"></i>
            </span>
            <a href="../../php/cerrar_sesion.php" class="btn btn-sm btn-outline ms-3" style="background: rgba(255, 174, 0, 0.96);">Cerrar sesión</a>
        </div>
    </nav>

    <div class="edit-layout">

        <div class="page-title"><i class="bi bi-pencil-square"></i> Editar Perfil</div>

        <?php if ($msg): ?>
        <div class="alert-custom alert-<?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <!-- FOTO DE PERFIL -->
        <div class="pcard">
            <div class="pcard-title">📸 Foto de perfil</div>
            <div class="foto-section">

                <?php if (!empty($row['profile_image']) && file_exists('../uploads/' . $row['profile_image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($row['profile_image']) ?>" class="avatar-img" alt="Foto">
                <?php else: ?>
                    <div class="avatar-initials"><?= strtoupper(substr($row['usuario'], 0, 1)) ?></div>
                <?php endif; ?>

                <div class="foto-info">
                    <form action="update.php" method="POST" enctype="multipart/form-data" id="fotoForm">
                        <input type="hidden" name="accion" value="foto">
                        <input type="hidden" name="redirect" value="edit_profile.php">
                        <input type="file" id="fotoInput" name="foto" accept="image/*" onchange="document.getElementById('fotoForm').submit()">
                        <label for="fotoInput" class="btn-upload">
                            <i class="bi bi-camera-fill"></i> Cambiar foto
                        </label>
                    </form>
                    <p>JPG, PNG o WEBP · Máx. 2MB</p>
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN PÚBLICA -->
        <div class="pcard">
            <div class="pcard-title">👤 Información pública</div>

            <form action="update.php" method="POST">
                <input type="hidden" name="accion" value="usuario">
                <input type="hidden" name="redirect" value="edit_profile.php">
                <div class="form-group">
                    <label class="form-label-custom">Nombre de usuario</label>
                    <input type="text" name="usuario" class="form-input"
                           value="<?= htmlspecialchars($row['usuario']) ?>"
                           required minlength="3" maxlength="32">
                </div>
                <button type="submit" class="btn-save">Guardar</button>
            </form>

            <div style="height:1px;background:var(--pt-border);margin:1.2rem 0;"></div>

            <form action="update.php" method="POST">
                <input type="hidden" name="accion" value="bio">
                <input type="hidden" name="redirect" value="edit_profile.php">
                <div class="form-group">
                    <label class="form-label-custom">Biografía</label>
                    <textarea name="bio" class="form-input"
                              placeholder="Cuéntanos algo sobre ti..."
                              maxlength="250"><?= htmlspecialchars($row['bio'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn-save">Guardar</button>
            </form>

            <div style="height:1px;background:var(--pt-border);margin:1.2rem 0;"></div>

            <form action="update.php" method="POST">
                <input type="hidden" name="accion" value="location">
                <input type="hidden" name="redirect" value="edit_profile.php">
                <div class="form-group">
                    <label class="form-label-custom">Ubicación</label>
                    <input type="text" name="location" class="form-input"
                           value="<?= htmlspecialchars($row['location'] ?? '') ?>"
                           placeholder="Ej: Medellín, Colombia" maxlength="100">
                </div>
                <button type="submit" class="btn-save">Guardar</button>
            </form>
        </div>

        <!-- INFORMACIÓN DE CUENTA -->
        <div class="pcard">
            <div class="pcard-title">🔐 Información de cuenta</div>

            <form action="update.php" method="POST">
                <input type="hidden" name="accion" value="correo">
                <input type="hidden" name="redirect" value="edit_profile.php">
                <div class="form-group">
                    <label class="form-label-custom">Correo electrónico</label>
                    <input type="email" name="correo" class="form-input"
                           value="<?= htmlspecialchars($row['correo']) ?>" required>
                </div>
                <button type="submit" class="btn-save">Guardar</button>
            </form>

            <div style="height:1px;background:var(--pt-border);margin:1.2rem 0;"></div>

            <form action="update.php" method="POST">
                <input type="hidden" name="accion" value="password">
                <input type="hidden" name="redirect" value="edit_profile.php">
                <div class="form-group">
                    <label class="form-label-custom">Contraseña actual</label>
                    <input type="password" name="password_actual" class="form-input" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label class="form-label-custom">Nueva contraseña</label>
                    <input type="password" name="password_nuevo" class="form-input" placeholder="Mínimo 8 caracteres" required minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label-custom">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmar" class="form-input" placeholder="Repite la nueva contraseña" required>
                </div>
                <button type="submit" class="btn-save">Cambiar contraseña</button>
            </form>
        </div>

        <a href="index.php" class="btn-back"><i class="bi bi-arrow-left"></i> Volver al perfil</a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>