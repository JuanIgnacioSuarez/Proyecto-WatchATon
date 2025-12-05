<?php
require_once('../classes/Conexion.php');
$conexion = new Conexion();

if (!isset($_COOKIE['iniciado'])) {
    echo '<div class="col-12 text-center py-5"><p class="text-white-50">Debes iniciar sesión para ver tus videos.</p></div>';
    exit;
}

$email = $_COOKIE['iniciado'];
$idUsuario = $conexion->existeDato('usuarios', 'ID', 'Correo', $email);

if ($idUsuario) {
    $sql = "SELECT * FROM videos WHERE ID_usuario = ?";
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
                 // Fallback al video si no hay portada (como estaba antes)
                 $thumbnailUrl = 'https://res.cloudinary.com/dqrxdpqef/video/upload/so_1/' . $video['public_id'] . '.jpg';
            }

            $isSanctioned = isset($video['sancionado']) && $video['sancionado'] == 1;
            $sanctionBadge = $isSanctioned ? '<div class="position-absolute top-0 start-0 m-2 badge bg-danger shadow-sm"><i class="bi bi-eye-slash-fill me-1"></i>Sancionado / Oculto</div>' : '';

            echo '
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
              <div class="card h-100 shadow-sm mb-4 glass-panel border-0">
                <div class="position-relative">
                    <a href="vervideo.php?id_video='.$video['ID_video'].'" style="text-decoration:none; color:inherit;">
                        <img src="' . $thumbnailUrl . '" 
                             class="card-img-top img-fluid rounded-top" 
                             style="height: 180px; object-fit: cover; opacity: '.($isSanctioned ? '0.5' : '1').';"
                             alt="Preview del video">
                        '.$sanctionBadge.'
                    </a>
                    <div class="position-absolute top-0 end-0 m-2 d-flex gap-2">
                        <button class="btn btn-light btn-sm rounded-circle shadow-sm btn-editar-video" 
                                data-id="'.$video['ID_video'].'" 
                                data-titulo="'.htmlspecialchars($video['Titulo']).'" 
                                data-descripcion="'.htmlspecialchars($video['Descripcion']).'"
                                data-portada="'.htmlspecialchars($video['public_id_portada'] ?? '').'"
                                data-video-public-id="'.$video['public_id'].'"
                                title="Editar Video">
                            <i class="bi bi-pencil-fill text-primary"></i>
                        </button>
                        <button class="btn btn-danger btn-sm rounded-circle shadow-sm btn-borrar-video" data-id="'.$video['ID_video'].'" title="Eliminar Video">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
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
            <h5 class="text-white mb-2">Aún no has subido videos</h5>
            <p class="text-white-50 mb-4">¡Comparte tu contenido con el mundo!</p>
        </div>';
    }
} else {
    echo '<div class="col-12 text-center py-5"><p class="text-white-50">Error al identificar al usuario.</p></div>';
}
?>
