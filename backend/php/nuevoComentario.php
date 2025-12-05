<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();

if(isset($_COOKIE['iniciado'])){
    $sanciones = $conexion->verificarSanciones($_COOKIE['iniciado']);
    
    if ($sanciones >= 3) {
        echo "sancionado";
    } elseif(strlen($_POST['comentario']) <= 200 && strlen($_POST['comentario'])>0){  //Verificamos el largo 
        $IDusuario = $conexion->existeDato('usuarios','ID','Correo',$_COOKIE['iniciado']);

        $sql = "INSERT INTO comentarios (id_usuario, id_video, contenido) VALUES ( ?, ?, ?)";
        $tipos = "iis";
        $parametros = [$IDusuario, $_POST['idVideo'], $_POST['comentario']];

        $conexion->insertar($sql, $tipos, $parametros);  //Cargo el comentario
        echo "bien";
    }
    else{
        echo "largo"; //El comentario no esta entre los rangos adecuados (0 a 200)
    }
}
else{
    echo "noiniciado";//El usuario no inicio sesion
}
?>