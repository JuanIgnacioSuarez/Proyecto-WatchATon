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

    // 1. Obtener detalles del reclamo y la sanción ANTES de hacer nada
    $sqlDetalles = "SELECT r.ID_Sancion, s.id_usuario, s.id_objeto, s.tipo_objeto, s.contenido_original 
                    FROM Reclamos r 
                    JOIN sanciones s ON r.ID_Sancion = s.id_sancion 
                    WHERE r.ID = ?";
    $detalles = $conexion->consultar($sqlDetalles, "i", [$id_reclamo]);

    if (empty($detalles)) {
        // Reclamo no existe o sancion borrada, borrar solo el reclamo si existe
        $conexion->eliminar("DELETE FROM Reclamos WHERE ID = ?", "i", [$id_reclamo]);
        echo json_encode(['success' => true, 'message' => 'Reclamo eliminado (sin datos asociados)']);
        exit;
    }

    $idSancion = $detalles[0]['ID_Sancion'];
    $idUsuario = $detalles[0]['id_usuario'];
    $idObjeto = $detalles[0]['id_objeto'];
    $tipoObjeto = $detalles[0]['tipo_objeto'];
    $contenidoOriginal = $detalles[0]['contenido_original'];

    if ($accion === 'aceptar') {
        // --- CASO ACEPTAR: Eliminar Sanción, Mensaje, Restaurar Contenido ---

        // A. Restaurar contenido (Video o Comentario)
        if ($tipoObjeto === 'video' && $idObjeto) {
            $conexion->actualizar("UPDATE videos SET sancionado = 0 WHERE ID_video = ?", "i", [$idObjeto]);
        } elseif ($tipoObjeto === 'comentario' && $idObjeto) {
            $conexion->actualizar("UPDATE comentarios SET sancionado = 0 WHERE id_comentario = ?", "i", [$idObjeto]);
        }

        // B. Eliminar Mensaje de Notificación
        // Buscamos mensajes de tipo 'sancion' enviados al usuario. 
        // Como no tenemos FK directa, usamos heurística: coincidencias de título o destinatario reciente.
        // Simplificación: Borrar mensajes de tipo 'sancion' para este usuario que mencionen el contenido.
        // Ojo: Esto podría borrar otros mensajes si el titulo es muy genérico, pero es lo mejor posible sin ID.
        // Título usual: "Video Eliminado: [Titulo]" o "Comentario Eliminado"
        $patronTitulo = "%" . $contenidoOriginal . "%";
        $sqlBorrarMsj = "DELETE FROM mensajes WHERE id_destinatario = ? AND tipo = 'sancion' AND (titulo LIKE ? OR contenido LIKE ?)";
        // Para comentarios, el titulo original no está en el titulo del mensaje ("Comentario Eliminado").
        // Pero el contenido del mensaje tiene el comentario. "Tu comentario '...' ".
        // Hacemos un intento de borrado.
        $conexion->eliminar($sqlBorrarMsj, "iss", [$idUsuario, $patronTitulo, $patronTitulo]);

        // C. Eliminar Sanción
        $conexion->eliminar("DELETE FROM sanciones WHERE id_sancion = ?", "i", [$idSancion]);
        
        $msgExito = 'Reclamo aceptado. Sanción revocada y contenido restaurado.';

    } else {
        // --- CASO RECHAZAR: Solo borrar el reclamo ---
        // La sanción se mantiene firme.
        $msgExito = 'Reclamo rechazado y eliminado.';
    }

    // 2. Eliminar el Reclamo (Común para ambos casos, al final)
    $resultado = $conexion->eliminar("DELETE FROM Reclamos WHERE ID = ?", "i", [$id_reclamo]);
    
    // Cerrar conexión auth
    if (isset($connection)) {
         // La instancia $conexion no expone close() directamente si reusamos, pero Conexion::cerrarConexion() si.
         $conexion->cerrarConexion();
    }

    if ($resultado) {
        echo json_encode(['success' => true, 'message' => $msgExito]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al procesar el reclamo']);
    }

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
