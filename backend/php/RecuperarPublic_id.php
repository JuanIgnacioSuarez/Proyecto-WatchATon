<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();

$IdPublica = $conexion->existeDato('videos', 'public_id', 'ID_video', $_POST['idVideo']);
echo $IdPublica;
?>