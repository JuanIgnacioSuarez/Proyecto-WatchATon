<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<header class="glass-header sticky-top">
  <div class="container d-flex flex-wrap align-items-center justify-content-between py-3">
    <div class="d-flex align-items-center">
      <a href="../views/landing.php" class="d-flex align-items-center text-decoration-none">
        <img src="../assets/images/logo.jpg" alt="Logo" width="50" height="50" class="rounded-circle me-3">
        <h1 class="h4 m-0 brand-text">WatchATon</h1>
      </a>
    </div>
    <nav class="nav">
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