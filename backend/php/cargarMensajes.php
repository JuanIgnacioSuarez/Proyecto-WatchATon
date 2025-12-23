<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();

if (!isset($_COOKIE['iniciado'])) {
    echo json_encode(['error' => 'No iniciado']);
    exit;
}

$email = $_COOKIE['iniciado'];
$idUsuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

if ($idUsuario) {
    // Obtener mensajes personales y globales (id_destinatario = 0 o NULL)
    // Asumimos que los mensajes globales tienen id_destinatario = 0
    $sql = "SELECT id_mensaje, titulo, contenido, tipo, fecha, leido, id_destinatario 
            FROM mensajes 
            WHERE id_destinatario = ? OR id_destinatario = 0 OR id_destinatario IS NULL 
            ORDER BY fecha DESC";
    $tipos = "i";
    $parametros = [$idUsuario];
    $mensajes = $conexion->consultar($sql, $tipos, $parametros);

    //Contamos no leídos solo de los personales para el badge
    $unreadCount = 0;
    foreach ($mensajes as $m) {
        if ($m['leido'] == 0 && $m['id_destinatario'] != 0) {
            $unreadCount++;
        }
    }

    echo json_encode([
        'mensajes' => $mensajes,
        'unreadCount' => $unreadCount
    ]);
} else {
    echo json_encode(['error' => 'Usuario no encontrado']);
}
?>
