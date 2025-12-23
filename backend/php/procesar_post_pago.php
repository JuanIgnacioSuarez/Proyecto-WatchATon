<?php
// backend/php/procesar_post_pago.php
require_once __DIR__ . '/../classes/Conexion.php';

/**
 * Procesa un pago confirmado.
 * - Registra el pago en la tabla `pagos`.
 * - Otorga el beneficio Premium en `canjeos`.
 */
function procesarPagoConfirmado($paymentData) {
    $conexion = new Conexion();
    
    $id_usuario = $paymentData['id_usuario'];
    $monto = $paymentData['monto'];
    $moneda = $paymentData['moneda'];
    $metodo = $paymentData['metodo_pago'];
    $id_transaccion = $paymentData['id_transaccion_externa'];
    $estado = $paymentData['estado'];

    // 1. Verificar si el pago ya existe para evitar duplicados
    $sqlCheck = "SELECT id_pago FROM pagos WHERE id_transaccion_externa = ?";
    $existe = $conexion->consultar($sqlCheck, "s", [$id_transaccion]);

    if (!empty($existe)) {
        // El pago ya fue procesado
        return ["success" => true, "message" => "Pago ya registrado previamente"];
    }

    // 2. Procesar Aprobado
    if ($estado === 'approved' || $estado === 'completed') {
        // Otorgar Premium (Canje)
        // El ID_beneficio 0 es PREMIUM
        $durationDays = isset($paymentData['metadata']['duration_days']) ? (int)$paymentData['metadata']['duration_days'] : 30;
        
        $fechaVencimiento = date('Y-m-d H:i:s', strtotime("+$durationDays days"));

        $sqlCanje = "INSERT INTO canjeos (ID_usuario, ID_beneficio, Fecha, fecha_vencimiento) VALUES (?, 0, NOW(), ?)";
        $conexion->insertar($sqlCanje, "is", [$id_usuario, $fechaVencimiento]);
        
        // Obtener ID del canje recién creado
        $idCanje = $conexion->ultimoId();
        
        // Insertar Pago vinculado al Canje
        $sqlInsert = "INSERT INTO pagos (id_usuario, monto, moneda, metodo_pago, id_transaccion_externa, estado, fecha, id_canje) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        $conexion->insertar($sqlInsert, "iddsssi", [$id_usuario, $monto, $moneda, $metodo, $id_transaccion, $estado, $idCanje]);
        
        return ["success" => true, "message" => "Pago registrado y Premium otorgado"];
    } else {
        // Pago no aprobado, solo registrar en pagos (sin id_canje)
        $sqlInsert = "INSERT INTO pagos (id_usuario, monto, moneda, metodo_pago, id_transaccion_externa, estado, fecha) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $conexion->insertar($sqlInsert, "iddsss", [$id_usuario, $monto, $moneda, $metodo, $id_transaccion, $estado]);
        
        return ["success" => true, "message" => "Pago registrado con estado: $estado"];
    }

    return ["success" => true, "message" => "Pago registrado con estado: $estado"];
}
?>
