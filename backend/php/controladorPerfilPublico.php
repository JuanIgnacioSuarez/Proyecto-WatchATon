<?php
require_once(__DIR__ . '/../classes/Conexion.php');
$conexion = new Conexion();

$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    header("Location: ../index.php");
    exit();
}

// Obtener datos del usuario
$sql = "SELECT nombre_usuario, public_id_perfil, biografia, Correo FROM usuarios WHERE ID = ?";
$tipos = "i";
$parametros = [$id_usuario];
$datosUsuario = $conexion->consultar($sql, $tipos, $parametros);

if (empty($datosUsuario)) {
    echo "Usuario no encontrado.";
    exit();
}

$nombreUsuario = $datosUsuario[0]['nombre_usuario'] ?? null;
$email = $datosUsuario[0]['Correo'];
$publicIdPerfil = $datosUsuario[0]['public_id_perfil'] ?? null;
$biografia = $datosUsuario[0]['biografia'] ?? null;

// Lógica para el nombre de usuario (default: correo)
$displayName = $nombreUsuario ? htmlspecialchars($nombreUsuario) : htmlspecialchars($email);

// Lógica para la biografía
$displayBio = $biografia ? htmlspecialchars($biografia) : "Este usuario no ha escrito una biografía aún.";

// Lógica para la foto de perfil (default: logo)
$profilePicUrl = "../assets/images/logo.jpg";
if ($publicIdPerfil) {
    $profilePicUrl = "https://res.cloudinary.com/dqrxdpqef/image/upload/c_fill,h_150,w_150,q_auto,f_auto/" . htmlspecialchars($publicIdPerfil);
}
?>
