<?php
// ══════════════════════════════════════════
// env.php — Cargador simple de variables de entorno (.env)
// Lee el archivo .env en la raíz del proyecto UNA sola vez
// y expone env_get('CLAVE', 'valor_por_defecto') para usarlo
// en cualquier archivo del proyecto.
// ══════════════════════════════════════════

if (!function_exists('env_get')) {

    function plance_cargar_env(): array {
        static $valores = null;

        if ($valores !== null) {
            return $valores;
        }

        $valores = [];
        $rutaEnv = dirname(__DIR__) . '/.env';

        if (is_file($rutaEnv)) {
            $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lineas as $linea) {
                $linea = trim($linea);
                if ($linea === '' || str_starts_with($linea, '#')) {
                    continue;
                }
                if (!str_contains($linea, '=')) {
                    continue;
                }
                [$clave, $valor] = explode('=', $linea, 2);
                $clave = trim($clave);
                $valor = trim($valor);

                // Quita comillas envolventes si las hay: "algo" o 'algo'
                if (strlen($valor) >= 2) {
                    $primero = $valor[0];
                    $ultimo  = $valor[strlen($valor) - 1];
                    if (($primero === '"' && $ultimo === '"') || ($primero === "'" && $ultimo === "'")) {
                        $valor = substr($valor, 1, -1);
                    }
                }

                $valores[$clave] = $valor;
            }
        }

        return $valores;
    }

    function env_get(string $clave, $default = null) {
        $valores = plance_cargar_env();
        return $valores[$clave] ?? $default;
    }

    // URL base del proyecto (sin / al final) para armar returnUrl, redirects, etc.
    // Por defecto asume php -S localhost:8000. Cámbialo en tu .env con APP_BASE_URL
    // si usas otro puerto, otro dominio, o subes esto a un servidor real.
    function app_base_url(): string {
        return rtrim(env_get('APP_BASE_URL', 'http://localhost:8000'), '/');
    }
}
