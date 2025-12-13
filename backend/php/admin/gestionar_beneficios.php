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
            // Listar beneficios con su tipo
            // Se asume que la tabla beneficios tiene: ID_beneficio, id_tipo, Descripcion, Valor, enlace
            // Y tipo_beneficio tiene: id_tipo, descripcion (o nombre)
            // Ajustar nombres de columnas según esquema real.
            $sql = "SELECT b.ID_beneficio, b.Descripcion, b.Valor, b.enlace, b.id_tipo, t.descripcion as nombre_tipo 
                    FROM beneficios b
                    INNER JOIN tipo_beneficio t ON b.id_tipo = t.id_tipo
                    ORDER BY b.ID_beneficio DESC";
            $data = $conexion->consultar($sql);
            echo json_encode($data);
            exit; // Salir directamente para arrays

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

            if (empty($id_tipo) || empty($descripcion) || empty($valor) || empty($enlace)) {
                throw new Exception("Faltan datos obligatorios.");
            }

            $sql = "INSERT INTO beneficios (id_tipo, Descripcion, Valor, enlace) VALUES (?, ?, ?, ?)";
            $params = [$id_tipo, $descripcion, $valor, $enlace];
            $types = "isis"; // int, string, int, string

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

            if (empty($id) || empty($id_tipo) || empty($descripcion) || empty($valor) || empty($enlace)) {
                throw new Exception("Faltan datos obligatorios.");
            }

            $sql = "UPDATE beneficios SET id_tipo = ?, Descripcion = ?, Valor = ?, enlace = ? WHERE ID_beneficio = ?";
            $params = [$id_tipo, $descripcion, $valor, $enlace, $id];
            $types = "isisi";

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
