<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<header class="bg-dark text-light sticky-top">
  <div class="container d-flex flex-wrap align-items-center justify-content-between py-3">
    <div class="d-flex align-items-center">
      <a href="../views/Ayuda.php" class="d-flex align-items-center text-light text-decoration-none">
        <img src="../assets/images/logo.jpg" alt="Logo" width="50" height="50" class="rounded-circle me-2">
        <h1 class="h4 m-0">WatchATon</h1>
      </a>
    </div>
    <nav class="nav">
      <a href="../views/index.php" class="nav-link text-light">Inicio</a>
      <?php  
        if(isset($_COOKIE['iniciado'])){           //Si la sesion esta iniciado mostramos esto 
         echo '<a href="../views/Canjear.php" class="nav-link text-light"><i class="bi bi-gift me-2"></i>Canjear recompensas!</a>';
          echo '<a href="perfil.php" class="nav-link text-light"><i class="bi bi-person-circle me-2"></i>Mi perfil</a>';
                   echo '<a href="../views/SubirVideo.php" class="nav-link text-light"><i class="bi bi-cloud-arrow-up me-2"></i>Subir un video</a>';
          echo '<a href="#" id="logout-button" class="nav-link text-danger" onclick="showLogoutModal()"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>';
        } else {
          echo '<a href="../views/IniciarSesion.php" class="nav-link text-light">Iniciar sesión</a>';
        }
      ?>
    </nav>
  </div>
</header>

<div id="logout-modal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeLogoutModal()">&times;</span>
    <p>¿Seguro que quieres cerrar sesión?</p>
    <div class="d-flex justify-content-center mt-4">
      <button id="confirm-logout" class="btn btn-success btn-sm me-2">Aceptar</button>
      <button id="cancel-logout" class="btn btn-danger btn-sm">Cancelar</button>
    </div>
  </div>
</div>