<?php
header('Content-Type: application/json');
require_once('../classes/Conexion.php');

if (!isset($_COOKIE['iniciado'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$idMensaje = isset($_POST['id_mensaje']) ? intval($_POST['id_mensaje']) : 0;

if ($idMensaje <= 0) {
    echo json_encode(['error' => 'ID de mensaje inválido']);
    exit;
}

$conexion = new Conexion();
$email = $_COOKIE['iniciado'];
$idUsuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

if (!$idUsuario) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Verificar que el mensaje pertenece al usuario Y NO es global (id_destinatario != 0)
// Asumimos que si id_destinatario es NULL o 0 es global.
$sqlCheck = "SELECT id_destinatario FROM mensajes WHERE id_mensaje = ?";
$mensaje = $conexion->consultar($sqlCheck, "i", [$idMensaje]);

if (empty($mensaje)) {
    echo json_encode(['error' => 'Mensaje no encontrado']);
    exit;
}

$destinatario = $mensaje[0]['id_destinatario'];

if ($destinatario == 0 || $destinatario == null) {
    echo json_encode(['error' => 'No puedes eliminar mensajes globales']);
    exit;
}

if ($destinatario != $idUsuario) {
    echo json_encode(['error' => 'No tienes permiso para eliminar este mensaje']);
    exit;
}

// Proceder a eliminar
$sqlDelete = "DELETE FROM mensajes WHERE id_mensaje = ?";
$deleted = $conexion->eliminar($sqlDelete, "i", [$idMensaje]);

if ($deleted) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al eliminar mensaje']);
}
?>
