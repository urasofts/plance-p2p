<?php
// p2p_sonda_core.php — Núcleo reutilizable de consulta de estado a PlaceToPay.
// Usado tanto por verificar_pago.php (disparado por el usuario desde el
// navegador) como por la sonda/cronjob (cron/sonda.php y cron/sonda_http.php),
// que revisa periódicamente las órdenes que quedaron en "pendiente" porque
// el webhook de notify.php no llegó o el usuario no volvió a retorno/*.php.

require_once __DIR__ . '/p2p_config.php';

if (!function_exists('p2p_tablas_gateway')) {
    function p2p_tablas_gateway(): array {
        return ['gateway_ordenes', 'gateway_recurrencias', 'gateway_suscription'];
    }
}

if (!function_exists('p2p_credenciales_por_tabla')) {
    function p2p_credenciales_por_tabla(string $tabla): array {
        $creds = p2p_credenciales();
        if ($tabla === 'reservaciones') return $creds['preautorizacion'];
        if ($tabla === 'dispersiones')  return $creds['dispersion'];
        return $creds['principal'];
    }
}

if (!function_exists('p2p_construir_auth')) {
    function p2p_construir_auth(string $login, string $secretKey): array {
        $seed    = date('c');
        $nonce   = bin2hex(random_bytes(16));
        $tranKey = base64_encode(hash('sha256', $nonce . $seed . $secretKey, true));

        return [
            "login"   => $login,
            "tranKey" => $tranKey,
            "nonce"   => base64_encode($nonce),
            "seed"    => $seed,
        ];
    }
}

// Consulta el estado de una transaccion en PlaceToPay y actualiza la fila
// local si el estado devuelto es reconocido. Nunca degrada un estado
// desconocido a "cancelada": si PlaceToPay no responde algo mapeable, la
// fila se deja intacta para reintentar en la siguiente pasada.
if (!function_exists('p2p_consultar_y_actualizar')) {
    function p2p_consultar_y_actualizar($conexion, string $tabla, int $id, string $request_id): array {
        $es_gateway = in_array($tabla, p2p_tablas_gateway(), true);
        $cred       = p2p_credenciales_por_tabla($tabla);
        $auth       = p2p_construir_auth($cred['login'], $cred['secretKey']);

        if ($es_gateway) {
            $url     = "https://api-test.placetopay.com/rest/gateway/query";
            $payload = ["auth" => $auth, "internalReference" => $request_id];
        } else {
            $url     = "https://checkout-test.placetopay.com/api/session/" . $request_id;
            $payload = ["auth" => $auth];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $curl_err = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['ok' => false, 'mensaje' => "Error de conexion con PlaceToPay: $curl_err"];
        }

        $result     = json_decode($response, true);
        $status_p2p = $result['status']['status'] ?? 'UNKNOWN';

        // El estado real de la transaccion puede venir en payment[] (checkout);
        // se prioriza sobre el estado general de la sesion.
        if (!$es_gateway && !empty($result['payment'][0]['status']['status'])) {
            $status_p2p = $result['payment'][0]['status']['status'];
        }

        $map = [
            'APPROVED'  => 'aprobada',
            'REJECTED'  => 'rechazada',
            'PENDING'   => 'pendiente',
            'CANCELLED' => 'cancelada',
            'REFUNDED'  => 'cancelada',
            'FAILED'    => 'rechazada',
        ];

        if (!isset($map[$status_p2p])) {
            return ['ok' => false, 'mensaje' => "sin cambios (PlaceToPay respondio: $status_p2p)"];
        }

        $nuevo_estado = $map[$status_p2p];
        $estado_safe  = mysqli_real_escape_string($conexion, $nuevo_estado);

        // $tabla no se interpola libre: siempre llega ya validada contra la
        // whitelist de tablas permitidas en el llamador (verificar_pago.php,
        // p2p_sonda_ejecutar).
        mysqli_query($conexion, "UPDATE `$tabla` SET estado = '$estado_safe' WHERE id = " . (int) $id);

        return ['ok' => true, 'estado_nuevo' => $nuevo_estado, 'mensaje' => "actualizado a $nuevo_estado"];
    }
}

