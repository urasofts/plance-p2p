<?php
require_once __DIR__ . '/conexion_be.php';
/** @var mysqli $conexion */
if (!isset($conexion)) {
    die("Error: database connection not initialized.");
}




// Sanitizar datos
$id = mysqli_real_escape_string($conexion, $_POST['id']);
$nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
$correo = mysqli_real_escape_string($conexion, $_POST['correo']);
$usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
$contraseña = mysqli_real_escape_string($conexion, $_POST['contraseña']);

// Validar campos primero
if ($id == "" || $nombre == "" || $correo == "" || $usuario == "" || $contraseña == "") {
    echo "
        <script>
            alert('Por favor rellena todos los campos');
            window.location = '../index.php';
        </script>
    ";
    exit();
}

// Encriptar contraseña BIEN
$contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Verificar correo
$verificar_correo = mysqli_query($conexion, "SELECT * FROM users WHERE correo = '$correo'");
if (mysqli_num_rows($verificar_correo) > 0) {
    echo "
        <script>
            alert('Este correo ya esta registrado');
            window.location = '../index.php';
        </script>
    ";
    exit();
}
//por alguna razon en campo de la identificacion no me deja poner el pattern, asi que lo validamos aqui, solo numeros, guiones y parentesis, y que tenga entre 1 y 20 caracteres
if (!preg_match('/^[0-9()+]{1,20}$/', $id)) {
    echo "<script>
        alert('La identificación solo puede contener números, y debe tener entre 1 y 20 caracteres');
        window.location = '../index.php';
    </script>";
    exit();
}
//validemos el campo del nombre que solo permita letras y espacios, y que no deje ingresar numeros ni caracteres especiales
if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $nombre)) {
    echo "<script>
        alert('El nombre solo puede contener letras y espacios');
        window.location = '../index.php';
    </script>";
    exit();
}

// Verificar usuario
$verificar_usuario = mysqli_query($conexion, "SELECT * FROM users WHERE usuario = '$usuario'");
if (mysqli_num_rows($verificar_usuario) > 0) {
    echo "
        <script>
            alert('Este usuario ya esta registrado');
            window.location = '../index.php';
        </script>
    ";
    exit();
}

// mi­nimo 5 caracteres
if (strlen($nombre) < 5) {
    echo "
        <script>
            alert('El nombre debe tener al menos 5 caracteres');
            window.location = '../index.php';
        </script>
    ";
    exit();
}

// solo letras y espacios
if (!preg_match("/^[[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
    echo "
        <script>
            alert('El nombre solo puede contener letras y espacios');
            window.location = '../index.php';
        </script>
    ";
    exit();
}

// mi­nimo 2 palabras (nombre + apellido)
if (str_word_count($nombre) < 2) {
    echo "
        <script>
            alert('Debes ingresar nombre y apellido');
            window.location = '../index.php';
        </script>
    ";
    exit();
}

//validemos la contraseña tambien, mi­nimo 8 caracteres, al menos una letra mayuscula, una letra minuscula, un numero y un caracter especial
$contraseña = $_POST['contraseña'];

// mi­nimo 8 caracteres
if (strlen($contraseña) < 8) {
    echo "<script>
        alert('La contraseña debe tener mínimo 8 caracteres');
        window.location = '../index.php';
    </script>";
    exit();
}
// al menos una mayuscula
if (!preg_match('/[A-Z]/', $contraseña)) {
    echo "<script>
        alert('La contraseña debe tener al menos una letra mayúscula');
        window.location = '../index.php';
    </script>";
    exit();
}
// al menos una minúscula
if (!preg_match('/[a-z]/', $contraseña)) {
    echo "<script>
        alert('La contraseña debe tener al menos una letra minúscula');
        window.location = '../index.php';
    </script>";
    exit();
}
// al menos un número
if (!preg_match('/[0-9]/', $contraseña)) {
    echo "<script>
        alert('La contraseña debe tener al menos un número');
        window.location = '../index.php';
    </script>";
    exit();
}

// Insertar
$query = "INSERT INTO users(id, nombre, correo, usuario, contraseña) 
VALUES('$id', '$nombre', '$correo', '$usuario', '$contraseña_hash')";

$ejecutar = mysqli_query($conexion, $query);

if ($ejecutar) {
    echo "
        <script>
            alert('Usuario registrado exitosamente');
            window.location = '../index.php';
        </script>
    ";
} else {
    echo "
        <script>
            alert('Error al registrar usuario');
            window.location = '../index.php';
        </script>
    ";
}

mysqli_close($conexion);

// quitar espacios extra
$nombre = trim($nombre);





// al menos un caracter especial
if (!preg_match('/[\W]/', $contraseña)) {
    echo "<script>
        alert('La contraseña debe tener al menos un caracter especial');
        window.location = '../index.php';
    </script>";
    exit();
}
























?>