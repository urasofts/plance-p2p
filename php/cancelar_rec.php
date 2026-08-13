<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$rec_id      = intval($_GET['id'] ?? 0);
$tabla       = $_GET['tabla'] ?? 'recurrencias';
$modo        = $_GET['modo'] ?? '';

$tablas_permitidas = ['recurrencias', 'suscription_rec'];
if (!in_array($tabla, $tablas_permitidas)) {
    $tabla = 'recurrencias';
}

if ($tabla === 'recurrencias') {
    $redirect = '../views/historial/reg-rec.php';
} elseif ($tabla === 'suscription_rec') {
    $redirect = '../views/historial/reg-sus.php?modo=wc-rec';
} else {
    $redirect = '../views/historial/historial.php';
}

if (!$rec_id) {
    header("Location: $redirect");
    exit();
}

$rec_id_safe   = mysqli_real_escape_string($conexion, $rec_id);
$correo_safe   = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

$rec = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT * FROM $tabla WHERE id = '$rec_id_safe' AND usuario_id = '$correo_safe'"
));

if (!$rec) {
    $_SESSION['cancel_msg'] = '❌ No se encontró el servicio o no tienes permisos para cancelarlo.';
    header("Location: $redirect");
    exit();
}

if (strtolower($rec['estado']) !== 'aprobada') {
    $_SESSION['cancel_msg'] = '❌ Solo se pueden cancelar servicios activos (aprobados).';
    header("Location: $redirect");
    exit();
}

mysqli_query($conexion,
    "UPDATE $tabla SET estado = 'cancelada' WHERE id = '$rec_id_safe'"
);

$nombre_servicio = '';
if ($tabla === 'recurrencias') {
    $nombre_servicio = 'Membresía';
} elseif ($tabla === 'suscription_rec') {
    $nombre_servicio = 'Suscripción de IA';
}

$_SESSION['cancel_msg'] = '🚫 ' . $nombre_servicio . ' #' . $rec_id . ' (' . $rec['servicio'] . ' — ' . $rec['plan'] . ') cancelada correctamente.';
header("Location: $redirect");
exit();
?>