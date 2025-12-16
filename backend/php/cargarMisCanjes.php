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
// Filtramos los canjes que tienen un pago asociado (para no mostrarlos como canjes de puntos)
$sql = "SELECT c.Fecha, b.Descripcion, b.Valor, t.descripcion as Tipo, b.enlace, c.activo, c.fecha_vencimiento
        FROM canjeos c
        INNER JOIN beneficios b ON c.ID_beneficio = b.ID_beneficio
        INNER JOIN tipo_beneficio t ON b.id_tipo = t.id_tipo
        LEFT JOIN pagos p ON c.ID_canjeo = p.id_canje
        WHERE c.ID_usuario = ? AND p.id_pago IS NULL
        ORDER BY c.fecha DESC";

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
