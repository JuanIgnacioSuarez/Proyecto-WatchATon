<?php
// backend/php/admin/configurar_expiracion_premium.php
require_once('../verificar_admin.php');
checkAdmin();

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
    // 1. Habilitar el programador de eventos global
    $sqlProgramador = "SET GLOBAL event_scheduler = ON";
    if ($mysqli->query($sqlProgramador)) {
        echo "Programador de eventos habilitado.<br>";
    } else {
        throw new Exception("Error habilitando programador: " . $mysqli->error);
    }

    // 2. Crear el evento para desactivar beneficios vencidos (Premium y otros)
    // Primero eliminamos el evento anterior
    $mysqli->query("DROP EVENT IF EXISTS expirar_premium");

    $sqlEvento = "CREATE EVENT expirar_premium
                 ON SCHEDULE EVERY 1 HOUR
                 DO
                 UPDATE canjeos 
                 SET activo = 0
                 WHERE fecha_vencimiento IS NOT NULL 
                 AND fecha_vencimiento < NOW() AND activo = 1";

    if ($mysqli->query($sqlEvento)) {
        echo "Evento 'expirar_premium' configurado correctamente.<br>";
    } else {
        throw new Exception("Error creando el evento: " . $mysqli->error);
    }

    echo "Configuración completada con éxito.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$mysqli->close();
?>
