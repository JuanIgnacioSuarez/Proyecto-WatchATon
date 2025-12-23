<?php  //Archivo que carga una bitacora en la base , permitiendonos llevar un registro de la visualizacion de los anuncios
require_once('../classes/Conexion.php');

$conexion = new Conexion();


// Buscamos el ID del usuario según la cookie, si no existe o no hay cookie  lo dejamos en 0
if (isset($_COOKIE['iniciado'])) {
    $id_usuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $_COOKIE['iniciado']);
    if ($id_usuario === null) {
        $id_usuario = 0;
    }
} else {
    $id_usuario = 0;
}

$id_video    = $_POST['id_video'] ?? 0;
$id_anuncio  = $_POST['id_anuncio'] ?? 0;
$navegador   = $_POST['navegador'] ?? 'Desconocido';
$inicio      = $_POST['inicio_visualizacion'] ?? '';
$fin         = $_POST['fin_visualizacion'] ?? '';

// Nuevos campos
$estado      = $_POST['estado'] ?? 'desconocido';
$click       = isset($_POST['click']) ? intval($_POST['click']) : 0;
$dispositivo = $_POST['dispositivo'] ?? 'desconocido';
$porcentaje  = isset($_POST['porcentaje_visto']) ? intval($_POST['porcentaje_visto']) : 0;

$sql = "INSERT INTO bitacora_anuncios (id_usuario, id_video, id_anuncio, navegador, inicio_visualizacion, fin_visualizacion, estado, click, dispositivo, porcentaje_visto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$tipos = "iiissssisi"; // i=int, s=string, i=int, i=int ... count: i(id_u), i(id_v), i(id_a), s(nav), s(ini), s(fin), s(est), i(cli), s(dev), i(por) = 10
$parametros = [$id_usuario, $id_video, $id_anuncio, $navegador, $inicio, $fin, $estado, $click, $dispositivo, $porcentaje];

// Enable strict error reporting to catch hidden DB errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Debug 
    $logEntry = date('Y-m-d H:i:s') . " - ID User: $id_usuario, ID Video: $id_video, ID Anuncio: $id_anuncio, Nav: $navegador, Inicio: $inicio, Fin: $fin" . PHP_EOL;
    file_put_contents(__DIR__ . '/../debug/debug_bitacora.txt', $logEntry, FILE_APPEND);

    if ($conexion->insertar($sql, $tipos, $parametros)) {
        echo json_encode(['success' => true, 'message' => 'Bitácora guardada']);
    } else {
        file_put_contents(__DIR__ . '/../debug/debug_bitacora.txt', "FAILED TO INSERT (returned false)" . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Error al guardar bitácora']);
    }
} catch (Exception $e) {
    $errorMsg = "DB Exception: " . $e->getMessage();
    file_put_contents(__DIR__ . '/../debug/debug_bitacora.txt', $errorMsg . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => $errorMsg]);
}
?>