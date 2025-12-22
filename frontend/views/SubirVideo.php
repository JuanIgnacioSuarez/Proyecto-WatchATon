<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link href="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.css" rel="stylesheet" />
  <script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>
  <script src="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.js"></script>
  <link rel="stylesheet" href="../css/misestilos.css" />
  <title>Subir un video</title>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include '../includes/cabecera.php' ?>

<div id="toast-container"></div>

<main class="container py-5 flex-grow-1 d-flex align-items-center justify-content-center">
  <div class="glass-panel p-5 w-100" style="max-width: 800px;">
    <h2 class="mb-4 text-center d-flex align-items-center justify-content-center fw-bold text-white">
        Subir Video 
        <button type="button" class="btn btn-sm btn-outline-light ms-3 rounded-circle" data-bs-toggle="modal" data-bs-target="#helpModal">
            <i class="bi bi-question-lg"></i>
        </button>
    </h2>
    
    <div class="mb-4">
      <label for="titulo" class="form-label text-white">Título</label>
      <input type="text" id="titulo" class="form-control form-control-glass" maxlength="30" aria-describedby="tituloHelp" placeholder="Escribe un título (máx 30 caracteres)" />
      <div id="tituloHelp" class="form-text text-white-50">
        No debe superar los 30 caracteres
      </div>
    </div>
    
    <div class="mb-4">
      <label for="descripcion" class="form-label text-white">Descripción del Video</label>
      <textarea id="descripcion" class="form-control form-control-glass" rows="4" maxlength="300" placeholder="Escribe una descripción para el video (máximo 300 caracteres)"></textarea>
    </div>

    <!-- Sección de carga y previsualización de Portada -->
    <div class="mb-4">
      <label class="form-label text-white">Portada del Video</label>
      <div class="d-flex gap-2">
        <button id="SubirPortada" class="btn btn-outline-light w-100" type="button"><i class="bi bi-image me-2"></i>Seleccionar Portada</button>
        <input type="hidden" id="portada_public_id" name="portada_public_id">
      </div>
      <div class="mt-3 text-center">
        <img id="portadaPreview" src="" alt="Previsualización de Portada" class="img-fluid rounded shadow-sm" style="max-height: 200px; display: none;">
      </div>
      <div class="text-center">
          <button id="borrarPortadaCargada" class="btn btn-danger btn-sm mt-2" style="display: none;">Borrar portada cargada <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinnerPortada" style="display: none;"></span></button>
      </div>
    </div>
    
    <!-- Sección de previsualización de Video -->
    <div class="mb-4">
      <label class="form-label text-white">Archivo de Video</label>
      <div class="d-flex gap-2 mb-3">
        <button id="SubirVideo" class="btn btn-outline-light w-100" type="button"><i class="bi bi-camera-video me-2"></i>Seleccionar Video</button>
      </div>
      <div id="videoPreviewContainer" style="width: 100%; max-width: 640px; height: 360px; margin: 0 auto; display: none;" class="rounded shadow-sm mb-3 overflow-hidden border border-secondary">
        <!-- El elemento video será generado dinámicamente por JavaScript -->
      </div>
      <button id="borrarVideoCargado" class="btn btn-danger btn-sm mt-2" style="display: none;">Borrar video cargado <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinnerVideo" style="display: none;"></span></button>
    </div>

    <div class="d-grid mt-5">
      <button id="guardar" class="btn btn-gradient btn-lg"><i class="bi bi-cloud-upload me-2"></i>Publicar Video</button>
    </div>
    
  </div>
</main>

<?php include '../includes/footer.php' ?>

<!-- Modal de Ayuda -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-panel border-0 text-white">
      <div class="modal-header border-bottom border-secondary">
        <h5 class="modal-title fw-bold" id="helpModalLabel">Cómo subir un video</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>En esta página puedes subir tus videos a la plataforma. Sigue estos pasos:</p>
        <ol class="ps-3">
          <li class="mb-2"><strong>Título y Descripción:</strong> Completa el título (máx. 30 caracteres) y la descripción (máx. 300 caracteres).</li>
          <li class="mb-2"><strong>Portada:</strong> Haz clic en "Seleccionar Portada" para elegir una imagen (JPG, PNG, WEBP).</li>
          <li class="mb-2"><strong>Video:</strong> Haz clic en "Seleccionar Video" para subir tu archivo MP4.</li>
          <li class="mb-2"><strong>Publicar:</strong> Una vez cargados ambos archivos y completados los textos, pulsa "Publicar Video".</li>
        </ol>
      </div>
      <div class="modal-footer border-top border-secondary">
        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Entendido</button>
      </div>
    </div>
  </div>
</div>


<script src="../js/subir-video.js"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>