<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../vendor/autoload.php';

use Cloudinary\Cloudinary;


$response = ['success' => false, 'message' => ''];

if (!isset($_POST["ID"]) || empty($_POST["ID"])) {
    $response['message'] = 'ID de video no proporcionado.';
    echo json_encode($response);
    exit();
}

$ID = $_POST["ID"];

try {
    // Configuración de Cloudinary directamente en el constructor
    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => 'dqrxdpqef',
            'api_key'    => '175642324611446',
            'api_secret' => 'mZQ1yBbkRrn8LVvQjL_dhCwO4rc'
        ]
    ]);

    $result = $cloudinary->uploadApi()->destroy($ID, [
        'resource_type' => 'video'
    ]);

    if (isset($result['result']) && $result['result'] === 'ok') {
        $response['success'] = true;
        $response['message'] = 'Video eliminado de Cloudinary correctamente.';
    } else {
        $response['message'] = 'Error al eliminar video de Cloudinary: ' . ($result['error']['message'] ?? 'Desconocido');
    }
} catch (Exception $e) {
    $response['message'] = 'Excepción al eliminar video: ' . $e->getMessage();
}

echo json_encode($response);

?>