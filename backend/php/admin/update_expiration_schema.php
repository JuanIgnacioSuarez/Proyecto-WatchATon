<?php
// backend/php/admin/update_expiration_schema.php
require_once __DIR__ . '/../../classes/Conexion.php';

$conexion = new Conexion();

// 1. Agregar dias_duracion a beneficios
$sql = "SHOW COLUMNS FROM beneficios LIKE 'dias_duracion'";
$result = $conexion->consultar($sql);

if (empty($result)) {
    // Agregar columna dias_duracion (INT, NULL)
    // NULL = Infinito
    $sqlAlter = "ALTER TABLE beneficios ADD COLUMN dias_duracion INT DEFAULT NULL";
    if ($conexion->ejecutar($sqlAlter)) {
        echo "Columna 'dias_duracion' agregada a 'beneficios'.<br>";
    } else {
        echo "Error agregando 'dias_duracion': " . $conexion->conn->error . "<br>";
    }
} else {
    echo "Columna 'dias_duracion' ya existe en 'beneficios'.<br>";
}

// 2. Agregar fecha_vencimiento a canjeos
$sql = "SHOW COLUMNS FROM canjeos LIKE 'fecha_vencimiento'";
$result = $conexion->consultar($sql);

if (empty($result)) {
    // Agregar columna fecha_vencimiento (DATETIME, NULL)
    $sqlAlter = "ALTER TABLE canjeos ADD COLUMN fecha_vencimiento DATETIME DEFAULT NULL";
    if ($conexion->ejecutar($sqlAlter)) {
        echo "Columna 'fecha_vencimiento' agregada a 'canjeos'.<br>";
    } else {
        echo "Error agregando 'fecha_vencimiento': " . $conexion->conn->error . "<br>";
    }
} else {
    echo "Columna 'fecha_vencimiento' ya existe en 'canjeos'.<br>";
}

$conexion->cerrarConexion();
?>
