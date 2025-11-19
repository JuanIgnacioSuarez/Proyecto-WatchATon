<?php
require_once('../classes/Conexion.php');

$conexion = new Conexion();

// Obtener el ID del usuario actual desde la cookie (asumiendo que se guarda aquí)
$correo_usuario_actual = isset($_COOKIE['iniciado']) ? $_COOKIE['iniciado'] : null;
$id_usuario_actual_db = null;

if ($correo_usuario_actual) {
    // Buscar el ID del usuario en la base de datos usando el correo
    $sql_get_user_id = "SELECT ID FROM usuarios WHERE Correo = ?";
    $result_user_id = $conexion->consultar($sql_get_user_id, "s", [$correo_usuario_actual]);
    if (count($result_user_id) > 0) {
        $id_usuario_actual_db = $result_user_id[0]['ID'];
    }
}

// Buscar comentarios y su usuario para ese video, incluyendo el ID del comentario y el ID del usuario del comentario
$sql = "SELECT c.id_comentario, c.contenido, c.id_usuario, u.Correo FROM comentarios c INNER JOIN usuarios u ON c.id_usuario = u.ID WHERE c.id_video = ? ORDER BY c.id_comentario DESC";

$tipos = "d";
$parametros = [$_POST['idVideo']];
$resultado = $conexion->consultar($sql, $tipos, $parametros);

$comentarios_json = [];

if (count($resultado) > 0) {  //Si existen comentarios en el video
    foreach ($resultado as $i) {
        $es_autor = ($id_usuario_actual_db !== null && $id_usuario_actual_db == $i['id_usuario']);
        $comentarios_json[] = [
            'id_comentario' => $i['id_comentario'],
            'contenido' => htmlspecialchars($i['contenido']),
            'correo' => htmlspecialchars($i['Correo']),
            'es_autor' => $es_autor
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($comentarios_json);

$conexion->cerrarConexion();
?>