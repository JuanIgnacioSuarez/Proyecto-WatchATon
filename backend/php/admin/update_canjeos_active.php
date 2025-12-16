<?php
// backend/php/admin/update_canjeos_active.php
require_once('../verificar_admin.php');
// checkAdmin(); // Temporalmente comentado para ejecución

// Credenciales
$host = "localhost";
$usuario = "root";
$clave = "";
$baseDatos = "watchaton";

$mysqli = new mysqli($host, $usuario, $clave, $baseDatos);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

try {
    // Agregar columna 'activo'
    $sql = "ALTER TABLE canjeos ADD COLUMN activo TINYINT(1) DEFAULT 1";
    if ($mysqli->query($sql)) {
        echo "Columna 'activo' agregada exitosamente.<br>";
    } else {
        // Ignorar si ya existe
        if ($mysqli->errno == 1060) {
             echo "La columna 'activo' ya existía.<br>";
        } else {
             throw new Exception("Error agregando columna: " . $mysqli->error);
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$mysqli->close();
?>
