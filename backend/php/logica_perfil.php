<?php
require_once('../../backend/classes/Conexion.php');
$conexion = new Conexion();

// Verificar sesión
if (!isset($_COOKIE['iniciado'])) {
    header("Location: IniciarSesion.php");
    exit();
}

$email = $_COOKIE['iniciado'];
$id_usuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

// Obtener datos del usuario
$sql = "SELECT nombre_usuario, public_id_perfil, biografia FROM usuarios WHERE ID = ?";
$tipos = "i";
$parametros = [$id_usuario];
$datosUsuario = $conexion->consultar($sql, $tipos, $parametros);

$nombreUsuario = $datosUsuario[0]['nombre_usuario'] ?? null;
$publicIdPerfil = $datosUsuario[0]['public_id_perfil'] ?? null;
$biografia = $datosUsuario[0]['biografia'] ?? null;

// Lógica para el nombre de usuario (default: correo)
$displayName = $nombreUsuario ? htmlspecialchars($nombreUsuario) : htmlspecialchars($email);

// Lógica para la biografía
$displayBio = $biografia ? htmlspecialchars($biografia) : "";

// Lógica para la foto de perfil (default: logo)
$profilePicUrl = "../assets/images/logo.jpg";
if ($publicIdPerfil) {
    $profilePicUrl = "https://res.cloudinary.com/dqrxdpqef/image/upload/c_fill,h_150,w_150,q_auto,f_auto/" . htmlspecialchars($publicIdPerfil);
}
?>
