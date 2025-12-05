<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<header class="glass-header sticky-top">
  <div class="container d-flex flex-wrap align-items-center justify-content-between py-3">
    <div class="d-flex align-items-center">
      <a href="../views/landing.php" class="d-flex align-items-center text-decoration-none">
        <img src="../assets/images/logo.jpg" alt="Logo" width="50" height="50" class="rounded-circle me-3">
        <h1 class="h4 m-0 brand-text">WatchATon</h1>
      </a>
    </div>
    <nav class="nav align-items-center">
      <?php 
        if(isset($_COOKIE['iniciado'])): 
            // Verificar si es admin
            $esAdmin = false;
            
            // Asegurar conexión
            if (!isset($conexion)) {
                // Intentar incluir Conexion.php asumiendo estructura estándar
                // cabecera está en frontend/includes/
                // Conexion está en backend/classes/
                $path = __DIR__ . '/../../backend/classes/Conexion.php';
                if (file_exists($path)) {
                    require_once($path);
                    $conexion = new Conexion();
                }
            }

            if (isset($conexion)) {
                $email = $_COOKIE['iniciado'];
                $sqlAdmin = "SELECT Permisos FROM usuarios WHERE Correo = ?";
                $resAdmin = $conexion->consultar($sqlAdmin, "s", [$email]);
                if (!empty($resAdmin) && $resAdmin[0]['Permisos'] == 1) {
                    $esAdmin = true;
                }
            }

            if ($esAdmin):
      ?>
          <a href="panel_admin.php" class="btn btn-danger fw-bold me-4 px-3 shadow-sm d-flex align-items-center" style="border: 1px solid rgba(255,255,255,0.2);">
              <i class="bi bi-shield-lock-fill me-2 fs-5"></i>PANEL ADMIN
          </a>
      <?php endif; ?>

          <!-- Notificaciones Dropdown -->
          <div class="dropdown d-inline-block position-relative me-3">
              <a href="#" class="nav-link-custom p-0" id="notificacionesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-bell fs-5"></i>
                  <span id="notificaciones-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                      0
                      <span class="visually-hidden">mensajes no leídos</span>
                  </span>
              </a>
              <div class="dropdown-menu dropdown-menu-end glass-panel border-secondary border-opacity-25 p-0 mt-3" aria-labelledby="notificacionesDropdown" style="width: 350px; max-height: 500px; overflow-y: auto;">
                  <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                      <h6 class="mb-0 text-white fw-bold">Notificaciones</h6>
                      <small class="text-white-50" style="cursor: pointer;" onclick="markAllRead()">Marcar todo leído</small>
                  </div>
                  <div id="lista-notificaciones" class="list-group list-group-flush">
                      <!-- Se cargan dinámicamente -->
                      <div class="text-center p-4 text-white-50">
                          <div class="spinner-border spinner-border-sm text-light" role="status"></div> Cargando...
                      </div>
                  </div>
              </div>
          </div>
      <?php endif; ?>

      <a href="../views/index.php" class="nav-link-custom">Inicio</a>
      
      <?php  
        if(isset($_COOKIE['iniciado'])){           //Si la sesion esta iniciado mostramos esto 
          echo '<a href="../views/Canjear.php" class="nav-link-custom"><i class="bi bi-gift me-2"></i>Canjear recompensas!</a>';
          echo '<a href="perfil.php" class="nav-link-custom"><i class="bi bi-person-circle me-2"></i>Mi perfil</a>';
          echo '<a href="../views/SubirVideo.php" class="nav-link-custom"><i class="bi bi-cloud-arrow-up me-2"></i>Subir un video</a>';
          echo '<a href="#" id="logout-button" class="nav-link-custom text-danger" onclick="showLogoutModal()"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>';
        } else {
          echo '<a href="../views/IniciarSesion.php" class="nav-link-custom">Iniciar sesión</a>';
        }
      ?>
    </nav>
  </div>
</header>

<div id="logout-modal" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-panel border-0 text-white">
      <div class="modal-header border-bottom border-secondary border-opacity-25">
        <h5 class="modal-title fw-bold">Cerrar Sesión</h5>
        <button type="button" class="btn-close btn-close-white" onclick="closeLogoutModal()" aria-label="Close"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <i class="bi bi-box-arrow-right display-1 text-danger mb-3"></i>
        <p class="fs-5">¿Seguro que quieres cerrar sesión?</p>
      </div>
      <div class="modal-footer border-top border-secondary border-opacity-25 justify-content-center">
        <button id="cancel-logout" class="btn btn-outline-light rounded-pill px-4" onclick="closeLogoutModal()">Cancelar</button>
        <button id="confirm-logout" class="btn btn-danger rounded-pill px-4">Cerrar Sesión</button>
      </div>
    </div>
  </div>
</div>
<script src="../js/notificaciones.js"></script>