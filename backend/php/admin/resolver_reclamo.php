<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

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
$usuario = $conexion->consultar("SELECT ID, Permisos FROM usuarios WHERE Correo = ?", "s", [$email]);

if (empty($usuario) || $usuario[0]['Permisos'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$idAdmin = (int)$usuario[0]['ID'];

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
        $patronTitulo = "%" . $contenidoOriginal . "%";
        $sqlBorrarMsj = "DELETE FROM mensajes WHERE id_destinatario = ? AND tipo = 1 AND (titulo LIKE ? OR contenido LIKE ?)";
        $conexion->eliminar($sqlBorrarMsj, "iss", [$idUsuario, $patronTitulo, $patronTitulo]);

        // C. Eliminar Sanción
        $conexion->eliminar("DELETE FROM sanciones WHERE id_sancion = ?", "i", [$idSancion]);
        
        // D. Eliminar el Reclamo (Ya no tiene sentido que exista si la sanción no existe)
        $resultado = $conexion->eliminar("DELETE FROM Reclamos WHERE ID = ?", "i", [$id_reclamo]);
        
        // E. Notificar al usuario (Tipo 2 = Aprobado)
        $tituloMsg = "Reclamo Aceptado";
        $contenidoMsg = "Tu reclamo ha sido revisado y aceptado. La sanción ha sido revocada y tu contenido restaurado. Lamentamos las molestias.";
        try {
            $conexion->insertar("INSERT INTO mensajes (id_remitente, id_destinatario, titulo, contenido, tipo) VALUES (?, ?, ?, ?, 2)", "iiss", [$idAdmin, (int)$idUsuario, $tituloMsg, $contenidoMsg]);
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../../debug/resolver_reclamo.log', date('Y-m-d H:i:s') . " ERROR INSERT tipo 2: " . $e->getMessage() . "\n", FILE_APPEND);
        }

        $msgExito = 'Reclamo aceptado. Sanción revocada y contenido restaurado.';

    } else {
        // --- CASO RECHAZAR: Persistir Reclamo con estado Rechazado ---
        // La sanción se mantiene. El reclamo queda como historial y bloqueo.
        $resultado = $conexion->actualizar("UPDATE Reclamos SET Estado = 'Rechazado' WHERE ID = ?", "i", [$id_reclamo]);
        
        // Notificar al usuario (Tipo 3 = Rechazado)
        $tituloMsg = "Reclamo Rechazado";
        $contenidoMsg = "Tu reclamo ha sido revisado y rechazado. La sanción se mantiene vigente tras la revisión administrativa.";
        try {
            $conexion->insertar("INSERT INTO mensajes (id_remitente, id_destinatario, titulo, contenido, tipo) VALUES (?, ?, ?, ?, 3)", "iiss", [$idAdmin, (int)$idUsuario, $tituloMsg, $contenidoMsg]);
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../../debug/resolver_reclamo.log', date('Y-m-d H:i:s') . " ERROR INSERT tipo 3: " . $e->getMessage() . "\n", FILE_APPEND);
        }

        $msgExito = 'Reclamo rechazado. El estado ha sido actualizado.';
    }
    
    // Cerrar conexión auth
    if (isset($connection)) {
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
