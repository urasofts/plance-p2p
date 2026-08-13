<?php

    session_start();

    //lo que hace esta página es destruir la sesión del usuario, para que así el usuario no pueda acceder a la página principal sin iniciar sesión, y luego redirigir al usuario a la página de inicio de sesión

    session_destroy();

    header("location: ../index.php");
    

    //necesito otro para la pagina de juegos, para que el usuario no pueda acceder a la página de juegos sin iniciar sesión, y luego redirigir al usuario a la página de inicio de sesión


    exit();

?>