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
    // Obtener todas las sanciones
    // Modificado para traer ID correcto y estado del reclamo
    $sql = "SELECT s.id_sancion as id, s.motivo, s.descripcion, s.contenido_original, s.tipo, s.fecha, r.Estado as estado_reclamo 
            FROM sanciones s 
            LEFT JOIN Reclamos r ON s.id_sancion = r.ID_Sancion AND r.ID_Usuario = ?
            WHERE s.id_usuario = ? 
            ORDER BY s.fecha DESC";
    $tipos = "ii";
    $parametros = [$idUsuario, $idUsuario];
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
