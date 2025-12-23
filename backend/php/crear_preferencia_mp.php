<?php
// backend/php/crear_preferencia_mp.php
session_start();
require_once __DIR__ . '/mercadopago_config.php';
require_once __DIR__ . '/../classes/Conexion.php';

header('Content-Type: application/json');

// Verificar autenticación via Cookie (Legacy)
if (!isset($_COOKIE['iniciado'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

$email = $_COOKIE['iniciado'];

// Obtener ID del usuario desde la DB
$conexion = new Conexion();
$userId = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

if (!$userId) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado en base de datos']);
    exit;
}

$userEmail = $email;
$conexion->cerrarConexion();

// Datos del producto según Plan
$plan = $_POST['plan'] ?? 'monthly';
$titulo = "Membresía Premium (Mensual) - WatchATon";
$precioUnitario = 2000.00;
$durationDays = 30;

if ($plan === 'yearly') {
    $titulo = "Membresía Premium (Anual) - WatchATon";
    $precioUnitario = 20000.00;
    $durationDays = 365;
}

$cantidad = 1;

// Configuración de la Preferencia (API Request)
$datosPreferencia = [
    "items" => [
        [
            "title" => $titulo,
            "quantity" => $cantidad,
            "unit_price" => $precioUnitario,
            "currency_id" => "ARS"
        ]
    ],
    "payer" => [
        "email" => $userEmail
    ],
    "back_urls" => [
        "success" => MP_BACK_URL_SUCCESS,
        "failure" => MP_BACK_URL_FAILURE,
        "pending" => MP_BACK_URL_PENDING
    ],
    // "auto_return" => "approved",
    "external_reference" => (string)$userId, 
    "statement_descriptor" => "WATCHATON PREMIUM",
    "metadata" => [
        "duration_days" => $durationDays,
        "plan_type" => $plan
    ]
];

if (defined('MP_NOTIFICATION_URL') && MP_NOTIFICATION_URL !== null) {
    $datosPreferencia["notification_url"] = MP_NOTIFICATION_URL;
}

// Llamada cURL a Mercado Pago
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/checkout/preferences");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosPreferencia));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . MP_ACCESS_TOKEN
]);

$respuesta = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// DEBUG 
$logMsg = date('Y-m-d H:i:s') . "\n";
$logMsg .= "HTTP Code: " . $httpCode . "\n";
$logMsg .= "Response: " . $respuesta . "\n";
$logMsg .= "Curl Error: " . $curlError . "\n";
$logMsg .= "Payload Sent: " . json_encode($datosPreferencia) . "\n";
$logMsg .= "-----------------------------------\n";
file_put_contents(__DIR__ . '/../debug/debug_mp.log', $logMsg, FILE_APPEND);

if ($httpCode === 201) {
    $data = json_decode($respuesta, true);
    echo json_encode([
        'preference_id' => $data['id'],
        'init_point' => $data['init_point'], // Link de pago directo si se necesita
        'sandbox_init_point' => $data['sandbox_init_point']
    ]);
} else {
    http_response_code(500);
    // Return the response for frontend debugging too
    echo json_encode(['error' => 'Error al crear la preferencia', 'details' => json_decode($respuesta) ?? $respuesta]);
}
?>
