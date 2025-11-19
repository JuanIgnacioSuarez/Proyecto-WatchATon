<?php     //Aqui es donde se cargaran las cartas con los videos , segun cuantos videos se encuentren parecidos a la busqueda.

require_once('../classes/Conexion.php');
$conexion = new Conexion();

$titulo = $_POST['titulo'];

if ($titulo == "") {
    $sql = "SELECT * FROM videos";
    $resultado = $conexion->consultar($sql);
} else {
    $sql = "SELECT * FROM videos WHERE titulo LIKE ?";  //Usamos el like para que aparezcan tantos videos como titulos tengan parecidos a la busqueda
    $tipos = "s";
    $busqueda = "%$titulo%";
    $parametros = [$busqueda];
    $resultado = $conexion->consultar($sql, $tipos, $parametros);
}

if(count($resultado) > 0){
    foreach ($resultado as $video) {     //Aca para cada video , creo una tarjeta con la info , ademas de darle el ID_video como atributo
        echo '
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm mb-4">
            <a href="vervideo.php?id_video='.$video['ID_video'].'" style="text-decoration:none; color:inherit;">
              <img src="https://res.cloudinary.com/dqrxdpqef/video/upload/so_1/' . $video['public_id'] . '.jpg" 
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
    echo '<p class="text-center fs-5 mt-4">No se encontraron videos que coincidan :c</p>'; //En caso de que ningun video tenga palabras parecidas
}
?>