<?php
require_once('../classes/Conexion.php');

if (!isset($_COOKIE['iniciado'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
    exit;
}

$conexion = new Conexion();
$email = $_COOKIE['iniciado'];
$id_usuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

if (!$id_usuario) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'update_photo') {
    $public_id = $_POST['public_id'] ?? '';
    
    if (empty($public_id)) {
        echo json_encode(['success' => false, 'message' => 'ID de imagen inválido.']);
        exit;
    }

    // 1. Obtener la foto actual para borrarla de Cloudinary
    $sqlGetOld = "SELECT public_id_perfil FROM usuarios WHERE ID = ?";
    $tiposGetOld = "i";
    $parametrosGetOld = [$id_usuario];
    $resultadoOld = $conexion->consultar($sqlGetOld, $tiposGetOld, $parametrosGetOld);
    
    $old_public_id = $resultadoOld[0]['public_id_perfil'] ?? null;

    // 2. Si existe una foto anterior, borrarla
    if ($old_public_id) {
        try {
            require_once __DIR__ . '/../../vendor/autoload.php';
            $cloudinary = new Cloudinary\Cloudinary([
                'cloud' => [
                    'cloud_name' => 'dqrxdpqef',
                    'api_key'    => '175642324611446',
                    'api_secret' => 'mZQ1yBbkRrn8LVvQjL_dhCwO4rc'
                ]
            ]);
            $cloudinary->uploadApi()->destroy($old_public_id, ['resource_type' => 'image']);
        } catch (Exception $e) {
            // Si falla el borrado, seguimos con la actualización pero podríamos loguear el error
            // error_log("Error al borrar imagen antigua: " . $e->getMessage());
        }
    }

    $sql = "UPDATE usuarios SET public_id_perfil = ? WHERE ID = ?";
    $tipos = "si";
    $parametros = [$public_id, $id_usuario];

    if ($conexion->insertar($sql, $tipos, $parametros)) {
        echo json_encode(['success' => true, 'message' => 'Foto de perfil actualizada.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la base de datos.']);
    }

} elseif ($action === 'update_info') {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $biografia = $_POST['biografia'] ?? '';

    if (empty($nombre_usuario)) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario no puede estar vacío.']);
        exit;
    }

    if (strlen($nombre_usuario) > 50) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario es demasiado largo.']);
        exit;
    }

    if (strlen($biografia) > 500) {
        echo json_encode(['success' => false, 'message' => 'La biografía no puede superar los 500 caracteres.']);
        exit;
    }

    // Verificar si el nombre de usuario ya existe para otro usuario
    $sqlCheck = "SELECT ID FROM usuarios WHERE nombre_usuario = ? AND ID != ?";
    $tiposCheck = "si";
    $parametrosCheck = [$nombre_usuario, $id_usuario];
    $resultadoCheck = $conexion->consultar($sqlCheck, $tiposCheck, $parametrosCheck);

    if (!empty($resultadoCheck)) {
        echo json_encode(['success' => false, 'message' => 'Este nombre de usuario ya está en uso.']);
        exit;
    }

    $sql = "UPDATE usuarios SET nombre_usuario = ?, biografia = ? WHERE ID = ?";
    $tipos = "ssi";
    $parametros = [$nombre_usuario, $biografia, $id_usuario];

    if ($conexion->insertar($sql, $tipos, $parametros)) {
        echo json_encode(['success' => true, 'message' => 'Información actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la información.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
}
?>
