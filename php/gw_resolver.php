<?php
session_start();

if (!isset($_SESSION["usuario"]) && empty($_SESSION["invitado"])) {
    http_response_code(403);
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) {
        http_response_code(500);
        exit();
    }
}

header('Content-Type: application/json');

// ══════════════════════════════════════════════════════════════
// gw_resolver.php — resolución diferida de transacciones "pendiente"
// simuladas en API Gateway (estados-gateway.php / estados-subs-gateway.php).
//
// No hay cron/worker en este entorno, así que la resolución es "perezosa":
// cada vez que la página de resultado consulta este endpoint, si ya se
// cumplió `resuelve_en`, aquí mismo se aplica `resuelve_a` a `estado`.
// ══════════════════════════════════════════════════════════════

$tablas_validas = [
    'orden'       => 'gateway_ordenes',
    'recurrencia' => 'gateway_recurrencias',
    'suscripcion' => 'gateway_suscription',
];

$tipo = $_GET['tipo'] ?? '';
$id   = (int) ($_GET['id'] ?? 0);

if (!isset($tablas_validas[$tipo]) || $id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'parámetros inválidos']);
    exit();
}

$tabla = $tablas_validas[$tipo];

$stmt = mysqli_prepare($conexion, "SELECT estado, resuelve_en, resuelve_a FROM {$tabla} WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'no encontrado']);
    exit();
}

// Si sigue pendiente y ya se cumplió la hora de resolución, resolvemos ahora
if ($row['estado'] === 'pendiente' && !empty($row['resuelve_en']) && !empty($row['resuelve_a'])
    && strtotime($row['resuelve_en']) <= time()) {

    $nuevo = $row['resuelve_a'];
    $upd = mysqli_prepare($conexion, "UPDATE {$tabla} SET estado = ?, resuelve_en = NULL, resuelve_a = NULL WHERE id = ? AND estado = 'pendiente'");
    mysqli_stmt_bind_param($upd, 'si', $nuevo, $id);
    mysqli_stmt_execute($upd);

    $row['estado'] = $nuevo;
}

echo json_encode(['estado' => $row['estado']]);
