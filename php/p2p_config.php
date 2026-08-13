<?php
require_once __DIR__ . '/env.php';

// p2p_config.php — Credenciales PlaceToPay
// Se leen de .env; si no existe, cae en los valores de sandbox
// que ya se usaban (son de prueba, no de producción).

define('P2P_CREDENCIALES', [
    'principal' => [
        'login'     => env_get('P2P_LOGIN_PRINCIPAL', '2d9eaf1e662518756a3d78806543af5b'),
        'secretKey' => env_get('P2P_SECRET_PRINCIPAL', '3YC5brb5eAR4xBGQ'),
    ],
    'preautorizacion' => [
        'login'     => env_get('P2P_LOGIN_PREAUTORIZACION', '62f3eeeb7655485cbf65b306b4585dfd'),
        'secretKey' => env_get('P2P_SECRET_PREAUTORIZACION', 'K8zGmmoark19y2ey'),
    ],
    'dispersion' => [
        'login'     => env_get('P2P_LOGIN_DISPERSION', '8ddd7ab3d5a270608832d033849a1a8d'),
        'secretKey' => env_get('P2P_SECRET_DISPERSION', 'U7rCf9me0vqk7755'),
    ],
]);

function p2p_credenciales(): array {
    return P2P_CREDENCIALES;
}

// URL publica (ngrok) donde PlaceToPay envia las notificaciones
define('P2P_NOTIFY_URL', env_get('P2P_NOTIFY_URL', 'https://doorman-situated-delivery.ngrok-free.dev/plance/php/notify.php'));

// Devuelve el juego de credenciales segun el prefijo de la referencia
function p2p_credenciales_por_referencia(string $reference): array {
    $creds = p2p_credenciales();
    if (strpos($reference, 'PRE-') === 0)  return $creds['preautorizacion'];
    if (strpos($reference, 'DISP-') === 0) return $creds['dispersion'];
    return $creds['principal'];
}
