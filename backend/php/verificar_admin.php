<?php
require_once(__DIR__ . '/../classes/Conexion.php');

function checkAdmin() {
    if (!isset($_COOKIE['iniciado'])) {
        echo json_encode(['success' => false, 'error' => 'No iniciado']);
        exit;
    }

    $conexion = new Conexion();
    $email = $_COOKIE['iniciado'];
    
    // Verificar permisos
    $sql = "SELECT ID, Permisos FROM usuarios WHERE Correo = ?";
    $res = $conexion->consultar($sql, "s", [$email]);

    if (empty($res) || $res[0]['Permisos'] != 1) {
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
        exit;
    }

    return $res[0]['ID'];
}
?>
