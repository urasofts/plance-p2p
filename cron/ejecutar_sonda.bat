@echo off
REM ejecutar_sonda.bat — lanzador para el Programador de tareas de Windows.
REM Ajusta la ruta de php.exe si tu XAMPP no esta instalado en C:\xampp.
"C:\xampp\php\php.exe" "%~dp0sonda.php"
