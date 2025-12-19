<?php
// backend/php/mercadopago_config.php

// Credenciales de Mercado Pago (Sandbox/Production)
define('MP_ACCESS_TOKEN', 'APP_USR-2462697044645494-121613-5629bdadada435c02ca6c0cc551154d1-3070440674');
define('MP_PUBLIC_KEY', 'APP_USR-44cd806e-504a-4aa7-a189-3cfb8ec45eab');

// Para este prototipo se asume locahost, es otras versiones se cambiara a una url del dominio  oficial.
define('BASE_URL', 'http://localhost/watchaton'); 

define('MP_BACK_URL_SUCCESS', BASE_URL . '/backend/php/retorno_pagos.php');
define('MP_BACK_URL_FAILURE', BASE_URL . '/frontend/views/comprar_premium.php?status=failure');
define('MP_BACK_URL_PENDING', BASE_URL . '/frontend/views/perfil.php?status=pending');

// Como estamos en local, lo dejamos en null para evitar conflictos
define('MP_NOTIFICATION_URL', null); // null para deshabilitar en local 
?>
