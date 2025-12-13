<?php
// backend/php/procesar_canje.php
require_once('../classes/Conexion.php');

$conexion = new Conexion();

if (!isset($_COOKIE['iniciado'])) {
    echo json_encode(['success' => false, 'error' => 'No has iniciado sesión']);
    exit;
}

$id_beneficio = $_POST['id_beneficio'] ?? '';
if (empty($id_beneficio)) {
    echo json_encode(['success' => false, 'error' => 'ID de beneficio inválido']);
    exit;
}

// Obtener Usuario
$id_usuario = $conexion->existeDato('usuarios', 'ID', 'correo', $_COOKIE['iniciado']);
$puntosUsuario = $conexion->existeDato('usuarios', 'Puntos', 'ID', $id_usuario);

// Obtener Beneficio
$sql = "SELECT Valor FROM beneficios WHERE ID_beneficio = ?";
$resBeneficio = $conexion->consultar($sql, "i", [$id_beneficio]);

if (count($resBeneficio) == 0) {
    echo json_encode(['success' => false, 'error' => 'Beneficio no encontrado']);
    exit;
}

$costo = $resBeneficio[0]['Valor'];

// Verificar Puntos
if ($puntosUsuario < $costo) {
    echo json_encode(['success' => false, 'error' => 'Puntos insuficientes']);
    exit;
}

// Verificar si ya fue canjeado (opcional, pero buena práctica si los beneficios son únicos)
// Asumimos que se puede canjear solo una vez según la lógica visual anterior checkeando canjeos
$sqlCheck = "SELECT ID_canjeo FROM canjeos WHERE ID_usuario = ? AND ID_beneficio = ?";
$check = $conexion->consultar($sqlCheck, "ii", [$id_usuario, $id_beneficio]);

if (count($check) > 0) {
    echo json_encode(['success' => false, 'error' => 'Ya canjeaste esta recompensa']);
    exit;
}

// Procesar Transacción
try {
    // 1. Restar Puntos
    $nuevosPuntos = $puntosUsuario - $costo;
    $sqlUpdate = "UPDATE usuarios SET Puntos = ? WHERE ID = ?";
    $conexion->actualizar($sqlUpdate, "ii", [$nuevosPuntos, $id_usuario]);

    // 2. Registrar Canje
    $sqlInsert = "INSERT INTO canjeos (ID_usuario, ID_beneficio, Fecha) VALUES (?, ?, NOW())";
    $conexion->actualizar($sqlInsert, "ii", [$id_usuario, $id_beneficio]);

    echo json_encode(['success' => true, 'nuevos_puntos' => $nuevosPuntos]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar: ' . $e->getMessage()]);
}
?>
