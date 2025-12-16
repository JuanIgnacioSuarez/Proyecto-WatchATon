<?php
// backend/php/webhook_mercadopago.php
require_once __DIR__ . '/mercadopago_config.php';
require_once __DIR__ . '/procesar_post_pago.php';

// Mercado Pago envía notificaciones por POST con query params
// Ejemplo: ?topic=payment&id=123456789

$topic = $_GET['topic'] ?? $_GET['type'] ?? null;
$id = $_GET['id'] ?? $_GET['data_id'] ?? null;

if (($topic === 'payment' || $topic === 'payment_intent') && $id) {
    
    // Consultar estado del pago a MP
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments/$id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . MP_ACCESS_TOKEN
    ]);

    $respuesta = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $paymentInfo = json_decode($respuesta, true);
        
        $status = $paymentInfo['status']; // approved, pending, rejected
        $external_ref = $paymentInfo['external_reference']; // id_usuario
        $transaction_amount = $paymentInfo['transaction_amount'];
        $currency_id = $paymentInfo['currency_id'];
        
        // Preparar datos para procesar
        $datosPago = [
            'id_usuario' => (int)$external_ref,
            'monto' => $transaction_amount,
            'moneda' => $currency_id,
            'metodo_pago' => 'MercadoPago',
            'id_transaccion_externa' => (string)$id,
            'estado' => $status
        ];

        // Procesar
        $resultado = procesarPagoConfirmado($datosPago);
        
        http_response_code(200);
        echo json_encode($resultado);
    } else {
        http_response_code(400); // Bad Request o Error
    }
} else {
    // Si no es un payment notification, solo respondemos 200 para que MP no reintente
    http_response_code(200);
}
?>
