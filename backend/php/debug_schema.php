<?php
// backend/php/debug_schema.php
require_once '../classes/Conexion.php';
$conexion = new Conexion();
$sql = "SHOW COLUMNS FROM canjeos";
$result = $conexion->consultar($sql);
echo "<pre>";
print_r($result);
echo "</pre>";
?>
