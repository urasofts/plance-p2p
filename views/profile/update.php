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

$user_id  = intval($_SESSION['user_id'] ?? 0);
$accion   = $_POST['accion']   ?? '';
$redirect = $_POST['redirect'] ?? 'index.php'; // a donde volver tras guardar

// ══════════════════════════════════════════
// FOTO DE PERFIL
// ══════════════════════════════════════════
if ($accion === 'foto') {

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['profile_msg']      = '❌ Error al subir la imagen.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    $archivo    = $_FILES['foto'];
    $extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $permitidos)) {
        $_SESSION['profile_msg']      = '❌ Solo se permiten imágenes JPG, PNG o WEBP.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    if ($archivo['size'] > 2 * 1024 * 1024) {
        $_SESSION['profile_msg']      = '❌ La imagen no puede superar 2MB.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    $nombre_archivo = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
    $destino        = '../uploads/' . $nombre_archivo;

    if (move_uploaded_file($archivo['tmp_name'], $destino)) {
        $nombre_safe = mysqli_real_escape_string($conexion, $nombre_archivo);
        mysqli_query($conexion, "UPDATE users SET profile_image = '$nombre_safe' WHERE id = '$user_id'");
        $_SESSION['profile_msg']      = '✅ Foto de perfil actualizada.';
        $_SESSION['profile_msg_type'] = 'success';
    } else {
        $_SESSION['profile_msg']      = '❌ No se pudo guardar la imagen.';
        $_SESSION['profile_msg_type'] = 'error';
    }

    header("Location: $redirect"); exit();
}

// ══════════════════════════════════════════
// NOMBRE DE USUARIO
// ══════════════════════════════════════════
if ($accion === 'usuario') {

    $nuevo = trim($_POST['usuario'] ?? '');

    if (empty($nuevo) || strlen($nuevo) < 3) {
        $_SESSION['profile_msg']      = '❌ El nombre debe tener al menos 3 caracteres.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    $check  = mysqli_real_escape_string($conexion, $nuevo);
    $existe = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT id FROM users WHERE usuario = '$check' AND id != '$user_id'"));

    if ($existe) {
        $_SESSION['profile_msg']      = '❌ Ese nombre de usuario ya está en uso.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    mysqli_query($conexion, "UPDATE users SET usuario = '$check' WHERE id = '$user_id'");
    $_SESSION['usuario']          = $nuevo;
    $_SESSION['profile_msg']      = '✅ Nombre de usuario actualizado.';
    $_SESSION['profile_msg_type'] = 'success';

    header("Location: $redirect"); exit();
}

// ══════════════════════════════════════════
// CORREO
// ══════════════════════════════════════════
if ($accion === 'correo') {

    $nuevo = trim($_POST['correo'] ?? '');

    if (!filter_var($nuevo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['profile_msg']      = '❌ El correo no tiene un formato válido.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    $check  = mysqli_real_escape_string($conexion, $nuevo);
    $existe = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT id FROM users WHERE correo = '$check' AND id != '$user_id'"));

    if ($existe) {
        $_SESSION['profile_msg']      = '❌ Ese correo ya está registrado.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    mysqli_query($conexion, "UPDATE users SET correo = '$check' WHERE id = '$user_id'");
    $_SESSION['correo']           = $nuevo;
    $_SESSION['profile_msg']      = '✅ Correo actualizado correctamente.';
    $_SESSION['profile_msg_type'] = 'success';

    header("Location: $redirect"); exit();
}

// ══════════════════════════════════════════
// BIOGRAFÍA
// ══════════════════════════════════════════
if ($accion === 'bio') {

    $bio  = trim($_POST['bio'] ?? '');
    $bio  = substr($bio, 0, 250); // máx 250 chars
    $safe = mysqli_real_escape_string($conexion, $bio);

    mysqli_query($conexion, "UPDATE users SET bio = '$safe' WHERE id = '$user_id'");
    $_SESSION['profile_msg']      = '✅ Biografía actualizada.';
    $_SESSION['profile_msg_type'] = 'success';

    header("Location: $redirect"); exit();
}

// ══════════════════════════════════════════
// UBICACIÓN
// ══════════════════════════════════════════
if ($accion === 'location') {

    $location = trim($_POST['location'] ?? '');
    $location = substr($location, 0, 100);
    $safe     = mysqli_real_escape_string($conexion, $location);

    mysqli_query($conexion, "UPDATE users SET location = '$safe' WHERE id = '$user_id'");
    $_SESSION['profile_msg']      = '✅ Ubicación actualizada.';
    $_SESSION['profile_msg_type'] = 'success';

    header("Location: $redirect"); exit();
}

// ══════════════════════════════════════════
// CONTRASEÑA
// ══════════════════════════════════════════
if ($accion === 'password') {

    $actual    = $_POST['password_actual']    ?? '';
    $nuevo     = $_POST['password_nuevo']     ?? '';
    $confirmar = $_POST['password_confirmar'] ?? '';

    $rowUser = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT contraseña FROM users WHERE id = '$user_id'"));

    if (!password_verify($actual, $rowUser['contraseña'])) {
        $_SESSION['profile_msg']      = '❌ La contraseña actual es incorrecta.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    if ($nuevo !== $confirmar) {
        $_SESSION['profile_msg']      = '❌ Las contraseñas nuevas no coinciden.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    if (strlen($nuevo) < 8 || !preg_match('/[A-Z]/', $nuevo) || !preg_match('/[0-9]/', $nuevo)) {
        $_SESSION['profile_msg']      = '❌ La contraseña debe tener mínimo 8 caracteres, una mayúscula y un número.';
        $_SESSION['profile_msg_type'] = 'error';
        header("Location: $redirect"); exit();
    }

    $hash      = password_hash($nuevo, PASSWORD_DEFAULT);
    $hash_safe = mysqli_real_escape_string($conexion, $hash);
    mysqli_query($conexion, "UPDATE users SET contraseña = '$hash_safe' WHERE id = '$user_id'");

    $_SESSION['profile_msg']      = '✅ Contraseña actualizada correctamente.';
    $_SESSION['profile_msg_type'] = 'success';

    header("Location: $redirect"); exit();
}

header("Location: $redirect"); exit();
?>