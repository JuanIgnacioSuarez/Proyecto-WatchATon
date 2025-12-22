<?php
require_once('../verificar_admin.php');
// Verificar admin (esto detiene la ejecución si no es admin)
$idAdmin = checkAdmin();

require_once('../../classes/Conexion.php');
$conexion = new Conexion();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'list_global':
        // Obtener mensajes globales ordenados por fecha descendente
        $sql = "SELECT m.*, u.nombre_usuario as remitente_nombre 
                FROM mensajes m 
                LEFT JOIN usuarios u ON m.id_remitente = u.ID 
                WHERE m.tipo = 0 
                ORDER BY m.fecha DESC";
        $res = $conexion->consultar($sql);
        echo json_encode($res);
        break;

    case 'send_global':
        $titulo = $_POST['titulo'] ?? '';
        $contenido = $_POST['contenido'] ?? '';

        if (empty($titulo) || empty($contenido)) {
            echo json_encode(['success' => false, 'error' => 'Título y contenido requeridos']);
            exit;
        }

        // Insertar mensaje global: id_destinatario = NULL (para no romper FK), id_remitente = admin
        $sql = "INSERT INTO mensajes (titulo, contenido, tipo, id_destinatario, id_remitente, leido, fecha) VALUES (?, ?, 0, NULL, ?, 0, NOW())";
        
        $id = $conexion->insertar($sql, "ssi", [$titulo, $contenido, $idAdmin]);
        
        if ($id) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al enviar mensaje']);
        }
        break;

    case 'edit_global':
        $idMensaje = $_POST['id_mensaje'] ?? '';
        $titulo = $_POST['titulo'] ?? '';
        $contenido = $_POST['contenido'] ?? '';

        if (empty($idMensaje) || empty($titulo) || empty($contenido)) {
            echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos (ID, título o contenido)']);
            exit;
        }

        // Actualizar mensaje
        $sql = "UPDATE mensajes SET titulo = ?, contenido = ? WHERE id_mensaje = ?";
        // Tipos: s (string), s (string), i (integer)
        $res = $conexion->actualizar($sql, "ssi", [$titulo, $contenido, (int)$idMensaje]);

        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar el mensaje']);
        }
        break;

    case 'delete_global':
        $idMensaje = $_POST['id_mensaje'] ?? '';

        if (empty($idMensaje)) {
            echo json_encode(['success' => false, 'error' => 'ID de mensaje no proporcionado']);
            exit;
        }

        // Eliminar mensaje
        $sql = "DELETE FROM mensajes WHERE id_mensaje = ?";
        // Tipos: i (integer)
        $res = $conexion->eliminar($sql, "i", [(int)$idMensaje]);

        if ($res) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al eliminar el mensaje']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción inválida']);
        break;
}
?>
