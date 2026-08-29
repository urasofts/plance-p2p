<?php
// notify.php - Endpoint para recibir notificaciones (webhooks) de PlaceToPay
// Docs: https://docs.placetopay.dev/checkout/notification
//       https://docs.placetopay.dev/gateway/webhooks/

require_once 'p2p_config.php';

// Log de todo lo que llega (metodo y body) para depurar
$raw_body = file_get_contents('php://input');
file_put_contents(
    __DIR__ . '/notify_debug.log',
    date('Y-m-d H:i:s') . ' | ' . $_SERVER['REQUEST_METHOD'] . ' | ' . ($_SERVER['REMOTE_ADDR'] ?? '?') . "\n" . $raw_body . "\n\n",
    FILE_APPEND
);

// Responder siempre JSON
header('Content-Type: application/json');

// GET/HEAD: health-check (evita el 405 al abrir la URL en navegador o pings de validacion)
if (in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'], true)) {
    http_response_code(200);
    echo json_encode(['status' => 'OK', 'message' => 'Endpoint de notificaciones activo']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'ERROR', 'message' => 'Metodo no permitido']);
    exit();
}

$data = json_decode($raw_body, true);
if (empty($data) || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'Sin datos']);
    exit();
}

require_once 'conexion_be.php';
if (!isset($conexion)) {
    $conexion = plance_db_connect();
    if (!$conexion) {
        http_response_code(500);
        echo json_encode(['status' => 'ERROR', 'message' => 'Error de conexion BD']);
        exit();
    }
}

// ==============================================================
// Webhook de devoluciones ACH (chargeback.created)
// Firma: header X-Signature = HMAC-SHA256(body, tranKey)
// ==============================================================
if (($data['type'] ?? '') === 'chargeback.created') {
    $headers  = function_exists('getallheaders') ? getallheaders() : [];
    $recibida = $headers['X-Signature'] ?? $headers['x-signature'] ?? '';

    $valida = false;
    foreach (P2P_CREDENCIALES as $cred) {
        if (hash_equals(hash_hmac('sha256', $raw_body, $cred['secretKey']), (string)$recibida)) {
            $valida = true;
            break;
        }
    }

    if (!$valida) {
        http_response_code(403);
        echo json_encode(['status' => 'ERROR', 'message' => 'Firma invalida']);
        exit();
    }

    // Por ahora solo se registra el chargeback en el log
    file_put_contents(__DIR__ . '/notify_debug.log', date('Y-m-d H:i:s') . " | CHARGEBACK registrado\n\n", FILE_APPEND);
    http_response_code(200);
    echo json_encode(['status' => 'OK']);
    exit();
}

// ==============================================================
// Notificacion de Checkout / recurrencias
// Body: { status: {status, reason, message, date}, requestId, reference, signature }
// Recurrentes: sin requestId, con internalReference
// Firma: SHA-256(requestId + status.status + status.date + secretKey) con prefijo "sha256:"
//        o SHA-1 sin prefijo (esquema anterior)
// ==============================================================
$status_txt  = $data['status']['status'] ?? '';
$status_date = $data['status']['date'] ?? '';
$request_id  = (string)($data['requestId'] ?? $data['internalReference'] ?? '');
$reference   = (string)($data['reference'] ?? $data['payment'][0]['reference'] ?? '');
$signature   = (string)($data['signature'] ?? '');

if ($status_txt === '' || $request_id === '' || $signature === '') {
    http_response_code(400);
    echo json_encode(['status' => 'ERROR', 'message' => 'Datos incompletos']);
    exit();
}

// Validar firma probando los juegos de credenciales conocidos
$usa_sha256  = strpos($signature, 'sha256:') === 0;
$firma_plana = $usa_sha256 ? substr($signature, 7) : $signature;

$firma_ok = false;
foreach (P2P_CREDENCIALES as $cred) {
    $base = $request_id . $status_txt . $status_date . $cred['secretKey'];
    $calc = $usa_sha256 ? hash('sha256', $base) : sha1($base);
    if (hash_equals($calc, $firma_plana)) {
        $firma_ok = true;
        break;
    }
}

