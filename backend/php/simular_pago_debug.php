<?php
// backend/php/simular_pago_debug.php
// SCRIPT SOLO PARA DESARROLLO LOCAL
require_once __DIR__ . '/procesar_post_pago.php'; // Incluye Conexion.php

session_start();

// Verificar cookie (seguridad mínima)
if (!isset($_COOKIE['iniciado'])) {
    die("Debes estar logueado.");
}

$email = $_COOKIE['iniciado'];
$conexion = new Conexion();
$userId = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

if (!$userId) {
    die("Usuario no encontrado.");
}

// Simulamos datos de pago
$plan = $_GET['plan'] ?? 'monthly';
$monto = 2000.00;
$durationDays = 30;

if ($plan === 'yearly') {
    $monto = 20000.00;
    $durationDays = 365;
}

$fakePaymentId = "SIM-" . time();
$datosPago = [
    'id_usuario' => $userId, 
    'monto' => $monto,
    'moneda' => 'ARS',
    'metodo_pago' => 'debug_simulation',
    'id_transaccion_externa' => $fakePaymentId, 
    'estado' => 'approved',
    'metadata' => [
        'duration_days' => $durationDays,
        'plan_type' => $plan
    ]
];

// Procesar
$resultado = procesarPagoConfirmado($datosPago);

$conexion->cerrarConexion();

if ($resultado['success']) {
    // Actualizar cookie Premium inmediato
    setcookie('Premium', 'true', time() + (86400 * 30), '/');
    
    // Redirigir a perfil con éxito
    header("Location: ../../frontend/views/perfil.php?status=success&payment_id=$fakePaymentId");
} else {
    echo "Error simulando pago: " . $resultado['message'];
}
?>
