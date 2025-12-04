<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../vendor/autoload.php';
require_once('../classes/Conexion.php');

use Cloudinary\Cloudinary;

$response = ['success' => false, 'message' => ''];

if (!isset($_POST['id_video']) || empty($_POST['id_video'])) {
    $response['message'] = 'ID de video no proporcionado.';
    echo json_encode($response);
    exit();
}

$idVideo = $_POST['id_video'];
$conexion = new Conexion();

// 1. Obtener los public_id de Cloudinary desde la base de datos
$sql = "SELECT public_id, public_id_portada FROM videos WHERE ID_video = ?";
$resultado = $conexion->consultar($sql, "i", [$idVideo]);

if (count($resultado) > 0) {
    $videoData = $resultado[0];
    $publicIdVideo = $videoData['public_id'];
    $publicIdPortada = $videoData['public_id_portada'];

    try {
        // Configuración de Cloudinary
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => 'dqrxdpqef',
                'api_key'    => '175642324611446',
                'api_secret' => 'mZQ1yBbkRrn8LVvQjL_dhCwO4rc'
            ]
        ]);

        // 2. Eliminar video de Cloudinary
        if ($publicIdVideo) {
            $cloudinary->uploadApi()->destroy($publicIdVideo, ['resource_type' => 'video']);
        }

        // 3. Eliminar portada de Cloudinary
        if ($publicIdPortada) {
            $cloudinary->uploadApi()->destroy($publicIdPortada, ['resource_type' => 'image']);
        }

        // 4. Eliminar registro de la base de datos
        $sqlDelete = "DELETE FROM videos WHERE ID_video = ?";
        if ($conexion->eliminar($sqlDelete, "i", [$idVideo])) {
            $response['success'] = true;
            $response['message'] = 'Video eliminado correctamente.';
        } else {
            $response['message'] = 'Error al eliminar el registro de la base de datos.';
        }

    } catch (Exception $e) {
        $response['message'] = 'Error al eliminar recursos de Cloudinary: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Video no encontrado.';
}

echo json_encode($response);
?>
