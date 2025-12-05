<?php
require_once('../../backend/classes/Conexion.php');
$conexion = new Conexion();

// Verificar sesión y permisos de admin
if (!isset($_COOKIE['iniciado'])) {
    header("Location: IniciarSesion.php");
    exit();
}

$email = $_COOKIE['iniciado'];
$sql = "SELECT Permisos FROM usuarios WHERE Correo = ?";
$res = $conexion->consultar($sql, "s", [$email]);

if (empty($res) || $res[0]['Permisos'] != 1) {
    // Si no es admin, redirigir al inicio
    header("Location: index.php");
    exit();
}
?>
