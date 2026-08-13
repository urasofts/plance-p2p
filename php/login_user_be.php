<?php
session_start();
require_once __DIR__ . '/conexion_be.php';
require_once __DIR__ . '/../vendor/autoload.php';
// Sanitizar
$correo     = mysqli_real_escape_string($conexion, $_POST['correo']);
$contraseña = $_POST['contraseña'];

// Buscar usuario por correo
$query     = "SELECT * FROM users WHERE correo = '$correo'";
$resultado = mysqli_query($conexion, $query);

if ($row = mysqli_fetch_assoc($resultado)) {

    // Verificar contraseña
    if (password_verify($contraseña, $row['contraseña'])) {

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['correo']  = $row['correo']; // ← correo disponible en toda la sesión
        unset($_SESSION['invitado']); // Salir del modo invitado al iniciar sesión

        header("Location: ../home.php");
        exit();

    } else {
        echo "
            <script>
                alert('Contraseña incorrecta');
                window.location = '../index.php';
            </script>
        ";
        exit();
    }

} else {
    echo "
        <script>
            alert('Usuario no encontrado');
            window.location = '../index.php';
        </script>
    ";
    exit();
}
?>