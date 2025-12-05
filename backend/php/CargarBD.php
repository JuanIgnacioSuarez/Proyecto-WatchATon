<?php
   require_once('../classes/Conexion.php');
$conexion = new Conexion();

if ($_POST['ID'] == "" || $_POST['Url'] == "" || $_POST['titulo'] == "" || $_POST['descripcion'] == "" || $_POST['portadaID'] == "") {
    echo "mal";
} else {
    if (strlen($_POST['titulo']) > 30 || strlen($_POST['descripcion']) > 300) {
        echo "largo";
    } else {
        if (!isset($_COOKIE['iniciado'])) {  //Utilizo la cokie para saber si puede subir el video o no
            echo "noiniciado";
        } else {
            $email = $_COOKIE['iniciado'];
            $sanciones = $conexion->verificarSanciones($email);

            if ($sanciones >= 3) {
                echo "sancionado";
            } else {
                $ID_usuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);  //Verifico que exista el usuario 

                if ($ID_usuario != null) {
                    $sql = "INSERT INTO videos (ID_usuario, public_id, Url, Titulo, Descripcion, public_id_portada) VALUES (?, ?, ?, ?, ?, ?)";
                    $tipos = "isssss";
                    $parametros = [  $ID_usuario, $_POST['ID'],$_POST['Url'],$_POST['titulo'],$_POST['descripcion'], $_POST['portadaID']];

                    if ($conexion->insertar($sql, $tipos, $parametros)) {
                        echo "bien";
                    }
                }
            }
        }
    }
}
?>