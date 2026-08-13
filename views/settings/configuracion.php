<?php
session_start();

$es_invitado = !isset($_SESSION['usuario']) && !empty($_SESSION['invitado']);

if (!isset($_SESSION['usuario']) && empty($_SESSION['invitado'])) {
    header("Location: ../../index.php");
    exit();
}

require_once __DIR__ . '/../../php/conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$correo = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

// ══════════════════════════════════════════
// Guardar tema seleccionado
// ══════════════════════════════════════════
$alerta = '';
$alerta_tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tema_post = ($_POST['tema'] ?? 'oscuro') === 'claro' ? 'claro' : 'oscuro';

    if ($es_invitado) {
        // Invitado: se guarda solo en la sesión, no en BD
        $_SESSION['tema_invitado'] = $tema_post;
        $ok = true;
    } else {
        $tema_safe = mysqli_real_escape_string($conexion, $tema_post);
        $existe = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT id FROM user_preferences WHERE usuario_correo = '$correo'"));
        if ($existe) {
            $ok = mysqli_query($conexion, "UPDATE user_preferences SET tema = '$tema_safe' WHERE usuario_correo = '$correo'");
        } else {
            $ok = mysqli_query($conexion, "INSERT INTO user_preferences (usuario_correo, tema) VALUES ('$correo', '$tema_safe')");
        }
    }

    $alerta = $ok ? '¡Tema actualizado!' : 'No se pudo guardar el tema.';
    $alerta_tipo = $ok ? 'success' : 'error';
}

// Cargar tema actual
$tema_actual = 'oscuro';
if ($es_invitado) {
    $tema_actual = $_SESSION['tema_invitado'] ?? 'oscuro';
} else {
    $pref = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT tema FROM user_preferences WHERE usuario_correo = '$correo'"));
    if ($pref && !empty($pref['tema'])) $tema_actual = $pref['tema'];
}

$theme_seccion = 'settings';
require_once __DIR__ . '/../../php/theme.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración | Plance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php require_once __DIR__ . '/../../php/theme.php'; ?>

    <style>

        :root {
            --bg-main: #161616;
            --bg-card: #0e0f0f;
            --border: rgba(255,255,255,0.08);
            --text-main: #f5f7fa;
            --text-soft: #98a2ad;
            --orange: rgb(255, 123, 0);
            --success: #49c774;
            --shadow: 0 12px 30px rgba(0,0,0,0.22);
            --radius-lg: 16px;
            --radius-md: 12px;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; background: var(--pt-bg-base); color: var(--pt-text); font-family: 'Barlow', sans-serif; }
        a { text-decoration: none; }

        .settings-page { max-width: 1280px; margin: 0 auto; padding: 28px 20px 40px; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 22px; }
        .back-link {
            display: inline-flex; align-items: center; gap: .55rem;
            color: #dbe3ea; font-weight: 700; font-size: .95rem;
            padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px;
            background: rgba(255,255,255,0.02); transition: .2s ease;
        }
        .back-link:hover { color: #fff; border-color: rgba(255,255,255,0.16); background: rgba(255,255,255,0.04); }
        .topbar-title { font-size: 1.6rem; font-weight: 800; margin: 0; }
        .topbar-subtitle { margin: 2px 0 0; color: var(--text-soft); font-size: .95rem; }

        .settings-layout { display: grid; grid-template-columns: 270px 1fr; gap: 20px; align-items: start; }
        .settings-main { display: grid; gap: 20px; }

        .card-panel {
            background: var(--pt-bg-card,); border-radius: var(--radius-lg);
            border: 1px solid var(-- border); box-shadow: var(--shadow);
            padding: 22px; opacity: 0;
        }
        .settings-card-title { font-size: 1.15rem; font-weight: 800; margin: 0 0 4px; }
        .settings-card-desc { color: var(--text-soft); font-size: .9rem; margin: 0 0 18px; } 

        .alert-box { padding: 12px 14px; border-radius: 10px; font-size: .88rem; margin-bottom: 16px; }
        .alert-box.success { background: rgba(73,199,116,0.12); color: var(--success); border: 1px solid rgba(73,199,116,0.3); }
        .alert-box.error   { background: rgba(224,82,82,0.12); color: #e05252; border: 1px solid rgba(224,82,82,0.3); }

        .theme-toggle-wrap { display: flex; gap: 10px; max-width: 420px; }
        .theme-opt {
            flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 16px; border-radius: 12px; border: 1.5px solid var(--border);
            background: var(--bg-base); color: var(--text-soft);
            cursor: pointer; font-weight: 700; font-size: 1rem; transition: all .2s;
        }
        .theme-opt:hover { border-color: var(--orange); }
        .theme-opt.active { border-color: var(--orange); background: rgba(211,155,23,0.12); color: var(--orange); }
        .theme-opt i { font-size: 1.2rem; }

        .form-actions { margin-top: 20px; }
        .btn-save {
            background: var(--orange); color: #0d0e10; border: none;
            padding: 11px 22px; border-radius: 10px; font-weight: 800;
            cursor: pointer; transition: .2s ease;
        }
        .btn-save:hover { opacity: .88; }

        @media (max-width: 900px) {
            .settings-layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="settings-page">
        <div class="topbar">
            <div>
                <h1 class="topbar-title">Configuración</h1>
                <p class="topbar-subtitle">Personaliza la apariencia de Plance.</p>
            </div>
            <a href="../../home.php" class="back-link">
                <i class="bi bi-arrow-left"></i>
                Atras
            </a>
        </div>

        <div class="settings-layout">
            <?php require_once __DIR__ . '/sidebar-settings.php'; ?>

            <main class="settings-main">
                <section class="card-panel">
                    <h2 class="settings-card-title">Apariencia</h2>
                    <p class="settings-card-desc">Elige el tema con el que quieres ver Plance.</p>

                    <?php if ($alerta): ?>
                        <div class="alert-box <?= $alerta_tipo ?>"><?= htmlspecialchars($alerta) ?></div>
                    <?php endif; ?>

                    <?php if ($es_invitado): ?>
                        <div class="alert-box" style="background:rgba(211,155,23,0.1);color:var(--yellow);border:1px solid rgba(211,155,23,0.3);">
                            Estás en modo invitado — tu tema se guardará mientras dure tu sesión actual.
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="temaForm">
                        <div class="theme-toggle-wrap">
                            <label class="theme-opt <?= $tema_actual === 'oscuro' ? 'active' : '' ?>">
                                <input type="radio" name="tema" value="oscuro" <?= $tema_actual === 'oscuro' ? 'checked' : '' ?> style="display:none;">
                                <i class="bi bi-moon-stars-fill"></i> Oscuro
                            </label>
                            <label class="theme-opt <?= $tema_actual === 'claro' ? 'active' : '' ?>">
                                <input type="radio" name="tema" value="claro" <?= $tema_actual === 'claro' ? 'checked' : '' ?> style="display:none;">
                                <i class="bi bi-sun-fill"></i> Claro
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">Guardar</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>

    <script>
        document.querySelectorAll('.theme-opt').forEach(function(opt) {
            opt.addEventListener('click', function() {
                document.querySelectorAll('.theme-opt').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Entrada rápida de los paneles (sin animejs, CSS puro para que cargue al instante)
        document.querySelectorAll('.card-panel').forEach(function(p, i) {
            p.style.transition = 'opacity .25s ease';
            setTimeout(() => p.style.opacity = '1', 20 + i * 40);
        });
    </script>
</body>
</html>