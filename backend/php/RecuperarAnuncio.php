<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();


$sql = "SELECT ID_anuncio, public_id FROM anuncios ORDER BY RAND() LIMIT 1";
$resultado = $conexion->consultar($sql);

if (count($resultado) > 0) {
    echo json_encode($resultado[0]);
} else {
    echo json_encode([]);
}
?>