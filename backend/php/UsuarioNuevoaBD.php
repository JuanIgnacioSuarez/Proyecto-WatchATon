<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();

$query = "INSERT INTO `usuarios` (`Correo`) VALUES (?)";  //Creo un nuevo usuario en la base (Tambien estara guardado en firebase)
$email = $_POST['email'];
$tipos = "s";
$parametros = [$email];

$conexion->insertar($query, $tipos, $parametros);
echo "";
?>