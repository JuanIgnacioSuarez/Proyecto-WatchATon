<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../css/misestilos.css">
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link href="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.css" rel="stylesheet" />
<script src="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<title>WAT Video</title>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include('../includes/cabecera.php');?>

<div id="toast-container" aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1060">
  
</div>

<div class="container mt-4 mb-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10">
      <div class="video-player-container text-center">
        <video id="ReproductorAnuncio" class="cld-video-player d-block mx-auto" controls playsinline></video>
        <button id="SaltarAnuncio" class="btn btn-warning btn-lg fw-bold text-dark mt-3 px-4 py-2 rounded-pill shadow-sm" disabled>Saltar anuncio en <span id="TiempoSaltar">8</span></button>
        <video id="ReproductorVideo" class="cld-video-player d-block mx-auto mt-4 d-none" controls playsinline></video>
      </div>
    </div>
  </div>

  <div class="row mt-4 justify-content-center">
    <div class="col-12 col-md-10">
      <div id="TituloDescripcion">
        <!-- Título y descripción se cargarán aquí -->
      </div>
    </div>
  </div>

  <div class="row mt-4 justify-content-center">
    <div class="col-12 col-md-10">
      <div class="card my-4 shadow-sm">  <!--Para que el usuario añada un nuevo comentario al video-->
        <div class="card-body">
          <h5 class="card-title text-secondary mb-3">Agregar un comentario (Max 200 caracteres)</h5>
          
          <div class="mb-3">
            <label for="nuevoComentario" class="form-label">Expresate!</label>
            <textarea class="form-control" id="nuevoComentario" rows="3" placeholder="Escribí tu comentario aquí..."></textarea>
          </div>

          <div class="d-grid">
            <button type="button" id="crearComentario" class="btn btn-success">
              <i class="bi bi-send-fill"></i> Publicar comentario
            </button>
          </div>
        </div>
      </div>
      <div id="Comentarios">
        <!-- Comentarios se cargarán aquí -->
      </div>
    </div>
  </div>
</div>

<div class="g-recaptcha"data-sitekey="6LeSLEQrAAAAAMr6mBGkjf1yr0D2epz_Hx42akzN" data-callback="ExitoCaptcha" data-size="invisible"> </div> <!--Es el captcha inivisible-->

<?php include('../includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../js/vervideo.js"></script>
<script src="../js/confirm-logout.js"></script>

<!-- Modal de Confirmación de Borrado de Comentario -->
<div id="deleteCommentModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeDeleteCommentModal()">&times;</span>
    <p>¿Estás seguro de que quieres eliminar este comentario?</p>
    <div class="d-flex justify-content-center mt-4">
      <button id="confirmDeleteComment" class="btn btn-danger btn-sm me-2">Eliminar</button>
      <button id="cancelDeleteComment" class="btn btn-secondary btn-sm">Cancelar</button>
    </div>
  </div>
</div>

<!-- Modal de Edición de Comentario -->
<div id="editCommentModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeEditCommentModal()">&times;</span>
    <p>Editar Comentario</p>
    <textarea id="editCommentTextarea" class="form-control mb-3" rows="4" maxlength="200"></textarea>
    <div id="editCommentError" class="text-danger small mb-3" style="display: none;"></div>
    <div class="d-flex justify-content-center mt-4">
      <button id="saveEditComment" class="btn btn-success btn-sm me-2">Guardar</button>
      <button id="cancelEditComment" class="btn btn-secondary btn-sm">Cancelar</button>
    </div>
  </div>
</div>

</body>
</html>