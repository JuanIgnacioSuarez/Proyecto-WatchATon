<?php

require_once('../classes/Conexion.php');

$conexion = new Conexion();

// Buscamos el id del usuario que subió el video
$idUsuario = $conexion->existeDato('videos', 'ID_usuario', 'ID_video', $_POST['idVideo']);

// Recuperar título y descripción del video
$sql = "SELECT Titulo, Descripcion FROM videos WHERE ID_video = ?";
$tipos = "i";
$parametros = [$_POST['idVideo']];
$datosVideo = $conexion->consultar($sql, $tipos, $parametros);

$titulo = "";
$descripcion = "";
if (count($datosVideo) > 0) {
    $titulo = $datosVideo[0]['Titulo'];
    $descripcion = $datosVideo[0]['Descripcion'];
}

// Recuperar correo del usuario que subió el video
$sql2 = "SELECT Correo FROM usuarios WHERE ID = ?";
$parametros2 = [$idUsuario];
$datosUsuario = $conexion->consultar($sql2, "i", $parametros2);

$correo = "";
if (count($datosUsuario) > 0) {
    $correo = $datosUsuario[0]['Correo'];
}

//Ahora devolveremos lo que se mostrara en el html , como cada video solo tiene 1 de cada  no hace falta hacer assoc 
	echo '
<div class="container mt-4">
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">
      <h3 id="tituloVideo" class="card-title mb-3 fw-bold text-primary"> '.htmlspecialchars($titulo).'</h3>
      <div class="d-flex align-items-center mb-3">
        <i class="bi bi-person-circle me-2 fs-5 text-secondary"></i>
        <span id="autorVideo" class="text-muted">Subido por <strong>'.htmlspecialchars($correo).'</strong></span>
      </div>
      <hr>
      <p id="descripcionVideo" class="card-text fs-5">'.htmlspecialchars($descripcion).'</p>
    </div>
  </div>
</div>
';
?>