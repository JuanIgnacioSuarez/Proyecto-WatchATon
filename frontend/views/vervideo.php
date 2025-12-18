<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/misestilos.css">
		<link href="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.css" rel="stylesheet" />
<script src="https://unpkg.com/cloudinary-video-player/dist/cld-video-player.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<title>WAT Video</title>
</head>
<style>
  /* Force modal z-index to be higher than everything else */
  .modal-backdrop {
    z-index: 10050 !important;
  }
  .modal {
    z-index: 10055 !important;
  }
</style>
<body class="d-flex flex-column min-vh-100">
<?php include('../includes/cabecera.php');?>

<div id="toast-container"></div>

<div class="container-fluid px-4 mt-4 mb-5 flex-grow-1">
  <div class="row g-4">
    
    <!-- Columna Izquierda: Video e Información -->
    <div class="col-12 col-lg-8">
      
      <!-- Contenedor del Video -->
      <div class="video-container-wrapper mb-4 rounded-4 overflow-hidden shadow-lg position-relative" style="background: #000;">
        <div class="p-0">
            <video id="ReproductorAnuncio" class="cld-video-player ad-player" controls playsinline></video>
            <video id="ReproductorVideo" class="cld-video-player d-none" controls playsinline></video>
        </div>
        
        <!-- Overlay para el botón de saltar anuncio -->
        <div id="AdOverlay" class="position-absolute end-0 p-4" style="bottom: 80px; z-index: 20;">
            <button id="SaltarAnuncio" class="btn btn-gradient fw-bold text-white px-4 py-2 rounded-pill shadow-lg border-0" disabled>
                Saltar anuncio en <span id="TiempoSaltar">5</span>
            </button>
        </div>
      </div>

      <!-- Información del Video (Título y Descripción) -->
      <div class="glass-panel p-4 mb-4">
        <div id="TituloDescripcion">
          <!-- Se carga dinámicamente -->
        </div>

      </div>

    </div>

    <!-- Columna Derecha: Comentarios -->
    <div class="col-12 col-lg-4">
      <div class="glass-panel p-0 h-100 d-flex flex-column" style="max-height: 800px;">
        
        <div class="p-3 border-bottom border-secondary">
            <h5 class="text-white mb-0"><i class="bi bi-chat-dots-fill me-2 text-primary"></i>Comentarios</h5>
        </div>

        <!-- Lista de Comentarios (Scrollable) -->
        <div id="Comentarios" class="flex-grow-1 overflow-auto p-3 custom-scrollbar" style="min-height: 300px;">
            <!-- Se cargan dinámicamente -->
        </div>

        <!-- Formulario de Nuevo Comentario (Fijo al fondo) -->
        <div class="p-3 border-top border-secondary bg-dark bg-opacity-25">
             <label for="nuevoComentario" class="form-label text-white-50 small">Agregar un comentario</label>
             <div class="d-flex gap-2">
                <textarea class="form-control form-control-glass custom-scrollbar" id="nuevoComentario" rows="1" placeholder="Escribe algo..." style="resize: none;"></textarea>
                <button type="button" id="crearComentario" class="btn btn-gradient btn-sm px-3">
                  <i class="bi bi-send-fill"></i>
                </button>
             </div>
             <div class="text-end mt-1">
                 <small class="text-white-50" style="font-size: 0.7rem;">Máx 200 caracteres</small>
             </div>
        </div>

      </div>
    </div>

  </div>
</div>

<div class="g-recaptcha" data-sitekey="6LeSLEQrAAAAAMr6mBGkjf1yr0D2epz_Hx42akzN" data-callback="ExitoCaptcha" data-size="invisible"> </div>

<?php include('../includes/footer.php'); ?>

<!-- Modal de Confirmación de Borrado de Comentario -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-panel border-0 text-white">
      <div class="modal-body text-center p-4">
        <i class="bi bi-exclamation-circle text-warning display-4 mb-3"></i>
        <p class="mb-4">¿Estás seguro de que quieres eliminar este comentario?</p>
        <div class="d-flex justify-content-center gap-2">
            <button id="cancelDeleteComment" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
            <button id="confirmDeleteComment" class="btn btn-danger">Eliminar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Edición de Comentario -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-panel border-0 text-white">
      <div class="modal-header border-bottom border-secondary">
        <h5 class="modal-title">Editar Comentario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <textarea id="editCommentTextarea" class="form-control form-control-glass" rows="4" maxlength="200"></textarea>
        <div id="editCommentError" class="text-danger small mt-2" style="display: none;"></div>
      </div>
      <div class="modal-footer border-top border-secondary">
        <button id="cancelEditComment" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
        <button id="saveEditComment" class="btn btn-success">Guardar Cambios</button>
      </div>
    </div>
  </div>
  </div>
</div>




<!-- Modal de Sanción (Admin) -->
<div class="modal fade" id="sanctionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-panel border-0 text-white">
      <div class="modal-header border-bottom border-secondary">
        <h5 class="modal-title">Sancionar / Eliminar Contenido</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="sanctionForm">
            <input type="hidden" id="sanctionTargetId" name="targetId">
            <input type="hidden" id="sanctionTargetType" name="targetType"> <!-- 'video' or 'comment' -->

            <div class="mb-3">
                <label for="sanctionReason" class="form-label">Motivo</label>
                <select class="form-select form-control-glass" id="sanctionReason" required>
                    <option value="" selected disabled>Seleccione un motivo...</option>
                    <option value="Spam">Spam / Publicidad no deseada</option>
                    <option value="Contenido Inapropiado">Contenido Inapropiado / NSFW</option>
                    <option value="Acoso">Acoso / Discurso de Odio</option>
                    <option value="Derechos de Autor">Infracción de Derechos de Autor</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="sanctionDescription" class="form-label">Descripción (Opcional)</label>
                <textarea class="form-control form-control-glass" id="sanctionDescription" rows="3" maxlength="300" placeholder="Detalles adicionales..."></textarea>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="applySanction">
                <label class="form-check-label" for="applySanction">
                    Aplicar Sanción (Strike) al usuario
                </label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-danger">Confirmar Eliminación</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal de Confirmación de Descarga (Movido al final para evitar problemas de Z-Index) -->


<script>
    const esAdmin = <?php echo isset($esAdmin) && $esAdmin ? 'true' : 'false'; ?>;
</script>
<script src="../js/vervideo.js?v=<?php echo time(); ?>"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>