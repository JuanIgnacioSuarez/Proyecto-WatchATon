<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../css/misestilos.css" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link href="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>
  <script src="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.js"></script>
  <title>Subir un video</title>
</head>
<body class="bg-dark text-light d-flex flex-column min-vh-100" style="background-image: url('../assets/images/fondo-suave.avif'); background-size: cover; background-position: center center; background-attachment: fixed;">
<?php include '../includes/cabecera.php' ?>

<main class="container py-5 flex-grow-1 d-flex align-items-center justify-content-center">
  <div class="card bg-dark text-light shadow-lg p-4" style="max-width: 800px; width: 100%;">
    <h2 class="mb-4 text-center d-flex align-items-center justify-content-center">Subir Video <button type="button" class="btn btn-sm btn-outline-info ms-2 rounded-circle" data-bs-toggle="modal" data-bs-target="#helpModal"><i class="bi bi-question-circle"></i></button></h2>
    
    <div class="mb-3">
      <label for="titulo" class="form-label">Título</label>
      <input   type="text"  id="titulo" class="form-control bg-secondary text-light border-0" maxlength="30" aria-describedby="tituloHelp" placeholder="Escribe un título (máx 30 caracteres)" />
      <div id="tituloHelp" class="form-text text-white-50">
        No debe superar los 30 caracteres
      </div>
    </div>
    
    <div class="mb-4">
      <label for="descripcion" class="form-label">Descripción del Video</label>
      <textarea   id="descripcion"  class="form-control bg-secondary text-light border-0"  rows="4"  maxlength="300" placeholder="Escribe una descripción para el video (máximo 300 caracteres)"></textarea>
    </div>

    <!-- Sección de carga y previsualización de Portada -->
    <div class="mb-4">
      <label class="form-label">Portada del Video</label>
      <div class="input-group">
        <button id="SubirPortada" class="btn btn-info text-dark" type="button">Seleccionar Portada</button>
        <input type="hidden" id="portada_public_id" name="portada_public_id">
      </div>
      <div class="mt-3 text-center">
        <label class="form-label d-block">Visualización de la portada</label>
        <img id="portadaPreview" src="" alt="Previsualización de Portada" class="img-fluid rounded shadow-sm" style="max-height: 200px; display: none;">
      </div>
      <button id="borrarPortadaCargada" class="btn btn-danger btn-sm mt-2" style="display: none;">Borrar portada cargada <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinnerPortada" style="display: none;"></span></button>
    </div>
    
    <!-- Sección de previsualización de Video -->
    <div class="mb-4 text-center">
      <label class="form-label d-block">Previsualización del Video</label>
      <div id="videoPreviewContainer" style="width: 640px; height: 360px; margin: 0 auto; display: none;" class="rounded shadow-sm mb-3">
        <!-- El elemento video será generado dinámicamente por JavaScript -->
      </div>
      <button id="borrarVideoCargado" class="btn btn-danger btn-sm mt-2" style="display: none;">Borrar video cargado <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinnerVideo" style="display: none;"></span></button>
    </div>

    <div class="d-flex gap-3 justify-content-center mt-4">
      <button id="SubirVideo" class="btn btn-primary px-4">Seleccionar Video</button>
      <button id="guardar" class="btn btn-success px-4">Subir video y guardar datos</button>
    </div>
    
    
  </div>
</main>

<?php include '../includes/footer.php' ?>

<!-- Modal de Ayuda -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-light">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="helpModalLabel">Cómo subir un video</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>En esta página puedes subir tus videos a la plataforma. Sigue estos pasos:</p>
        <ol>
          <li>**Título y Descripción:** Completa el título (máx. 30 caracteres) y la descripción (máx. 300 caracteres) de tu video.</li>
          <li>**Portada del Video:** Haz clic en "Seleccionar Portada" para elegir una imagen que represente tu video. Asegúrate de que el formato sea JPG, JPEG, PNG o WEBP. Verás una previsualización.</li>
          <li>**Video:** Haz clic en "Seleccionar Video" para subir tu archivo de video. Solo se permiten videos en formato MP4. Verás una previsualización del video cargado.</li>
          <li>**Borrar Contenido Cargado:** Si te confundes o cambias de opinión, puedes usar los botones "Borrar portada cargada" y "Borrar video cargado" para eliminar lo que hayas subido antes de guardar.</li>
          <li>**Guardar Datos:** Una vez que hayas subido el video y la portada, y completado los campos de título y descripción, haz clic en "Subir video y guardar datos" para finalizar.</li>
        </ol>
        <p>¡Listo! Tu video estará disponible para que otros usuarios lo vean.</p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../js/subir-video.js"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>