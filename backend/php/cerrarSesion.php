<?php
setcookie('iniciado', '', time() - 3600, '/');  //Seteamos la cookie en tiempo negativo para que se elimine 
header('Location:../../frontend/views/index.php'); //Simplemente redijirimos al inicio
exit;
?>