<?php
session_start();

// Si ya hay un usuario logueado, ir a home
if (isset($_SESSION['usuario'])) {
    header("Location: ../home.php");
    exit();
}

// Marcar como invitado
$_SESSION['invitado'] = true;

header("Location: ../home.php");
exit();
?>
