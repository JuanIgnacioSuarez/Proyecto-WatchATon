<?php
// backend/php/mercadopago_config.php

// Credenciales de Mercado Pago (Sandbox/Production)
define('MP_ACCESS_TOKEN', 'APP_USR-2462697044645494-121613-5629bdadada435c02ca6c0cc551154d1-3070440674');
define('MP_PUBLIC_KEY', 'APP_USR-44cd806e-504a-4aa7-a189-3cfb8ec45eab');

// URLs de retorno (Ajustar según tu entorno local o producción)
// Asumiendo localhost para desarrollo
// Correccion: Revertimos a localhost pero con url limpia, y desactivaremos auto_return
define('BASE_URL', 'http://localhost/watchaton'); 

define('MP_BACK_URL_SUCCESS', BASE_URL . '/backend/php/retorno_pagos.php');
define('MP_BACK_URL_FAILURE', BASE_URL . '/frontend/views/comprar_premium.php?status=failure');
define('MP_BACK_URL_PENDING', BASE_URL . '/frontend/views/perfil.php?status=pending');

// URL para Webhook (Necesita ser accesible públicamente)
// En producción: 'https://tu-dominio.com/backend/php/webhook_mercadopago.php'
// Para localhost sin ngrok, NO ENVIAR notification_url o MP puede rechazar la preferencia .
define('MP_NOTIFICATION_URL', null); // null para deshabilitar en local 
?>
