<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../vendor/autoload.php';

use Cloudinary\Cloudinary;

$response = ['success' => false, 'message' => ''];

if (!isset($_POST['ID']) || empty($_POST['ID'])) {
    $response['message'] = 'ID del archivo no proporcionado.';
    echo json_encode($response);
    exit();
}

$public_id = $_POST['ID'];

try {
    // Configuración de Cloudinary directamente en el constructor
    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => 'dqrxdpqef',
            'api_key'    => '175642324611446',
            'api_secret' => 'mZQ1yBbkRrn8LVvQjL_dhCwO4rc'
        ]
    ]);

    // Eliminar el recurso (imagen) de Cloudinary
    $result = $cloudinary->uploadApi()->destroy($public_id, ['resource_type' => 'image']);

    if (isset($result['result']) && $result['result'] === 'ok') {
        $response['success'] = true;
        $response['message'] = 'Imagen eliminada de Cloudinary con éxito.';
    } else {
        $response['message'] = 'Error al eliminar la imagen de Cloudinary: ' . ($result['error']['message'] ?? 'Desconocido');
    }
} catch (Exception $e) {
    $response['message'] = 'Excepción al eliminar la imagen de Cloudinary: ' . $e->getMessage();
}

echo json_encode($response);

?>
