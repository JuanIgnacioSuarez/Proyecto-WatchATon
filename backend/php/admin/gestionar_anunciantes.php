<?php
require_once('../verificar_admin.php');
// Verificar admin (esto detiene la ejecución si no es admin)
$idAdmin = checkAdmin();

require_once('../../classes/Conexion.php');
$conexion = new Conexion();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'list':
        $sql = "SELECT * FROM anunciantes ORDER BY id DESC";
        $res = $conexion->consultar($sql);
        echo json_encode($res);
        break;

    case 'add':
        $nombre = $_POST['nombre'] ?? '';
        if (empty($nombre)) {
            echo json_encode(['success' => false, 'error' => 'Nombre requerido']);
            exit;
        }

        $nombre = mb_strtoupper($nombre, 'UTF-8');

        // Verificar duplicados
        $sqlCheck = "SELECT count(*) as total FROM anunciantes WHERE nombre = ?";
        $check = $conexion->consultar($sqlCheck, "s", [$nombre]);
        if ($check[0]['total'] > 0) {
            echo json_encode(['success' => false, 'error' => 'El anunciante ya existe']);
            exit;
        }
        
        $sql = "INSERT INTO anunciantes (nombre) VALUES (?)";
        $id = $conexion->insertar($sql, "s", [$nombre]);
        
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
        
        $sql = "DELETE FROM anunciantes WHERE id = ?";
        $res = $conexion->actualizar($sql, "i", [$id]);
        
        // Verificar si se borró (aunque actualizar devuelve true/false o filas afectadas, asumimos éxito si no hay error)
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción inválida']);
        break;
}
?>
