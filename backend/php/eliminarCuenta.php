<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../vendor/autoload.php';
require_once('../classes/Conexion.php');

use Cloudinary\Cloudinary;

$response = ['success' => false, 'message' => ''];

if (!isset($_COOKIE['iniciado'])) {
    $response['message'] = 'No hay sesión iniciada.';
    echo json_encode($response);
    exit();
}

$email = $_COOKIE['iniciado'];
$conexion = new Conexion();

try {
    // 1. Obtener ID del usuario
    $sqlUser = "SELECT ID FROM usuarios WHERE Correo = ?";
    $resUser = $conexion->consultar($sqlUser, "s", [$email]);

    if (count($resUser) === 0) {
        throw new Exception("Usuario no encontrado.");
    }

    $idUsuario = $resUser[0]['ID'];

    // 2. Obtener videos del usuario para borrar de Cloudinary
    $sqlVideos = "SELECT public_id, public_id_portada FROM videos WHERE ID_usuario = ?";
    $videos = $conexion->consultar($sqlVideos, "i", [$idUsuario]);

    // Configuración Cloudinary
    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => 'dqrxdpqef',
            'api_key'    => '175642324611446',
            'api_secret' => 'mZQ1yBbkRrn8LVvQjL_dhCwO4rc'
        ]
    ]);

    // 3. Borrar videos y portadas de Cloudinary
    foreach ($videos as $video) {
        if (!empty($video['public_id'])) {
            try {
                $cloudinary->uploadApi()->destroy($video['public_id'], ['resource_type' => 'video']);
            } catch (Exception $e) {
                // Ignorar error individual para seguir borrando
            }
        }
        if (!empty($video['public_id_portada'])) {
            try {
                $cloudinary->uploadApi()->destroy($video['public_id_portada'], ['resource_type' => 'image']);
            } catch (Exception $e) {
                // Ignorar error individual
            }
        }
    }

    // 3.1 Borrar foto de perfil de Cloudinary
    $sqlProfile = "SELECT public_id_perfil FROM usuarios WHERE ID = ?";
    $resProfile = $conexion->consultar($sqlProfile, "i", [$idUsuario]);
    
    if (count($resProfile) > 0 && !empty($resProfile[0]['public_id_perfil'])) {
        try {
            $cloudinary->uploadApi()->destroy($resProfile[0]['public_id_perfil'], ['resource_type' => 'image']);
        } catch (Exception $e) {
            // Ignorar error
        }
    }

    // 4. Borrar usuario de la BD (Debería borrar videos en cascada si está configurado, pero lo hacemos explícito)
    // Borrar videos primero
    $conexion->eliminar("DELETE FROM videos WHERE ID_usuario = ?", "i", [$idUsuario]);
    
    // Borrar usuario
    if ($conexion->eliminar("DELETE FROM usuarios WHERE ID = ?", "i", [$idUsuario])) {
        // Cerrar sesión (borrar cookie)
        setcookie("iniciado", "", time() - 3600, "/");
        $response['success'] = true;
        $response['message'] = 'Cuenta eliminada correctamente.';
    } else {
        throw new Exception("Error al eliminar usuario de la base de datos.");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
