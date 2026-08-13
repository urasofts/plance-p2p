<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    header("Location: ../index.php");
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) die("Error de conexión: " . mysqli_connect_error());
}

$id   = intval($_GET['id']   ?? 0);
$tipo = $_GET['tipo'] ?? '';

$tipos_permitidos = ['orden', 'suscripcion', 'recurrencia'];
if (!$id || !in_array($tipo, $tipos_permitidos)) {
    header("Location: ../views/historial/reversos.php");
    exit();
}

$id_safe     = mysqli_real_escape_string($conexion, $id);
$correo_safe = mysqli_real_escape_string($conexion, $_SESSION['correo'] ?? '');

// ══════════════════════════════════════════
// Verificar que la transacción existe,
// pertenece al usuario y está aprobada
// ══════════════════════════════════════════
if ($tipo === 'orden') {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT * FROM ordenes WHERE id = '$id_safe' AND estado = 'aprobada'"
    ));
    $tabla  = 'ordenes';
    $nombre = $trx['producto'] ?? '';

} elseif ($tipo === 'suscripcion') {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT * FROM suscripciones WHERE id = '$id_safe' AND estado = 'aprobada' AND usuario_id = '$correo_safe'"
    ));
    $tabla  = 'suscripciones';
    $nombre = ($trx['plataforma'] ?? '') . ' — ' . ($trx['plan'] ?? '');

} else {
    $trx = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT * FROM recurrencias WHERE id = '$id_safe' AND estado = 'aprobada' AND usuario_id = '$correo_safe'"
    ));
    $tabla  = 'recurrencias';
    $nombre = ($trx['servicio'] ?? '') . ' — ' . ($trx['plan'] ?? '');
}

if (!$trx) {
    $_SESSION['reverso_msg']      = '❌ No se encontró la transacción o no tienes permisos para reversarla.';
    $_SESSION['reverso_msg_type'] = 'error';
    header("Location: ../views/historial/reversos.php");
    exit();
}

// ══════════════════════════════════════════
// Actualizar estado a "reversada" en BD
// ══════════════════════════════════════════
$resultado = mysqli_query($conexion,
    "UPDATE $tabla SET estado = 'reversada' WHERE id = '$id_safe'"
);

if ($resultado) {
    $_SESSION['reverso_msg']      = '✅ Transacción #' . $id . ' (' . $nombre . ') reversada correctamente. El dinero será devuelto al cliente.';
    $_SESSION['reverso_msg_type'] = 'success';
} else {
    $_SESSION['reverso_msg']      = '❌ Error al reversar la transacción: ' . mysqli_error($conexion);
    $_SESSION['reverso_msg_type'] = 'error';
}

header("Location: ../views/historial/reversos.php");
exit();
?>