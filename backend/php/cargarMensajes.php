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
            WHERE id_destinatario = ? OR id_destinatario = 0 
            ORDER BY fecha DESC";
    $tipos = "i";
    $parametros = [$idUsuario];
    $mensajes = $conexion->consultar($sql, $tipos, $parametros);

    // Contar no leídos (solo personales, o globales si implementamos tracking de leídos para globales, 
    // pero por ahora contaremos todos los que tengan leido=0 y sean para el usuario, 
    // para globales es más complejo trackear leido por usuario sin una tabla intermedia, 
    // así que asumiremos que los globales siempre se muestran o no suman al contador personal si es simple)
    
    // Simplificación: Contamos no leídos solo de los personales para el badge
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
