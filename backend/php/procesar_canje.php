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

// Obtener Beneficio y duración
$sql = "SELECT Valor, dias_duracion FROM beneficios WHERE ID_beneficio = ?";
$resBeneficio = $conexion->consultar($sql, "i", [$id_beneficio]);

if (count($resBeneficio) == 0) {
    echo json_encode(['success' => false, 'error' => 'Beneficio no encontrado']);
    exit;
}

$costo = $resBeneficio[0]['Valor'];
$diasDuracion = $resBeneficio[0]['dias_duracion'];

// Verificar Puntos
if ($puntosUsuario < $costo) {
    echo json_encode(['success' => false, 'error' => 'Puntos insuficientes']);
    exit;
}

// Verificar si ya fue canjeado POR PUNTOS (excluyendo pagos)
$sqlCheck = "SELECT c.ID_canjeo 
             FROM canjeos c 
             LEFT JOIN pagos p ON c.ID_canjeo = p.id_canje 
             WHERE c.ID_usuario = ? AND c.ID_beneficio = ? AND p.id_pago IS NULL AND c.activo = 1";
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

    // 2. Registrar Canje con Vencimiento
    // Calcular fecha vencimiento si aplica
    $fechaVencimiento = null;
    if (!empty($diasDuracion) && is_numeric($diasDuracion)) {
        $fechaVencimiento = date('Y-m-d H:i:s', strtotime("+$diasDuracion days"));
    }

    $sqlInsert = "INSERT INTO canjeos (ID_usuario, ID_beneficio, Fecha, fecha_vencimiento) VALUES (?, ?, NOW(), ?)";
    // Insertar vencimiento (puede ser null)
    $conexion->actualizar($sqlInsert, "iis", [$id_usuario, $id_beneficio, $fechaVencimiento]);

    echo json_encode(['success' => true, 'nuevos_puntos' => $nuevosPuntos]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar: ' . $e->getMessage()]);
}
?>
