<?php
// Archivo: backend/php/admin/gestionar_beneficios.php
// Propósito: API para listar, crear, editar y eliminar beneficios

require_once('../../classes/Conexion.php');
require_once('../verificar_admin.php');

// Verificar sesión de admin
checkAdmin();

$conexion = new Conexion();
$accion = $_REQUEST['accion'] ?? '';

// Respuesta por defecto
$response = ['success' => false, 'error' => 'Acción no válida'];

try {
    switch ($accion) {
        case 'list':
            // Listar beneficios con su tipo y duración
            $sql = "SELECT b.ID_beneficio, b.Descripcion, b.Valor, b.enlace, b.id_tipo, b.dias_duracion, t.descripcion as nombre_tipo 
                    FROM beneficios b
                    INNER JOIN tipo_beneficio t ON b.id_tipo = t.id_tipo
                    ORDER BY b.ID_beneficio DESC";
            $data = $conexion->consultar($sql);
            echo json_encode($data);
            exit; 

        case 'get_types':
            $sql = "SELECT id_tipo, descripcion FROM tipo_beneficio ORDER BY descripcion ASC";
            $data = $conexion->consultar($sql);
            echo json_encode($data);
            exit;

        case 'add':
            $id_tipo = $_POST['id_tipo'] ?? '';
            $descripcion = trim($_POST['descripcion'] ?? '');
            $valor = $_POST['valor'] ?? '';
            $enlace = trim($_POST['enlace'] ?? '');
            // Duracion es opcional (null = infinito)
            $dias_duracion = isset($_POST['dias_duracion']) && $_POST['dias_duracion'] !== '' ? $_POST['dias_duracion'] : null;

            if (empty($id_tipo) || empty($descripcion) || empty($valor) || empty($enlace)) {
                throw new Exception("Faltan datos obligatorios.");
            }

            $sql = "INSERT INTO beneficios (id_tipo, Descripcion, Valor, enlace, dias_duracion) VALUES (?, ?, ?, ?, ?)";
            $params = [$id_tipo, $descripcion, $valor, $enlace, $dias_duracion];
            $types = "isisi"; // int, string, int, string, int

            $res = $conexion->actualizar($sql, $types, $params);
            if ($res) {
                $response = ['success' => true, 'message' => 'Beneficio agregado correctamente.'];
            } else {
                throw new Exception("Error al insertar en la base de datos.");
            }
            break;

        case 'edit':
            $id = $_POST['id'] ?? '';
            $id_tipo = $_POST['id_tipo'] ?? '';
            $descripcion = trim($_POST['descripcion'] ?? '');
            $valor = $_POST['valor'] ?? '';
            $enlace = trim($_POST['enlace'] ?? '');
            $dias_duracion = isset($_POST['dias_duracion']) && $_POST['dias_duracion'] !== '' ? $_POST['dias_duracion'] : null;

            // Debug logging
            file_put_contents(__DIR__ . '/../../debug/debug_beneficios.log', print_r($_POST, true), FILE_APPEND);

            $missing = [];
            if ($id === '') $missing[] = 'ID';
            if ($id_tipo === '') $missing[] = 'Tipo';
            if ($descripcion === '') $missing[] = 'Descripción';
            if ($valor === '') $missing[] = 'Valor';
            if ($enlace === '') $missing[] = 'Enlace';

            if (!empty($missing)) {
                throw new Exception("Faltan datos obligatorios: " . implode(', ', $missing));
            }

            $sql = "UPDATE beneficios SET id_tipo = ?, Descripcion = ?, Valor = ?, enlace = ?, dias_duracion = ? WHERE ID_beneficio = ?";
            $params = [$id_tipo, $descripcion, $valor, $enlace, $dias_duracion, $id];
            $types = "isisii"; // int, string, int, string, int, int

            $res = $conexion->actualizar($sql, $types, $params);
            if ($res) {
                $response = ['success' => true, 'message' => 'Beneficio actualizado correctamente.'];
            } else {
                throw new Exception("No se realizaron cambios o error en BD.");
            }
            break;

        case 'delete':
            $id = $_POST['id'] ?? '';
            if (empty($id)) throw new Exception("ID no proporcionado.");

            $sql = "DELETE FROM beneficios WHERE ID_beneficio = ?";
            $res = $conexion->actualizar($sql, "i", [$id]);
            
            if ($res) {
                $response = ['success' => true, 'message' => 'Beneficio eliminado correctamente.'];
            } else {
                throw new Exception("Error al eliminar.");
            }
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
}

echo json_encode($response);
?>
