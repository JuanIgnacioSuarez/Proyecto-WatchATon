<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();

$idUsuario = $_POST['id_usuario'] ?? null;

if (!$idUsuario) {
    echo '<div class="col-12 text-center py-5"><p class="text-white-50">Usuario no especificado.</p></div>';
    exit;
}

$sql = "SELECT * FROM videos WHERE ID_usuario = ? AND sancionado = 0";
$tipos = "i";
$parametros = [$idUsuario];
$resultado = $conexion->consultar($sql, $tipos, $parametros);

if (count($resultado) > 0) {
    foreach ($resultado as $video) {
        // Determinar URL de la portada
        $thumbnailUrl = '../assets/images/logo.jpg'; // Fallback por defecto
        if (!empty($video['public_id_portada'])) {
            $thumbnailUrl = 'https://res.cloudinary.com/dqrxdpqef/image/upload/c_fill,h_180,w_320/' . $video['public_id_portada'];
        } elseif (!empty($video['public_id'])) {
                // Fallback al video si no hay portada
                $thumbnailUrl = 'https://res.cloudinary.com/dqrxdpqef/video/upload/so_1/' . $video['public_id'] . '.jpg';
        }

        echo '
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm mb-4 glass-panel border-0">
            <div class="position-relative">
                <a href="vervideo.php?id_video='.$video['ID_video'].'" style="text-decoration:none; color:inherit;">
                    <img src="' . $thumbnailUrl . '" 
                            class="card-img-top img-fluid rounded-top" 
                            style="height: 180px; object-fit: cover;"
                            alt="Preview del video">
                </a>
            </div>
            <div class="card-body text-white">
                <h5 class="card-title text-truncate">'.htmlspecialchars($video['Titulo']).'</h5>
                <p class="card-text small text-white-50 text-truncate">'.htmlspecialchars($video['Descripcion']).'</p>
            </div>
            </div>
        </div>';
    }
} else {
    // Estado vacío si el usuario no tiene videos
    echo '
    <div class="col-12 text-center py-5">
        <div class="bg-white bg-opacity-10 rounded-circle p-4 d-inline-block mb-3">
            <i class="bi bi-film fs-1 text-white-50"></i>
        </div>
        <h5 class="text-white mb-2">Este usuario aún no ha subido videos</h5>
    </div>';
}
?>
