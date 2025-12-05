<?php
require_once('../../backend/php/logica_perfil.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi Perfil - WatchATon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <!-- Cargar estilos personalizados DESPUÉS de bootstrap -->
  <link rel="stylesheet" href="../css/misestilos.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-analytics.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-auth.js"></script>
  <script src="../js/firebase-config.js"></script>
  <script src="https://widget.cloudinary.com/v2.0/global/all.js" type="text/javascript"></script>

</head>
<body class="d-flex flex-column min-vh-100">
  <!-- Fondo Animado -->
  <div class="mesh-loader">
      <div class="mesh-gradient"></div>
  </div>

  <?php include '../includes/cabecera.php';?>

  <div class="dashboard-container position-relative z-2">
      <!-- Barra Lateral -->
      <aside class="dashboard-sidebar">
        <div class="text-center mb-4">
            <div class="profile-avatar-container mb-3">
                <div class="profile-avatar-glow"></div>
                <img src="<?php echo $profilePicUrl; ?>" alt="Foto de Perfil" class="rounded-circle border border-2 border-white profile-pic-display" style="width: 80px; height: 80px; object-fit: cover;">
            </div>
            <h5 class="text-white fw-bold mb-1 text-truncate username-display"><?php echo $displayName; ?></h5>
            <p class="text-white-50 small mb-0">Miembro desde 2025</p>
        </div>
        
        <hr class="border-secondary opacity-50 my-4">
        
        <nav class="nav flex-column">
            <a href="#" class="profile-nav-link active" id="btn-mis-videos">
                <i class="bi bi-collection-play me-3 text-primary"></i>
                <span>Mis videos</span>
            </a>
            <a href="#" class="profile-nav-link" id="btn-mi-info">
                <i class="bi bi-person me-3 text-info"></i>
                <span>Mi info</span>
            </a>
            <a href="#" class="profile-nav-link" id="btn-mi-cuenta">
                <i class="bi bi-gear me-3 text-warning"></i>
                <span>Mi cuenta</span>
            </a>
            <a href="#" class="profile-nav-link" id="btn-mis-sanciones">
                <i class="bi bi-exclamation-triangle me-3 text-danger"></i>
                <span>Mis Sanciones</span>
            </a>
        </nav>
      </aside>

      <!-- Contenido Principal -->
      <main class="dashboard-content w-100">
        <div class="glass-panel p-4 rounded-4 w-100">
          
          <!-- Contenido de "Mis videos" -->
          <div id="mis-videos-content" class="fade-in-up">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h3 class="text-white fw-bold mb-0">Mis Videos</h3>
                <a href="SubirVideo.php" class="btn btn-sm btn-gradient rounded-pill px-3">
                    <i class="bi bi-plus-lg me-2"></i>Subir Nuevo
                </a>
            </div>
            
            <!-- Contenedor para la lista de videos -->
            <div id="lista-mis-videos" class="row g-4">
                <!-- Aquí se cargarán los videos dinámicamente -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
          </div>

          <!-- Contenido de "Mi info" -->
          <div id="mi-info-content" class="d-none fade-in-up">
            <h3 class="text-white fw-bold mb-4">Mi Información</h3>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="profile-avatar-container mb-3">
                        <div class="profile-avatar-glow"></div>
                        <img src="<?php echo $profilePicUrl; ?>" alt="Foto de Perfil" class="rounded-circle border border-2 border-white shadow-lg profile-pic-display" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <button class="btn btn-outline-light btn-sm rounded-pill mt-2" id="btn-cambiar-foto">
                        <i class="bi bi-camera me-2"></i>Cambiar Foto
                    </button>
                </div>
                <div class="col-12 col-md-8">
                    <form id="form-mi-info">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Nombre de Usuario</label>
                            <input type="text" class="form-control form-control-glass" id="input-username" value="<?php echo $displayName; ?>" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white-50">Biografía</label>
                            <textarea class="form-control form-control-glass" id="input-biography" rows="4" placeholder="¿Por qué no nos contás algo de vos?" maxlength="500"><?php echo $biografia ? htmlspecialchars($biografia) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-gradient rounded-pill px-4">Guardar Cambios</button>
                    </form>
                </div>
            </div>
          </div>

          <!-- Contenido de "Mi cuenta" -->
          <div id="mi-cuenta-content" class="d-none fade-in-up">
            <h3 class="text-white fw-bold mb-4">Configuración de Cuenta</h3>
            <div class="row">
                <div class="col-12">
                    <form id="form-cambiar-password">
                        <div class="mb-4">
                            <label class="form-label text-white-50">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-white-50 ps-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control form-control-glass" id="email-display" value="<?php echo htmlspecialchars($email); ?>" readonly>
                            </div>
                            <div class="form-text text-white-50 opacity-50">El correo electrónico no se puede cambiar.</div>
                        </div>
                        
                        <h5 class="text-white fw-bold mb-3 mt-5">Cambiar Contraseña</h5>
                        <div class="mb-3">
                            <label for="current-password" class="form-label text-white-50">Contraseña Actual</label>
                            <input type="password" class="form-control form-control-glass" id="current-password" placeholder="Ingresa tu contraseña actual">
                        </div>
                        <div class="mb-3">
                            <label for="new-password" class="form-label text-white-50">Nueva Contraseña</label>
                            <input type="password" class="form-control form-control-glass" id="new-password" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="mb-4">
                            <label for="confirm-password" class="form-label text-white-50">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control form-control-glass" id="confirm-password" placeholder="Repite la nueva contraseña">
                        </div>
                        <button type="submit" class="btn btn-outline-light rounded-pill px-4" id="btn-update-password">
                            Actualizar Contraseña
                        </button>
                    </form>
                    
                    <hr class="border-secondary opacity-50 my-5">
                    
                    <div class="bg-danger bg-opacity-10 p-4 rounded-4 border border-danger border-opacity-25">
                        <h5 class="text-danger fw-bold mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Zona de Peligro</h5>
                        <p class="text-white-50 mb-3 small">Una vez que elimines tu cuenta, no hay vuelta atrás. Por favor, asegúrate.</p>
                        <button class="btn btn-danger rounded-pill px-4" id="btn-eliminar-cuenta">
                            <i class="bi bi-trash me-2"></i>Eliminar Cuenta
                        </button>
                    </div>
                </div>
            </div>
          </div>

          <!-- Contenido de "Mis Sanciones" -->
          <div id="mis-sanciones-content" class="d-none fade-in-up">
            <h3 class="text-white fw-bold mb-4">Historial de Sanciones</h3>
            
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-white mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill fs-1 me-3"></i>
                    <div>
                        <h5 class="alert-heading fw-bold mb-1">Estado de la Cuenta</h5>
                        <p class="mb-0">Tienes <strong id="total-sanciones-count" class="fs-4">0</strong> sanciones activas (Strikes).</p>
                        <small class="opacity-75">Recuerda que al llegar a 3 sanciones, tu cuenta será limitada.</small>
                    </div>
                </div>
            </div>

            <div id="lista-sanciones" class="d-flex flex-column gap-3">
                <!-- Se cargan dinámicamente -->
            </div>
          </div>

        </div>
      </main>
  </div>

  <!-- Modal de Confirmación de Eliminación -->
  <div class="modal fade" id="deleteVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-panel border-0 text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25">
          <h5 class="modal-title fw-bold" id="deleteModalTitle">Confirmar Eliminación</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-4">
          <div class="text-center mb-3">
            <i class="bi bi-exclamation-triangle-fill text-warning display-1" id="deleteModalIcon"></i>
          </div>
          <p class="text-center fs-5" id="deleteModalBody">¿Estás seguro de que quieres eliminar este video?</p>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-25 justify-content-center">
          <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger rounded-pill px-4" id="btn-confirm-delete-step">Continuar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Edición de Video -->
  <div class="modal fade" id="editVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-panel border-0 text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25">
          <h5 class="modal-title fw-bold">Editar Video</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-4">
          <form id="form-editar-video">
              <input type="hidden" id="edit-video-id">
              <div class="mb-3">
                  <label for="edit-titulo" class="form-label text-white-50">Título</label>
                  <input type="text" class="form-control form-control-glass" id="edit-titulo" maxlength="30">
              </div>
              <div class="mb-3">
                  <label for="edit-descripcion" class="form-label text-white-50">Descripción</label>
                  <textarea class="form-control form-control-glass" id="edit-descripcion" rows="3" maxlength="300"></textarea>
              </div>
              <div class="mb-3">
                  <label class="form-label text-white-50">Portada</label>
                  <div class="d-flex align-items-center gap-3">
                      <img id="edit-preview-portada" src="" alt="Portada actual" class="rounded" style="width: 100px; height: 60px; object-fit: cover;">
                      <button type="button" class="btn btn-outline-light btn-sm" id="btn-cambiar-portada-edit">
                          <i class="bi bi-image me-2"></i>Cambiar Portada
                      </button>
                      <input type="hidden" id="edit-public-id-portada">
                  </div>
              </div>
          </form>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-25">
          <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success rounded-pill px-4" id="btn-guardar-edicion">Guardar Cambios</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Eliminación de Cuenta (4 Pasos) -->
  <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-panel border-0 text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25">
          <h5 class="modal-title fw-bold" id="deleteAccountTitle">Eliminar Cuenta</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-4 text-center">
          <div class="mb-3">
            <i class="bi display-1" id="deleteAccountIcon"></i>
          </div>
          <p class="fs-5" id="deleteAccountBody"></p>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-25 justify-content-center">
          <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger rounded-pill px-4" id="btn-confirm-delete-account">Continuar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container" style="z-index: 2000;"></div>

  <?php include '../includes/footer.php';?>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="../js/confirm-logout.js"></script>
  <script src="../js/perfil.js"></script>
</body>
</html>
