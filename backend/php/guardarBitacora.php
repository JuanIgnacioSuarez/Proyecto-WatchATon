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

$id_video    = $_POST['id_video'];
$id_anuncio  = $_POST['id_anuncio'];
$navegador   = $_POST['navegador'];
$inicio      = $_POST['inicio_visualizacion'];
$fin         = $_POST['fin_visualizacion'];

$sql = "INSERT INTO bitacora_anuncios (id_usuario, id_video, id_anuncio, navegador, inicio_visualizacion, fin_visualizacion) VALUES (?, ?, ?, ?, ?, ?)";
$tipos = "iiisss";
$parametros = [$id_usuario, $id_video, $id_anuncio, $navegador, $inicio, $fin];
$conexion->insertar($sql, $tipos, $parametros);  //Cargamos en la bitacora
?>