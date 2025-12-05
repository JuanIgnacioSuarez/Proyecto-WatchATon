<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();

if (!isset($_COOKIE['iniciado'])) {
    echo json_encode(['success' => false, 'message' => 'No iniciado']);
    exit;
}

$email = $_COOKIE['iniciado'];
$idUsuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);
$idMensaje = $_POST['id_mensaje'] ?? null;

if ($idUsuario && $idMensaje) {
    // Verificar que el mensaje pertenece al usuario (no es global)
    $sqlCheck = "SELECT id_destinatario FROM mensajes WHERE id_mensaje = ?";
    $res = $conexion->consultar($sqlCheck, "i", [$idMensaje]);

    if (!empty($res) && $res[0]['id_destinatario'] == $idUsuario) {
        $sqlUpdate = "UPDATE mensajes SET leido = 1 WHERE id_mensaje = ?";
        $conexion->actualizar($sqlUpdate, "i", [$idMensaje]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Mensaje no válido o global']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
}
?>
