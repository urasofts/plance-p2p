<?php
// ══════════════════════════════════════════
// theme.php — Motor de tema (claro/oscuro)
// Se incluye en el <head> de cada página, justo antes de </head>
// Requiere: session_start() ya ejecutado (y $conexion si ya existe)
// ══════════════════════════════════════════

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($conexion)) {
    require_once __DIR__ . '/conexion_be.php';
    if (!isset($conexion)) {
        $conexion = plance_db_connect();
    }
}

// ── Cargar preferencia de tema del usuario (o del invitado) ──
$theme_tema = 'oscuro';
$correo_actual = $_SESSION['correo'] ?? null;

if ($correo_actual && $conexion) {
    $correo_safe = mysqli_real_escape_string($conexion, $correo_actual);
    $pref = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT tema FROM user_preferences WHERE usuario_correo = '$correo_safe'"));
    if ($pref && !empty($pref['tema'])) {
        $theme_tema = $pref['tema'];
    }
} elseif (!empty($_SESSION['tema_invitado'])) {
    // Invitado sin cuenta: el tema se guarda solo en su sesión
    $theme_tema = $_SESSION['tema_invitado'];
}
// ── Definir una fuente de texto ──




// ── Definir paleta según tema ──
$es_claro = ($theme_tema === 'claro');

$c_bg_base    = $es_claro ? '#f5f5f7' : '#0d0e10';
$c_bg_secondary = $es_claro ? '#f5f5f7' : '#000000';
$c_bg_surface = $es_claro ? '#ffffff' : '#16181c';
$c_bg_card    = $es_claro ? '#f0f0f2' : '#1e2128';
$c_bg_card2    = $es_claro ? '#f1f1f1fa' : '#2b2b2ba9';
$c_border     = $es_claro ?  '#f3f3f3;' : '#2e3038';
$c_text       = $es_claro ? '#111114' : '#f0f1f3';
$c_text_sec   = $es_claro ? '#5a5a63' : '#8a8d96';
$c_navbar     = $es_claro ? 'rgba(255,255,255,0.85)' : '#2b2b2ba9';
$c_box_shadow  = $es_claro ? '0 2px 4px rgba(0,0,0,0.1)' : '0 2px 4px rgba(0,0,0,0.5)';
$c_hover      = $es_claro ?  '#f3f3f3;' : '#2e3038';
$c_paint       = $es_claro ? 'rgb(238, 238, 238)' : '#0a0a0a';
$c_th       = $es_claro ? 'hsla(0, 100%, 100%, 0.84)' : '#0a0a0a';
$c_th2       = $es_claro ? 'rgba(223, 223, 223, 0.72)' : '#0a0a0a';
$c_boxitem       = $es_claro ? 'rgb(255, 253, 253)' : '#222224';   
$c_lightbox       = $es_claro ? 'rgba(255, 255, 255, 0.85)' : 'rgba(14,14,14,0.8)';
$c_recivos       = $es_claro ? 'rgb(255, 253, 253)' : '#222224';
$c_dropdown       = $es_claro ? 'rgba(247, 246, 246, 0.85)' : 'rgba(14,14,14,0.8)';
$c_bg_gradiente    = $es_claro ? '#f5f5f7' : '#0d0e10'; //como seria esto con gradiente? 
?>
<style id="plance-theme-engine">
    :root {
        --pt-bg-base:    <?= $c_bg_base ?>;
        --pt-bg-secondary: <?= $c_bg_secondary ?>;
        --pt-bg-surface: <?= $c_bg_surface ?>;
        --pt-bg-card:    <?= $c_bg_card ?>;
        --pt-bg-card2:   <?= $c_bg_card2 ?>;
        --pt-border:     <?= $c_border ?>;
        --pt-text:       <?= $c_text ?>;
        --pt-text-sec:   <?= $c_text_sec ?>;
        --pt-navbar:     <?= $c_navbar ?>;
        --pt-paint:      <?= $c_paint ?>; 
        --pt-box-shadow:  <?= $c_box_shadow ?>;
        --pt-th: <?= $c_th ?>;
        --pt-th2: <?= $c_th2 ?>;
        --pt-boxitem: <?= $c_boxitem ?>;
        --pt-hover: <?= $c_hover ?>;
        --pt-recivos: <?= $c_recivos ?>;
        --pt-lightbox: <?= $c_lightbox ?>;
        --pt-dropdown: <?= $c_dropdown ?>;
    }
    body {
        background-color: var(--pt-bg-base) !important;
        color: var(--pt-text) !important;
    }
    .navbar { background-color: var(--pt-navbar) !important; }
</style>