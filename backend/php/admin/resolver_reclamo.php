<?php
session_start();
require_once '../../classes/Conexion.php';

// Validar Admin via Cookie (Consistente con gestionar_reclamos.php)
if (!isset($_COOKIE['iniciado'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$conexion = new Conexion();
$email = $_COOKIE['iniciado'];
$usuario = $conexion->consultar("SELECT Permisos FROM usuarios WHERE Correo = ?", "s", [$email]);

if (empty($usuario) || $usuario[0]['Permisos'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reclamo = isset($_POST['id_reclamo']) ? intval($_POST['id_reclamo']) : 0;
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($id_reclamo <= 0 || !in_array($accion, ['aceptar', 'rechazar'])) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
    }

    $nuevo_estado = ($accion === 'aceptar') ? 'Aceptado' : 'Rechazado';
    
    // Actualizar estado del reclamo
    $sql = "UPDATE Reclamos SET Estado = ? WHERE ID = ?";
    $resultado = $conexion->actualizar($sql, "si", [$nuevo_estado, $id_reclamo]);

    $conexion->cerrarConexion();

    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Reclamo ' . $nuevo_estado]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el reclamo']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
