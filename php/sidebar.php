<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plance - Configuración</title>
    <link rel="stylesheet" href="">                                 
</head>
<style>
    body {
        background-color: #0a0a0a;
        color: white;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .sidebar {
        width: 220px;
        background-color: #1a1a1a;
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        padding-top: 20px;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
    }

    .sidebar ul {
        list-style-type: none;
        padding: 0;
    }

    .sidebar ul li {
        padding: 15px 20px;
    }

    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
    }

    .sidebar ul li a:hover {
        background-color: #333333;
    }

    .content {
        margin-left: 220px;
        padding: 20px;
    }
</style>
<body>
    <div class="sidebar">
        <h2>Plance</h2>
        <ul>
            <li><a href="home.php">Inicio</a></li>
            <li><a href="config.php">Configuración</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </div>

    <div class="content">
        <!-- Aquí irá el contenido de la página de configuración -->
    </div>
</body>
</html>
