<?php
require_once __DIR__ . '/env.php';

// ══════════════════════════════════════════
// conexion_be.php — Conexión central a MySQL
// Ya NO usa credenciales fijas: las toma de .env
// (crea tu .env local a partir de .env.example)
// Funciona igual con XAMPP, MySQL standalone, o
// cualquier servidor MySQL — solo cambia el .env.
// ══════════════════════════════════════════

if (!function_exists('plance_db_connect')) {
    function plance_db_connect() {
        $host = env_get('DB_HOST', 'localhost');
        $port = (int) env_get('DB_PORT', 3306);
        $user = env_get('DB_USER', 'root');
        $pass = env_get('DB_PASS', '');
        $name = env_get('DB_NAME', 'place_bsd');

        $conexion = mysqli_connect($host, $user, $pass, $name, $port);

        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        return $conexion;
    }
}

$conexion = plance_db_connect();
