<?php
session_start();
require_once('../classes/Conexion.php');
$conexion = new Conexion();

if(isset($_COOKIE['iniciado'])){

    $puntosActuales = $conexion->existeDato('usuarios','Puntos','Correo',$_COOKIE['iniciado']);
    $nuevosPuntos = rand(300, 700);
    $puntosActuales = $puntosActuales + $nuevosPuntos; //Sumos una cantidad de puntos random entre 300 y 700

    $query = "UPDATE `usuarios` SET `Puntos` = ? WHERE `Correo` = ?";  

    $tipos = "is";
    $parametros = [$puntosActuales, $_COOKIE['iniciado']];
    $conexion->actualizar($query, $tipos, $parametros);

    echo $nuevosPuntos;
}
else{
    echo 0;
}
?>