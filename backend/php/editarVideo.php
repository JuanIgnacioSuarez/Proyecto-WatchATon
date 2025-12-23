<?php
header('Content-Type: application/json');
require_once('../classes/Conexion.php');
$conexion = new Conexion();

$response = ['success' => false, 'message' => ''];

if (!isset($_POST['id_video']) || !isset($_POST['titulo']) || !isset($_POST['descripcion'])) {
    $response['message'] = 'Faltan datos.';
    echo json_encode($response);
    exit;
}

$idVideo = $_POST['id_video'];
$titulo = trim($_POST['titulo']);
$descripcion = trim($_POST['descripcion']);

if ($titulo == "" || $descripcion == "") {
    $response['message'] = 'El título y la descripción no pueden estar vacíos.';
    echo json_encode($response);
    exit;
}

if (strlen($titulo) > 30) {
    $response['message'] = 'El título no puede superar los 30 caracteres.';
    echo json_encode($response);
    exit;
}

if (strlen($descripcion) > 300) {
    $response['message'] = 'La descripción no puede superar los 300 caracteres.';
    echo json_encode($response);
    exit;
}

if (!isset($_COOKIE['iniciado'])) {
    $response['message'] = 'Debes iniciar sesión.';
    echo json_encode($response);
    exit;
}

$email = $_COOKIE['iniciado'];
$idUsuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

if ($idUsuario) {
    // Verificar que el video pertenezca al usuario
    $sqlCheck = "SELECT ID_video FROM videos WHERE ID_video = ? AND ID_usuario = ?";
    $checkResult = $conexion->consultar($sqlCheck, "ii", [$idVideo, $idUsuario]);

    if (count($checkResult) > 0) {
        // Recuperar la portada anterior para borrarla si se cambia
        $sqlGetOld = "SELECT public_id_portada FROM videos WHERE ID_video = ?";
        $oldData = $conexion->consultar($sqlGetOld, "i", [$idVideo]);
        $oldPublicId = $oldData[0]['public_id_portada'] ?? null;

        $newPublicIdPortada = isset($_POST['public_id_portada']) ? $_POST['public_id_portada'] : $oldPublicId;

        // Si hay una nueva portada y es diferente a la anterior, borrar la vieja
        if ($newPublicIdPortada !== $oldPublicId && !empty($oldPublicId)) {
             require_once '../../vendor/autoload.php';             
             
             $cloud_name = "dqrxdpqef";
             $api_key = "663633514336335";
             $api_secret = "QM_D1b-7s9Yy8Jj55p7qQ4Jg56o";
             
             $timestamp = time();
             $signature = sha1("public_id=" . $oldPublicId . "&timestamp=" . $timestamp . $api_secret);
             
             $ch = curl_init();
             curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . $cloud_name . "/image/destroy");
             curl_setopt($ch, CURLOPT_POST, 1);
             curl_setopt($ch, CURLOPT_POSTFIELDS, [
                 "public_id" => $oldPublicId,
                 "api_key" => $api_key,
                 "timestamp" => $timestamp,
                 "signature" => $signature
             ]);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $cloudinaryResponse = curl_exec($ch);
             curl_close($ch);
        }

        $sqlUpdate = "UPDATE videos SET Titulo = ?, Descripcion = ?, public_id_portada = ? WHERE ID_video = ?";
        if ($conexion->actualizar($sqlUpdate, "sssi", [$titulo, $descripcion, $newPublicIdPortada, $idVideo])) {
            $response['success'] = true;
            $response['message'] = 'Video actualizado correctamente.';
        } else {
            $response['message'] = 'Error al actualizar en la base de datos.';
        }
    } else {
        $response['message'] = 'No tienes permiso para editar este video.';
    }
} else {
    $response['message'] = 'Usuario no válido.';
}

echo json_encode($response);
?>
