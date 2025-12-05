<?php
require_once('../verificar_admin.php');
checkAdmin();

require_once('../../classes/Conexion.php');
$conexion = new Conexion();

// Contar usuarios
$sqlUsers = "SELECT COUNT(*) as total FROM usuarios";
$resUsers = $conexion->consultar($sqlUsers);
$totalUsers = $resUsers[0]['total'] ?? 0;

// Contar videos
$sqlVideos = "SELECT COUNT(*) as total FROM videos";
$resVideos = $conexion->consultar($sqlVideos);
$totalVideos = $resVideos[0]['total'] ?? 0;

// Contar anuncios
$sqlAds = "SELECT COUNT(*) as total FROM anuncios";
$resAds = $conexion->consultar($sqlAds);
$totalAds = $resAds[0]['total'] ?? 0;

echo json_encode([
    'users' => $totalUsers,
    'videos' => $totalVideos,
    'ads' => $totalAds
]);
?>
