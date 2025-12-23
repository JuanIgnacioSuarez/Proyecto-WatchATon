<?php
// backend/php/retorno_pagos.php

require_once __DIR__ . '/mercadopago_config.php';
require_once __DIR__ . '/procesar_post_pago.php'; // Incluye Conexion.php

// Verificar sesión (Opcional: Si MP redirige, el navegador debería enviar la cookie)
if (!isset($_COOKIE['iniciado'])) {
    header("Location: ../../frontend/views/index.php");
    exit;
}

// Obtener parámteros de MP
$payment_id = $_GET['payment_id'] ?? null;
$status = $_GET['status'] ?? null;
$external_reference = $_GET['external_reference'] ?? null;
$preference_id = $_GET['preference_id'] ?? null;

if (!$payment_id || !$status) {
    // Retorno sin datos validos
    header("Location: " . MP_BACK_URL_FAILURE . "&error=missing_params");
    exit;
}

if ($status === 'approved') {
    // VERIFICACIÓN DOBLE CON API DE MERCADO PAGO
    // Para evitar que alguien ponga ?status=approved manualmente
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.mercadopago.com/v1/payments/$payment_id",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . MP_ACCESS_TOKEN
        ]
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode === 200) {
        $paymentData = json_decode($response, true);
        
        // Verificar que coincida el status real
        $realStatus = $paymentData['status'];
        
        if ($realStatus === 'approved') {
             // Preparar datos para procesar
             $datosPago = [
                 'id_usuario' => $paymentData['external_reference'], 
                 'monto' => $paymentData['transaction_amount'],
                 'moneda' => $paymentData['currency_id'],
                 'metodo_pago' => $paymentData['payment_method_id'],
                 'id_transaccion_externa' => $paymentData['id'], 
                 'estado' => $realStatus,
                 'metadata' => $paymentData['metadata'] ?? [] // Metadata con duracion
             ];
             
             // Procesar (Guardar en DB y dar Premium)
             $resultado = procesarPagoConfirmado($datosPago);
             
             if ($resultado['success']) {
                 // Éxito total
                 // Actualizar cookie Premium para que el frontend lo refleje de inmediato
                 setcookie('Premium', 'true', time() + (86400 * 30), '/'); 
                 
                 header("Location: ../../frontend/views/perfil.php?status=success&payment_id=$payment_id");
                 exit;
             } else {
                 // Error interno al guardar
                 header("Location: " . MP_BACK_URL_FAILURE . "&error=db_error");
                 exit;
             }
             
        } else {
            // El estado real no es aprobado (ej: pending, rejected)
            header("Location: " . MP_BACK_URL_FAILURE . "&error=status_mismatch&real_status=$realStatus");
            exit;
        }
        
    } else {
        // Error de conexión con API MP
        header("Location: " . MP_BACK_URL_FAILURE . "&error=api_verification_failed");
        exit;
    }

} else {
    // Volvió pero no approved (ej: pending, failure)
    header("Location: ../../frontend/views/perfil.php?status=" . $status);
    exit;
}
?>
