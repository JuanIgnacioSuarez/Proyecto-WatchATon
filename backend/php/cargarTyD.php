<?php

require_once('../classes/Conexion.php');

$conexion = new Conexion();

// Buscamos el id del usuario que subió el video
$idUsuario = $conexion->existeDato('videos', 'ID_usuario', 'ID_video', $_POST['idVideo']);

// Recuperar título y descripción del video
// Recuperar título, descripción y estado de sanción del video
$sql = "SELECT Titulo, Descripcion, sancionado FROM videos WHERE ID_video = ?";
$tipos = "i";
$parametros = [$_POST['idVideo']];
$datosVideo = $conexion->consultar($sql, $tipos, $parametros);

$titulo = "";
$descripcion = "";
$sancionado = 0;

if (count($datosVideo) > 0) {
    $titulo = $datosVideo[0]['Titulo'];
    $descripcion = $datosVideo[0]['Descripcion'];
    $sancionado = $datosVideo[0]['sancionado'];
}

// Recuperar datos del usuario que subió el video
$sql2 = "SELECT Correo, nombre_usuario, public_id_perfil FROM usuarios WHERE ID = ?";
$parametros2 = [$idUsuario];
$datosUsuario = $conexion->consultar($sql2, "i", $parametros2);

$nombreMostrar = "Usuario Desconocido";
$fotoPerfilUrl = "../assets/images/logo.jpg"; // Imagen por defecto

if (count($datosUsuario) > 0) {
    $usuario = $datosUsuario[0];
    // Determinar nombre a mostrar
    $nombreMostrar = !empty($usuario['nombre_usuario']) ? $usuario['nombre_usuario'] : $usuario['Correo'];
    
    // Determinar foto de perfil
    if (!empty($usuario['public_id_perfil'])) {
        // Construir URL de Cloudinary (transformación básica para asegurar tamaño)
        $fotoPerfilUrl = "https://res.cloudinary.com/dqrxdpqef/image/upload/c_fill,h_100,w_100/" . $usuario['public_id_perfil'];
    }
}

//Ahora devolveremos lo que se mostrara en el html
	echo '
<input type="hidden" id="estadoSancionVideo" value="'.$sancionado.'">
<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4 glass-panel text-white">
    <div class="card-body p-4">
      <h3 id="tituloVideo" class="card-title mb-3 fw-bold text-gradient"> '.htmlspecialchars($titulo).'</h3>
      <div class="d-flex align-items-center mb-3">
        <div class="me-3">
            <a href="perfilPublico.php?id='.$idUsuario.'" class="text-decoration-none">
                <img src="'.htmlspecialchars($fotoPerfilUrl).'" alt="Perfil" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid rgba(255,255,255,0.2);">
            </a>
        </div>
        <div>
            <span class="text-white-50 small d-block">Subido por</span>
            <a href="perfilPublico.php?id='.$idUsuario.'" class="text-decoration-none">
                <span id="autorVideo" class="fw-bold text-white hover-text-primary">'.htmlspecialchars($nombreMostrar).'</span>
            </a>
        </div>
      </div>
      <hr class="border-secondary opacity-50">
      <p id="descripcionVideo" class="card-text fs-5 text-white-50">'.nl2br(htmlspecialchars($descripcion)).'</p>
    </div>
  </div>
</div>
';
?>