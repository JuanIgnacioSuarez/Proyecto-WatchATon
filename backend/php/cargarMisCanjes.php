<?php
require_once('../classes/Conexion.php');

$conexion = new Conexion();

if (!isset($_COOKIE['iniciado'])) {
    echo json_encode(['error' => 'No has iniciado sesión']);
    exit;
}

$id_usuario = $conexion->existeDato('usuarios', 'ID', 'correo', $_COOKIE['iniciado']);

if (!$id_usuario) {
    echo json_encode(['error' => 'Usuario no válido']);
    exit;
}

// Consultar canjes con detalles del beneficio
$sql = "SELECT c.Fecha, b.Descripcion, b.Valor, t.descripcion as Tipo, b.enlace
        FROM canjeos c
        INNER JOIN beneficios b ON c.ID_beneficio = b.ID_beneficio
        INNER JOIN tipo_beneficio t ON b.id_tipo = t.id_tipo
        WHERE c.ID_usuario = ?
        ORDER BY c.Fecha DESC";

$canjes = $conexion->consultar($sql, "i", [$id_usuario]);

// Si no hay canjes, devolver array vacío en lugar de error
if (empty($canjes)) {
    echo json_encode([]);
    exit;
}

// Formatear fecha para mostrar mejor (Opcional, se puede hacer en JS)
foreach ($canjes as &$canje) {
    $canje['Fecha'] = date("d/m/Y H:i", strtotime($canje['Fecha']));
}

header('Content-Type: application/json');
echo json_encode($canjes);
?>
