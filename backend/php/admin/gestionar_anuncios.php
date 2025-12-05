<?php
require_once('../verificar_admin.php');
$idAdmin = checkAdmin();

require_once __DIR__ . '/../../../vendor/autoload.php';
use Cloudinary\Cloudinary;

require_once('../../classes/Conexion.php');
$conexion = new Conexion();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'list':
        $idAnunciante = $_GET['id_anunciante'] ?? '';

        $sql = "SELECT a.ID_anuncio, a.public_id, a.Url, a.nombre, an.nombre as nombre_anunciante 
                FROM anuncios a 
                LEFT JOIN anunciantes an ON a.id_anunciante = an.id";
        
        $params = [];
        $types = "";

        if (!empty($idAnunciante)) {
            $sql .= " WHERE a.id_anunciante = ?";
            $params[] = $idAnunciante;
            $types .= "i";
        }
        
        $sql .= " ORDER BY a.ID_anuncio DESC";

        if (!empty($params)) {
             $res = $conexion->consultar($sql, $types, $params);
        } else {
             $res = $conexion->consultar($sql);
        }
        
        echo json_encode($res);
        break;

    case 'add':
        $id_anunciante = $_POST['id_anunciante'] ?? '';
        $public_id = $_POST['public_id'] ?? '';
        $url = $_POST['url'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        if (empty($id_anunciante) || empty($public_id) || empty($url) || empty($nombre)) {
            echo json_encode(['success' => false, 'error' => 'Faltan datos']);
            exit;
        }

        $nombre = mb_strtoupper($nombre, 'UTF-8');

        // Verificar duplicados de nombre de anuncio
        $sqlCheck = "SELECT count(*) as total FROM anuncios WHERE nombre = ?";
        $check = $conexion->consultar($sqlCheck, "s", [$nombre]);
        if ($check[0]['total'] > 0) {
            echo json_encode(['success' => false, 'error' => 'El nombre del anuncio ya existe']);
            exit;
        }
        
        $sql = "INSERT INTO anuncios (public_id, Url, id_anunciante, nombre) VALUES (?, ?, ?, ?)";
        $id = $conexion->insertar($sql, "ssis", [$public_id, $url, $id_anunciante, $nombre]);
        
        if ($id) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al insertar']);
        }
        break;

    case 'delete':
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'ID requerido']);
            exit;
        }
        
        // Obtener public_id para borrar de Cloudinary
        $sqlInfo = "SELECT public_id FROM anuncios WHERE ID_anuncio = ?";
        $info = $conexion->consultar($sqlInfo, "i", [$id]);

        if (!empty($info) && !empty($info[0]['public_id'])) {
            $public_id = $info[0]['public_id'];
            
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => 'dqrxdpqef',
                        'api_key'    => '175642324611446',
                        'api_secret' => 'mZQ1yBbkRrn8LVvQjL_dhCwO4rc'
                    ]
                ]);

                $cloudinary->uploadApi()->destroy($public_id, [
                    'resource_type' => 'video'
                ]);
            } catch (Exception $e) {
                // Si falla Cloudinary, seguimos para borrar de la BD al menos
                // error_log("Error Cloudinary: " . $e->getMessage());
            }
        }

        $sql = "DELETE FROM anuncios WHERE ID_anuncio = ?";
        $res = $conexion->actualizar($sql, "i", [$id]);
        
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción inválida']);
        break;
}
?>