// Recalcula el estado y el monto_pagado de una orden de gateway_ordenes con
// tipo_pago = 'mixto' a partir de sus abonos aprobados en gateway_abonos.
// Si aun no hay ningun abono aprobado, refleja el resultado del ultimo
// abono intentado (para que el historial distinga "nunca se pagó nada y el
// ultimo intento fue rechazado" de "pendiente de un primer intento").
if (!function_exists('p2p_recalcular_orden_mixta')) {
    function p2p_recalcular_orden_mixta($conexion, int $orden_id): void {
        $row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT precio FROM gateway_ordenes WHERE id = " . $orden_id));
        if (!$row) return;
        $precio = (float) $row['precio'];

        $sumRow = mysqli_fetch_assoc(mysqli_query(
            $conexion,
            "SELECT COALESCE(SUM(monto), 0) AS pagado FROM gateway_abonos WHERE gateway_orden_id = $orden_id AND estado = 'aprobada'"
        ));
        $pagado = (float) ($sumRow['pagado'] ?? 0);

        if ($pagado >= $precio) {
            $nuevo_estado = 'aprobada';
        } elseif ($pagado > 0) {
            $nuevo_estado = 'parcial';
        } else {
            $ultimo = mysqli_fetch_assoc(mysqli_query(
                $conexion,
                "SELECT estado FROM gateway_abonos WHERE gateway_orden_id = $orden_id ORDER BY id DESC LIMIT 1"
            ));
            $nuevo_estado = $ultimo['estado'] ?? 'pendiente';
        }

        $pagado_safe = mysqli_real_escape_string($conexion, (string) $pagado);
        $estado_safe = mysqli_real_escape_string($conexion, $nuevo_estado);
        mysqli_query($conexion, "UPDATE gateway_ordenes SET monto_pagado = '$pagado_safe', estado = '$estado_safe' WHERE id = $orden_id");
    }
}

// Consulta el estado de un abono pendiente (gateway_abonos) contra PlaceToPay.
// Igual que p2p_consultar_y_actualizar pero a nivel de abono: al resolverse
// también recalcula la orden padre (gateway_ordenes) con la función de arriba.
if (!function_exists('p2p_verificar_abono')) {
    function p2p_verificar_abono($conexion, int $abono_id, string $request_id): array {
        $cred = p2p_credenciales()['principal'];
        $auth = p2p_construir_auth($cred['login'], $cred['secretKey']);

        $ch = curl_init("https://api-test.placetopay.com/rest/gateway/query");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode(["auth" => $auth, "internalReference" => $request_id]));
        curl_setopt($ch, CURLOPT_HTTPHEADER,     ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $curl_err = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return ['ok' => false, 'mensaje' => "Error de conexion con PlaceToPay: $curl_err"];
        }

        $result     = json_decode($response, true);
        $status_p2p = $result['status']['status'] ?? 'UNKNOWN';

        $map = [
            'APPROVED'  => 'aprobada',
            'REJECTED'  => 'rechazada',
            'PENDING'   => 'pendiente',
            'CANCELLED' => 'cancelada',
            'REFUNDED'  => 'cancelada',
            'FAILED'    => 'rechazada',
        ];

        if (!isset($map[$status_p2p]) || $map[$status_p2p] === 'pendiente') {
            return ['ok' => false, 'mensaje' => "sin cambios (PlaceToPay respondio: $status_p2p)"];
        }

        $nuevo_estado = $map[$status_p2p];
        $estado_safe  = mysqli_real_escape_string($conexion, $nuevo_estado);
        mysqli_query($conexion, "UPDATE gateway_abonos SET estado = '$estado_safe' WHERE id = " . (int) $abono_id);

        $abono_row = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT gateway_orden_id FROM gateway_abonos WHERE id = " . (int) $abono_id));
        if ($abono_row) {
            p2p_recalcular_orden_mixta($conexion, (int) $abono_row['gateway_orden_id']);
        }

        return ['ok' => true, 'estado_nuevo' => $nuevo_estado, 'mensaje' => "abono actualizado a $nuevo_estado"];
    }
}

// Recorre las tablas indicadas buscando filas "pendiente" cuyo created_at
// tenga al menos $margen_min minutos (para no chocar con un pago que el
// usuario todavia esta completando), las consulta contra PlaceToPay, y
// devuelve un resumen por tabla. Usado por cron/sonda.php y cron/sonda_http.php.
if (!function_exists('p2p_sonda_ejecutar')) {
    function p2p_sonda_ejecutar($conexion, array $tablas, int $margen_min, int $limite): array {
        $resumen = [];

        foreach ($tablas as $tabla) {
            $sql = "SELECT id, request_id FROM `$tabla` " .
                   "WHERE estado = 'pendiente' AND request_id IS NOT NULL AND request_id <> '' " .
                   "AND created_at <= (NOW() - INTERVAL " . (int) $margen_min . " MINUTE) " .
                   "LIMIT " . (int) $limite;

            $rs = mysqli_query($conexion, $sql);
            if ($rs === false) {
                $resumen[$tabla] = ['error' => mysqli_error($conexion)];
                continue;
            }

            $revisadas    = 0;
            $actualizadas = 0;
            $detalle      = [];

            while ($fila = mysqli_fetch_assoc($rs)) {
                $revisadas++;
                $r = p2p_consultar_y_actualizar($conexion, $tabla, (int) $fila['id'], (string) $fila['request_id']);
                if ($r['ok']) {
                    $actualizadas++;
                }
                $detalle[] = "#{$fila['id']}: {$r['mensaje']}";
            }

            $resumen[$tabla] = [
                'revisadas'    => $revisadas,
                'actualizadas' => $actualizadas,
                'detalle'      => $detalle,
            ];
        }

        return $resumen;
    }
}
