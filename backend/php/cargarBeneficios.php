<?php
require_once('../classes/Conexion.php');

$conexion = new Conexion();

// Traer todos los beneficios
$sql = "SELECT b.*, t.descripcion as nombre_tipo 
        FROM beneficios b 
        INNER JOIN tipo_beneficio t ON b.id_tipo = t.id_tipo";
$beneficios = $conexion->consultar($sql);

// Recuperar el id del usuario y datos
$puntosUsuario = 0;
$beneficiosCanjeados = [];

if (isset($_COOKIE['iniciado'])) {
    $id_usuario = $conexion->existeDato('usuarios', 'ID', 'correo', $_COOKIE['iniciado']);
    if ($id_usuario) {
        // Buscar los puntos del usuario
        $puntosUsuario = $conexion->existeDato('usuarios', 'Puntos', 'ID', $id_usuario) ?? 0;

        // Buscar todos los beneficios que ya canjeó el usuario (excluyendo los pagados con dinero)
        // Solo bloqueamos si fue canjeado por puntos. Si fue comprado, permitimos canjear por puntos si el usuario quiere.
        $sql = "SELECT c.ID_beneficio 
                FROM canjeos c 
                LEFT JOIN pagos p ON c.ID_canjeo = p.id_canje 
                WHERE c.ID_usuario = ? AND p.id_pago IS NULL AND c.activo = 1";
        $tipos = "i";
        $parametros = [$id_usuario];
        $yaCanjeados = $conexion->consultar($sql, $tipos, $parametros);

        foreach ($yaCanjeados as $i) {
            $beneficiosCanjeados[] = $i['ID_beneficio'];
        }
    }
}

// Estructurar respuesta JSON
$response = [
    'puntos' => $puntosUsuario,
    'canjeados' => $beneficiosCanjeados,
    'items' => $beneficios
];

header('Content-Type: application/json');
echo json_encode($response);
?>