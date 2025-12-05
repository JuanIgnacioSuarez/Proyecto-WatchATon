<?php     //Aqui es donde se cargaran las cartas con los videos , segun cuantos videos se encuentren parecidos a la busqueda.

require_once('../classes/Conexion.php');
$conexion = new Conexion();

$titulo = $_POST['titulo'];

if ($titulo == "") {
    $sql = "SELECT * FROM videos WHERE sancionado = 0";
    $resultado = $conexion->consultar($sql);
} else {
    $sql = "SELECT * FROM videos WHERE titulo LIKE ? AND sancionado = 0";  //Usamos el like para que aparezcan tantos videos como titulos tengan parecidos a la busqueda
    $tipos = "s";
    $busqueda = "%$titulo%";
    $parametros = [$busqueda];
    $resultado = $conexion->consultar($sql, $tipos, $parametros);
}

if(count($resultado) > 0){
    foreach ($resultado as $video) {     //Aca para cada video , creo una tarjeta con la info , ademas de darle el ID_video como atributo
        // Fallback a frame del video (comportamiento original)
        $thumbnailUrl = 'https://res.cloudinary.com/dqrxdpqef/video/upload/so_1/' . $video['public_id'] . '.jpg';
        
        if (!empty($video['public_id_portada'])) {
            $thumbnailUrl = 'https://res.cloudinary.com/dqrxdpqef/image/upload/c_fill,h_250,w_400/' . $video['public_id_portada'];
        }

        echo '
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm mb-4">
            <a href="vervideo.php?id_video='.$video['ID_video'].'" style="text-decoration:none; color:inherit;">
              <img src="' . $thumbnailUrl . '" 
                   class="card-img-top img-fluid" 
                   style="height: 250px; object-fit: cover;"
                   alt="Preview del video">
              <div class="card-body">
                <h5 class="card-title">'.htmlspecialchars($video['Titulo']).'</h5>
                <p class="card-text">'.htmlspecialchars($video['Descripcion']).'</p>
              </div>
            </a>
          </div>
        </div>';
    }
}
else{
    echo '
    <div class="col-12 text-center mt-5 no-results-container">
        <div class="glass-panel d-inline-block p-5 fade-in-up">
            <i class="bi bi-search display-1 text-muted mb-4 d-block" style="opacity: 0.5;"></i>
            <h3 class="fw-bold text-white mb-3">¡Vaya! No encontramos nada</h3>
            <p class="text-white-50 fs-5">Parece que no hay videos con ese nombre.<br>¿Por qué no pruebas con otra búsqueda?</p>
        </div>
    </div>';
}
?>