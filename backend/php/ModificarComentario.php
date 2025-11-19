<?php
require_once('../classes/Conexion.php');
session_start();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Error desconocido.'];

if (!isset($_COOKIE['iniciado'])) {
    $response['message'] = 'Usuario no autenticado.';
    echo json_encode($response);
    exit();
}

if (!isset($_POST['id_comentario']) || !isset($_POST['nuevo_comentario'])) {
    $response['message'] = 'Datos incompletos para modificar el comentario.';
    echo json_encode($response);
    exit();
}

$correo_usuario_actual = $_COOKIE['iniciado'];
$id_comentario = $_POST['id_comentario'];
$nuevo_comentario = trim($_POST['nuevo_comentario']);

if (empty($nuevo_comentario)) {
    $response['message'] = 'El comentario no puede estar vacío.';
    echo json_encode($response);
    exit();
}

// Verificar longitud máxima (asumiendo 200 caracteres como en el frontend)
if (strlen($nuevo_comentario) > 200) {
    $response['message'] = 'El comentario supera el límite de 200 caracteres.';
    echo json_encode($response);
    exit();
}

$conexion = new Conexion();

// Obtener el ID del usuario actual desde la base de datos usando el correo
$sql_get_user_id = "SELECT ID FROM usuarios WHERE Correo = ?";
$result_user_id = $conexion->consultar($sql_get_user_id, "s", [$correo_usuario_actual]);
$id_usuario_actual_db = null;

if (count($result_user_id) > 0) {
    $id_usuario_actual_db = $result_user_id[0]['ID'];
} else {
    $response['message'] = 'Usuario no encontrado en la base de datos.';
    echo json_encode($response);
    $conexion->cerrarConexion();
    exit();
}

// Primero, verificar que el usuario actual es el autor del comentario
$sql_verificar = "SELECT id_usuario FROM comentarios WHERE id_comentario = ?";
$resultado_verificar = $conexion->consultar($sql_verificar, "d", [$id_comentario]);

if (count($resultado_verificar) === 0 || $resultado_verificar[0]['id_usuario'] != $id_usuario_actual_db) {
    $response['message'] = 'No tienes permiso para modificar este comentario.';
    echo json_encode($response);
    $conexion->cerrarConexion();
    exit();
}

// Si la verificación es exitosa, proceder a modificar
$sql_modificar = "UPDATE comentarios SET contenido = ? WHERE id_comentario = ?";
$tipos = "sd";
$parametros = [$nuevo_comentario, $id_comentario];

if ($conexion->actualizar($sql_modificar, $tipos, $parametros)) {
    $response['success'] = true;
    $response['message'] = 'Comentario modificado con éxito.';
} else {
    $response['message'] = 'Error al modificar el comentario en la base de datos.';
}

echo json_encode($response);

$conexion->cerrarConexion();
?>
