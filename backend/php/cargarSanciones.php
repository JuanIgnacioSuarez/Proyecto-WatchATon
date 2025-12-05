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
    // Obtener todas las sanciones
    $sql = "SELECT motivo, descripcion, contenido_original, tipo, fecha FROM sanciones WHERE id_usuario = ? ORDER BY fecha DESC";
    $tipos = "i";
    $parametros = [$idUsuario];
    $sanciones = $conexion->consultar($sql, $tipos, $parametros);

    // Contar sanciones activas (tipo 1)
    $activeSanctions = 0;
    foreach ($sanciones as $s) {
        if ($s['tipo'] == 1) {
            $activeSanctions++;
        }
    }

    echo json_encode([
        'sanciones' => $sanciones,
        'totalActive' => $activeSanctions
    ]);
} else {
    echo json_encode(['error' => 'Usuario no encontrado']);
}
?>