if (!$firma_ok) {
    http_response_code(403);
    echo json_encode(['status' => 'ERROR', 'message' => 'Firma invalida']);
    exit();
}

// Mapear estado
$nuevo_estado = match ($status_txt) {
    'APPROVED'           => 'aprobada',
    'APPROVED_PARTIAL'   => 'aprobada',
    'REJECTED', 'FAILED' => 'rechazada',
    'PENDING',
    'PENDING_VALIDATION' => 'pendiente',
    'REFUNDED'           => 'cancelada',
    default              => 'cancelada'
};

$rid_safe = mysqli_real_escape_string($conexion, $request_id);
$est_safe = mysqli_real_escape_string($conexion, $nuevo_estado);
$ref_safe = mysqli_real_escape_string($conexion, $reference);

// Enrutar a la tabla segun el prefijo de la referencia
$queries = [];

if (strpos($reference, 'PRE-') === 0) {
    // Preautorizaciones (reservaciones)
    $queries[] = "UPDATE reservaciones SET estado = '$est_safe' WHERE request_id = '$ref_safe' OR session_id = '$rid_safe'";

} elseif (strpos($reference, 'DISP-') === 0) {
    // Dispersiones (guarda la referencia en request_id)
    $queries[] = "UPDATE dispersiones SET estado = '$est_safe' WHERE request_id = '$ref_safe'";

} elseif (strpos($reference, 'PL-') === 0) {
    // Links de pago (tabla payment_link, columna referencia)
    $estado_link = ($nuevo_estado === 'aprobada') ? 'pagado' : $nuevo_estado;
    $inc_pagos   = ($nuevo_estado === 'aprobada') ? ", pagos_usados = pagos_usados + 1" : "";
    $el_safe = mysqli_real_escape_string($conexion, $estado_link);
    $queries[] = "UPDATE payment_link SET estado = '$el_safe'$inc_pagos WHERE referencia = '$ref_safe'";

} elseif (strpos($reference, 'REC-') === 0) {
    $id = intval(str_replace('REC-', '', $reference));
    $queries[] = "UPDATE recurrencias SET estado = '$est_safe' WHERE id = $id";

} elseif (strpos($reference, 'SUB-') === 0) {
    $id = intval(str_replace('SUB-', '', $reference));
    $queries[] = "UPDATE suscripciones SET estado = '$est_safe' WHERE id = $id AND request_id = '$rid_safe'";
    $queries[] = "UPDATE suscription SET estado = '$est_safe' WHERE id = $id AND request_id = '$rid_safe'";

} elseif (strpos($reference, 'SREC-') === 0) {
    $id = intval(str_replace('SREC-', '', $reference));
    $queries[] = "UPDATE suscription_rec SET estado = '$est_safe' WHERE id = $id AND request_id = '$rid_safe'";

} elseif (strpos($reference, 'GW-') === 0) {
    // Gateway (pago basico sin Web Checkout)
    $queries[] = "UPDATE gateway_ordenes SET estado = '$est_safe' WHERE request_id = '$rid_safe'";
    $queries[] = "UPDATE gateway_recurrencias SET estado = '$est_safe' WHERE request_id = '$rid_safe'";

} else {
    // Ordenes basicas (referencia numerica)
    $id = intval($reference);
    if ($id > 0) {
        $queries[] = "UPDATE ordenes SET estado = '$est_safe' WHERE id = $id";
    }
}

foreach ($queries as $q) {
    if (!mysqli_query($conexion, $q)) {
        error_log('Error webhook notify.php: ' . mysqli_error($conexion));
    }
}

// NOTA: la notificacion NO trae el token del medio de pago; para suscripciones
// se debe consultar la sesion via API (verificar_pago.php) tras el APPROVED.

// Responder 200 lo antes posible (PlaceToPay NO reintenta la notificacion)
http_response_code(200);
echo json_encode(['status' => 'OK']);