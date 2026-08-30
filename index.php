<?php
    session_start();

    if(isset($_SESSION['usuario'])){
        header("Location: home.php");
        die();
    }
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placetopay</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    

    <link rel="stylesheet" href="assets/css/estilos.css?v=<?php echo filemtime(__DIR__ . '/assets/css/estilos.css'); ?>">
</head>
<body>  
    <main>

            <div class="contenedor__todo">
                
                <div class="caja__trasera">
                    <div class="caja__trasera-login">
                        <h3>¿Ya tienes una cuenta?</h3>
                        <p>Inicia sesión para entrar en la página</p>
                        <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                    </div>
                  
                    <div class="caja__trasera-register">
                        <h3>¿Aún no tienes una cuenta?</h3>
                        <p>Regístrate para que puedas iniciar sesión</p>
                        <button id="btn__registrarse">Regístrarse</button>
                    </div>
                </div>
                

                <!--Formulario de Login y registro-->
                <div class="contenedor__login-register">
                    <!--Login-->
                    <form action="php/login_user_be.php" method="POST" class="formulario__login">
                        <img src="assets/icons/icono.png" alt="Logo de EV" style="width:  50px; height: 50px; margin-bottom: 20px; align-self: center;">
                        <h2>Iniciar Sesión</h2>
                        <input type="text" placeholder="correo" name="correo">
                        <div style="position: relative;">
                        <input type="password" id="loginpassword" name="contraseña"
                                style="width: 100%; padding-right: 40px;" required placeholder="contraseña">

                            <i id="toggleIconLogin" class="bi bi-eye cursor-pointer"
                            style="position: absolute; right: 5px; top: 50%; margin: 10px auto; transform: translateY(-50%);
                                cursor: pointer; font-size: 18px; color: #686565;">
                            </i>
                            
                        </div><br>
                        <button style="height: 40px; width: 100%; border-radius: 8px;">Entrar</button>

                        <div style="display: flex; align-items: center; gap: 10px; margin: 14px 0 6px; width: 100%;">
                            <span style="flex: 1; height: 1px; background: #555;"></span>
                            <span style="color: #888; font-size: 0.8rem;">o</span>
                            <span style="flex: 1; height: 1px; background: #555;"></span>
                        </div>

                        <a href="php/entrar_invitado.php" class="btn" style="border: 1.5px solid rgb(255, 102, 0); color: rgb(255, 102, 0); height: 40px; width: 100%; border-radius: 8px;"><i class="bi bi-person"></i>  Continuar como invitado
                        </a>

                        <a href="welcome.php" style="color: #aaa; font-size: 0.85rem; text-decoration: none; align-self: center; margin-top: 14px;"><i class="bi bi-arrow-left"></i> Volver a la página de bienvenida
                        </a>

                    </form>

                    <!--Register-->
                    <form action="php/register_user_be.php" METHOD="POST" class="formulario__register">
                        <img src="assets/icons/icono.png" alt="Logo de EV" style="width:  50px; height: 50px; margin-bottom: 20px; align-self: center;">
                        <h2>Regístrarse</h2>
                        <input type="text" placeholder="identificacion" name="id" pattern="[0-9]+">
                        <input type="text" placeholder="nombre y apellido" name="nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}">
                        <input type="email" placeholder="correo " name="correo">
                        <input type="text" placeholder="usuario" name="usuario">
                        <!--el ojito no me quiere dar la ptm-->
                        <div style="position: relative;">
                            <input type="password" id="registerPassword" name="contraseña"
                                style="width: 100%; padding-right: 40px;" required placeholder="contraseña">

                            <i id="toggleIcon" class="bi bi-eye cursor-pointer"
                            style="position: absolute; right: 5px; top: 50%; margin: 10px auto; transform: translateY(-50%);
                                cursor: pointer; font-size: 18px; color: #686565;">
                            </i>
                        </div><br>

                        
                        <h6 style="color: #ffee00; text-align: center;">Sugerencia</h6>
                        <i class="bi bi-exclamation-circle" style="color: yellow;"></i>
                        <small style="color: white; font-size: 12px;">
                        Tu contraseña debe tener mínimo 8 caracteres, mayúscula, número y símbolo
                        </small>

                        <!-- BARRA DE FUERZA -->
                        <div style="height: 8px; width: 100%; background: #ddd; margin-top: 5px;">
                            <div id="strengthLevel" style="height: 100%; width: 0%; transition: 0.3s;"></div>
                        </div>

                        <small id="strengthText" style="color: white;"></small>
                        <button style="height: 40px; width: 100%;">Regístrarse</button>             
                    </form>
                    
                </div>
            </div>

    </main>


</body>
<script src="assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/validaciones.js"></script>
</html>
