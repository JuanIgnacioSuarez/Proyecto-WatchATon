<?php
require_once('../../backend/php/controladorPerfilPublico.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Perfil de <?php echo $displayName; ?> - WatchATon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <!-- Cargar estilos personalizados DESPUÉS de bootstrap -->
  <link rel="stylesheet" href="../css/misestilos.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body class="d-flex flex-column min-vh-100">
  <!-- Fondo Animado -->
  <div class="mesh-loader">
      <div class="mesh-gradient"></div>
  </div>

  <?php include '../includes/cabecera.php';?>

  <div class="container py-5 position-relative z-2">
      
      <!-- Encabezado de Perfil -->
      <div class="text-center mb-5 fade-in-up">
          <div class="profile-avatar-container mb-4">
              <div class="profile-avatar-glow" style="filter: blur(15px);"></div>
              <img src="<?php echo $profilePicUrl; ?>" alt="Foto de Perfil" class="rounded-circle border border-4 border-white shadow-lg profile-pic-display" style="width: 180px; height: 180px; object-fit: cover;">
          </div>
          <h1 class="text-white fw-bold mb-2 display-5"><?php echo $displayName; ?></h1>
          <p class="text-white-50 mb-0">Miembro de la comunidad</p>
      </div>

      <!-- Sección de Biografía -->
      <div class="row justify-content-center mb-5 fade-in-up" style="animation-delay: 0.1s;">
          <div class="col-12 col-lg-8">
              <div class="glass-panel p-4 rounded-4 text-center position-relative overflow-hidden">
                  <i class="bi bi-quote text-white-50 display-1 position-absolute top-0 start-0 opacity-25 ms-3"></i>
                  <h4 class="text-white fw-bold mb-3">Sobre mí</h4>
                  <p class="text-white fs-5 fst-italic mb-0 px-4" style="line-height: 1.8;"><?php echo $displayBio; ?></p>
              </div>
          </div>
      </div>

      <!-- Sección de Videos -->
      <div class="fade-in-up" style="animation-delay: 0.2s;">
        <div class="d-flex align-items-center mb-4">
            <h3 class="text-white fw-bold mb-0 border-start border-4 border-primary ps-3">Videos de <?php echo $displayName; ?></h3>
        </div>
        
        <div id="lista-videos-publicos" class="row g-4">
            <!-- Aquí se cargarán los videos dinámicamente -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
      </div>

  </div>

  <?php include '../includes/footer.php';?>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="../js/confirm-logout.js"></script>
  <script>
      const idUsuarioPublico = <?php echo $id_usuario; ?>;
  </script>
  <script src="../js/perfilPublico.js"></script>
</body>
</html>
