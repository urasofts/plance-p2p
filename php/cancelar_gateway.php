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

$rec_id = intval($_GET['id'] ?? 0);
$tabla  = $_GET['tabla'] ?? 'gateway_recurrencias';
$modo   = $_GET['modo'] ?? '';

$tablas_permitidas = ['gateway_recurrencias', 'gateway_suscription'];
if (!in_array($tabla, $tablas_permitidas)) {
    $tabla = 'gateway_recurrencias';
}

$modo_redirect = $tabla === 'gateway_recurrencias' ? 'gw-sub' : 'gw-pura';
$redirect = '../views/historial/reg-sus.php?modo=' . $modo_redirect;

if (!$rec_id) {
    header("Location: $redirect");
    exit();
}

$rec_id_safe = mysqli_real_escape_string($conexion, $rec_id);
$correo_safe = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

$rec = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT * FROM $tabla WHERE id = '$rec_id_safe' AND correo = '$correo_safe'"
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

$nombre_servicio = $tabla === 'gateway_recurrencias' ? 'Suscripción (API Gateway)' : 'Suscripción con tarjeta guardada (API Gateway)';

$_SESSION['cancel_msg'] = '🚫 ' . $nombre_servicio . ' #' . $rec_id . ' (' . $rec['servicio'] . ' — ' . $rec['plan'] . ') cancelada correctamente.';
header("Location: $redirect");
exit();
