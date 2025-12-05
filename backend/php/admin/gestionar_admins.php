<?php
require_once('../verificar_admin.php');
$idAdmin = checkAdmin();

require_once('../../classes/Conexion.php');
$conexion = new Conexion();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'list':
        $sql = "SELECT ID, nombre_usuario, Correo FROM usuarios WHERE Permisos = 1";
        $res = $conexion->consultar($sql);
        echo json_encode($res);
        break;

    case 'create':
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($username) || empty($email)) {
            echo json_encode(['success' => false, 'error' => 'Faltan datos']);
            exit;
        }

        // Verificar si correo existe
        $existe = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);
        if ($existe) {
            echo json_encode(['success' => false, 'error' => 'El correo ya está registrado']);
            exit;
        }

        // Insertar con Permisos = 1
        $sql = "INSERT INTO usuarios (nombre_usuario, Correo, Permisos) VALUES (?, ?, 1)";
        $id = $conexion->insertar($sql, "ss", [$username, $email]);

        if ($id) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al crear admin']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción inválida']);
        break;
}
?>
