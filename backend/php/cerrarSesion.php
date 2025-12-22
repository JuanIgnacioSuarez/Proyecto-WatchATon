<?php
setcookie('iniciado', '', time() - 3600, '/');  //Seteamos la cookie en tiempo negativo para que se elimine 
setcookie('Premium', '', time() - 3600, '/');  // Eliminar cookie Premium
setcookie('SkipAds', '', time() - 3600, '/');  // Eliminar preferencia de anuncios
setcookie('es_admin', '', time() - 3600, '/');  // Eliminar cookie admin (limpieza) 
header('Location:../../frontend/views/index.php'); //Simplemente redijirimos al inicio
exit;
?>