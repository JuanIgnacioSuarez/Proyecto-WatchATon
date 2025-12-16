<?php
// backend/php/admin/update_pagos_schema.php
require_once __DIR__ . '/../../classes/Conexion.php';

$conexion = new Conexion();

// Intentar agregar la columna id_canje si no existe
$sql = "SHOW COLUMNS FROM pagos LIKE 'id_canje'";
$result = $conexion->consultar($sql);

if (empty($result)) {
    // No existe, agregarla
    // Asumimos que canjeos.ID_canje es INT.
    // Agregar INDEX para performance
    $sqlAlter = "ALTER TABLE pagos ADD COLUMN id_canje INT DEFAULT NULL, ADD INDEX (id_canje)";
    
    if ($conexion->ejecutar($sqlAlter)) {
        echo "Columna 'id_canje' agregada correctamente a la tabla 'pagos'.";
    } else {
        echo "Error al agregar columna: " . $conexion->conn->error; // Acceso directo a conn para error en script admin
    }
} else {
    echo "La columna 'id_canje' ya existe.";
}
$conexion->cerrarConexion();
?>
