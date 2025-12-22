<?php
header('Content-Type: application/json');
require_once('../../classes/Conexion.php');

// Validar Admin
if (!isset($_COOKIE['iniciado'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$conexion = new Conexion();
$email = $_COOKIE['iniciado'];
$usuario = $conexion->consultar("SELECT Permisos FROM usuarios WHERE Correo = ?", "s", [$email]);

if (empty($usuario) || $usuario[0]['Permisos'] != 1) {
    echo json_encode(['error' => 'No tienes permisos de administrador']);
    exit;
}

// Acción: Listar
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $accion = $_GET['accion'] ?? 'list';

    if ($accion === 'list') {
        // Obtenemos: id reclamo, fecha reclamo, estado reclamo, usuario (id, nombre, public_id), 
        // sancion (motivo, tipo, id_objeto, tipo_objeto), video (public_id para thumbnail/link)
        // NOTA: Para tipo=comentario, no tenemos el public_id del video directamente en sanciones (solo id_objeto que es id_comentario).
        // Sería complejo hacer join a comentarios y luego videos en una sola query si tipo_objeto varía.
        // Haremos la query principal y si es necesario resolveremos datos extra, o asumiendo que el User prioritario pidio "si es un video".

        $search_id = isset($_GET['search_id']) ? intval($_GET['search_id']) : 0;

        $sql = "SELECT 
                    r.ID as id_reclamo,
                    r.Fecha as fecha_reclamo,
                    r.Estado as estado_reclamo,
                    u.ID as id_usuario,
                    u.nombre_usuario,
                    u.public_id_perfil,
                    s.id_sancion,
                    s.motivo,
                    s.descripcion as desc_sancion,
                    s.tipo as tipo_sancion,
                    s.id_objeto,
                    s.tipo_objeto,
                    s.contenido_original,
                    v.public_id as video_public_id, -- SÓLO SI ES VIDEO
                    v.Titulo as video_titulo
                FROM Reclamos r
                JOIN sanciones s ON r.ID_Sancion = s.id_sancion
                JOIN usuarios u ON r.ID_Usuario = u.ID
                LEFT JOIN videos v ON (s.tipo_objeto = 'video' AND s.id_objeto = v.ID_video)";
        
        $params = [];
        $types = "";

        if ($search_id > 0) {
            $sql .= " WHERE u.ID = ?";
            $params[] = $search_id;
            $types .= "i";
        }

        $sql .= " ORDER BY r.Fecha DESC";
        
        $reclamos = $conexion->consultar($sql, $types, $params);
        echo json_encode($reclamos);
    }
}
?>
