<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../vendor/autoload.php';
require_once('../classes/Conexion.php');

use Cloudinary\Cloudinary;

$response = ['success' => false, 'message' => ''];

// 1. Verificar Sesión
if (!isset($_COOKIE['iniciado'])) {
    $response['message'] = 'No hay sesión iniciada.';
    echo json_encode($response);
    exit();
}

$email = $_COOKIE['iniciado'];
$conexion = new Conexion();

try {
    // 2. Verificar Permisos de Admin
    $sqlUser = "SELECT ID, Permisos FROM usuarios WHERE Correo = ?";
    $resUser = $conexion->consultar($sqlUser, "s", [$email]);

    if (empty($resUser) || $resUser[0]['Permisos'] != 1) {
        throw new Exception("Acceso denegado. No tienes permisos de administrador.");
    }

    $idAdmin = $resUser[0]['ID'];
    $action = $_POST['action'] ?? '';

    if ($action === 'sanction') {
        $targetId = $_POST['targetId'];
        $targetType = $_POST['targetType'];
        $reason = $_POST['reason'];
        $description = $_POST['description'];
        $applySanction = (int)$_POST['applySanction']; // 1 or 0

        // Configuración Cloudinary
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => 'dqrxdpqef',
                'api_key'    => '175642324611446',
                'api_secret' => 'mZQ1yBbkRrn8LVvQjL_dhCwO4rc'
            ]
        ]);

        if ($targetType === 'video') {
            // Obtener datos del video
            $sqlVideo = "SELECT ID_usuario, public_id, public_id_portada, Titulo FROM videos WHERE ID_video = ?";
            $videoData = $conexion->consultar($sqlVideo, "i", [$targetId]);

            if (empty($videoData)) {
                throw new Exception("El video no existe.");
            }

            $idUsuarioDestino = $videoData[0]['ID_usuario'];
            $tituloVideo = $videoData[0]['Titulo'];
            $publicId = $videoData[0]['public_id'];
            $publicIdPortada = $videoData[0]['public_id_portada'];

            // Actualizar BD (Soft Delete / Ocultar)
            $conexion->actualizar("UPDATE videos SET sancionado = 1 WHERE ID_video = ?", "i", [$targetId]);

            // Registrar Sanción
            $sqlSancion = "INSERT INTO sanciones (id_usuario, id_admin, motivo, descripcion, contenido_original, tipo, id_objeto, tipo_objeto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $conexion->insertar($sqlSancion, "iissssss", [$idUsuarioDestino, $idAdmin, $reason, $description, $tituloVideo, $applySanction, $targetId, 'video']);

            // Enviar Mensaje
            $tituloMensaje = "Video Eliminado: " . $tituloVideo;
            $contenidoMensaje = "Tu video ha sido eliminado por el siguiente motivo: " . $reason . ". \n\nDetalles: " . $description;
            if ($applySanction) {
                $contenidoMensaje .= "\n\nSE HA APLICADO UNA SANCIÓN A TU CUENTA.";
            }
            
            $sqlMensaje = "INSERT INTO mensajes (id_remitente, id_destinatario, titulo, contenido, tipo) VALUES (?, ?, ?, ?, ?)";
            $conexion->insertar($sqlMensaje, "iisss", [$idAdmin, $idUsuarioDestino, $tituloMensaje, $contenidoMensaje, 'sancion']);

            $response['success'] = true;
            $response['message'] = 'Video eliminado y sanción aplicada correctamente.';

        } elseif ($targetType === 'comment') {
            // Obtener datos del comentario
            $sqlComment = "SELECT ID_usuario, Contenido FROM comentarios WHERE id_comentario = ?";
            $commentData = $conexion->consultar($sqlComment, "i", [$targetId]);

            if (empty($commentData)) {
                throw new Exception("El comentario no existe.");
            }

            $idUsuarioDestino = $commentData[0]['ID_usuario'];
            $contenidoComentario = $commentData[0]['Contenido'];

            // Actualizar BD (Soft Delete / Ocultar)
            $conexion->actualizar("UPDATE comentarios SET sancionado = 1 WHERE id_comentario = ?", "i", [$targetId]);

            // Registrar Sanción con el contenido del comentario
            $sqlSancion = "INSERT INTO sanciones (id_usuario, id_admin, motivo, descripcion, contenido_original, tipo, id_objeto, tipo_objeto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $conexion->insertar($sqlSancion, "iissssss", [$idUsuarioDestino, $idAdmin, $reason, $description, $contenidoComentario, $applySanction, $targetId, 'comentario']);

            // Enviar Mensaje
            $tituloMensaje = "Comentario Eliminado";
            $contenidoMensaje = "Tu comentario '" . substr($contenidoComentario, 0, 20) . "...' ha sido eliminado por: " . $reason . ". \n\nDetalles: " . $description;
            if ($applySanction) {
                $contenidoMensaje .= "\n\nSE HA APLICADO UNA SANCIÓN A TU CUENTA.";
            }

            $sqlMensaje = "INSERT INTO mensajes (id_remitente, id_destinatario, titulo, contenido, tipo) VALUES (?, ?, ?, ?, ?)";
            $conexion->insertar($sqlMensaje, "iisss", [$idAdmin, $idUsuarioDestino, $tituloMensaje, $contenidoMensaje, 'sancion']);

            $response['success'] = true;
            $response['message'] = 'Comentario eliminado y sanción aplicada correctamente.';
        } else {
            throw new Exception("Tipo de objetivo no válido.");
        }

    } else {
        throw new Exception("Acción no válida.");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
