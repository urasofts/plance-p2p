<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexion: " . mysqli_connect_error());
}

// Recibir parametros
$tabla      = $_GET['tabla']      ?? '';
$id         = intval($_GET['id']  ?? 0);
$request_id = $_GET['request_id'] ?? '';
$redirect   = $_GET['redirect']   ?? '../views/historial/historial.php';

// Validar tabla permitida (seguridad)
$tablas_permitidas = ['ordenes', 'suscripciones', 'recurrencias', 'suscription_rec', 'suscription', 'gateway_recurrencias', 'gateway_suscription', 'gateway_ordenes', 'gateway_abonos', 'reservaciones', 'dispersiones'];
if (!in_array($tabla, $tablas_permitidas) || !$id || !$request_id) {
    header("Location: $redirect");
    exit();
}

// ==========================================
// Consultar PlaceToPay y actualizar la BD
// (misma logica que usa la sonda/cronjob en cron/sonda.php)
// ==========================================
require_once 'p2p_sonda_core.php';

// gateway_abonos se verifica a nivel de abono (recalcula la orden padre después)
$resultado = $tabla === 'gateway_abonos'
    ? p2p_verificar_abono($conexion, $id, $request_id)
    : p2p_consultar_y_actualizar($conexion, $tabla, $id, $request_id);

$etiqueta = $tabla === 'gateway_abonos' ? "Abono #$id" : "Orden #$id";
$_SESSION['verify_msg'] = $resultado['ok']
    ? "$etiqueta actualizado a: " . strtoupper($resultado['estado_nuevo'])
    : "No se pudo verificar el estado de $etiqueta. Estado sin cambios.";

header("Location: $redirect");
exit();
?>