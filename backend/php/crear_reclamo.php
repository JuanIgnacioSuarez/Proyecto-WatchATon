<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();

// Verificar sesión
if (!isset($_COOKIE['iniciado'])) {
    echo json_encode(['error' => 'No iniciado']);
    exit;
}

$email = $_COOKIE['iniciado'];
$idUsuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

if (!$idUsuario) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Validar input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_sancion'])) {
    $idSancion = intval($_POST['id_sancion']);

    // Verificar si ya existe un reclamo para esta sanción del mismo usuario
    $sqlCheck = "SELECT ID FROM Reclamos WHERE ID_Sancion = ? AND ID_Usuario = ?";
    $existe = $conexion->consultar($sqlCheck, "ii", [$idSancion, $idUsuario]);

    if (!empty($existe)) {
        echo json_encode(['success' => false, 'message' => 'Ya existe un reclamo pendiente o procesado para esta sanción.']);
        exit;
    }

    // Insertar nuevo reclamo
    // Insertar nuevo reclamo
    $sql = "INSERT INTO Reclamos (ID_Sancion, ID_Usuario) VALUES (?, ?)";
    $tipos = "ii";
    $parametros = [$idSancion, $idUsuario];
    
    if ($conexion->insertar($sql, $tipos, $parametros)) {
        echo json_encode(['success' => true, 'message' => 'Reclamo enviado correctamente. Un administrador lo revisará.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el reclamo en la base de datos.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o método incorrecto.']);
}
?>
