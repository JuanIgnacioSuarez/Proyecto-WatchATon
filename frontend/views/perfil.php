<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi Perfil - WatchATon</title>
  <link rel="stylesheet" href="../css/misestilos.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-dark text-light d-flex flex-column min-vh-100" style="background-image: url('../assets/images/fondo-suave.avif'); background-size: cover; background-position: center center; background-attachment: fixed;">
  <?php include '../includes/cabecera.php';?>

  <main class="container my-5 flex-grow-1">
    <div class="row">
      <!-- Menú Lateral -->
      <div class="col-md-3">
        <div class="list-group bg-secondary shadow-sm rounded">
          <a href="#" class="list-group-item list-group-item-action bg-secondary text-light active" id="btn-mis-videos"><i class="bi bi-collection-play me-2"></i>Mis videos</a>
          <a href="#" class="list-group-item list-group-item-action bg-secondary text-light" id="btn-mi-info"><i class="bi bi-person me-2"></i>Mi info</a>
          <a href="#" class="list-group-item list-group-item-action bg-secondary text-light" id="btn-mi-cuenta"><i class="bi bi-gear me-2"></i>Mi cuenta</a>
        </div>
      </div>

      <!-- Contenido Principal -->
      <div class="col-md-9">
        <div class="card bg-secondary text-light shadow-sm p-4 rounded" id="content-container">
          <!-- Contenido de "Mis videos" (inicialmente visible) -->
          <div id="mis-videos-content">
            <h4 class="mb-4">Mis Videos</h4>
            <p>Aquí se mostrarán los videos que has subido.</p>
            <!-- Aquí irían los videos del usuario -->
          </div>

          <!-- Contenido de "Mi info" (inicialmente oculto) -->
          <div id="mi-info-content" class="d-none">
            <h4 class="mb-4">Mi Información</h4>
            <div class="mb-3">
              <strong>Nombre de Usuario:</strong> <span id="username-display">UsuarioEjemplo</span>
            </div>
            <div class="mb-3">
              <strong>Foto de Perfil:</strong>
              <img src="../assets/images/logo.jpg" alt="Foto de Perfil" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px;">
            </div>
          </div>

          <!-- Contenido de "Mi cuenta" (inicialmente oculto) -->
          <div id="mi-cuenta-content" class="d-none">
            <h4 class="mb-4">Información de la Cuenta</h4>
            <div class="mb-3">
              <strong>Correo Electrónico:</strong> <span id="email-display">usuario@example.com</span>
            </div>
            <div class="mb-3">
              <strong>Contraseña:</strong> ************
            </div>
            <button class="btn btn-danger mt-3" id="btn-eliminar-cuenta"><i class="bi bi-trash me-2"></i>Eliminar Cuenta</button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../includes/footer.php';?>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="../js/confirm-logout.js"></script>
  <script>
    $(document).ready(function() {
      // Función para mostrar la sección y actualizar el estado activo del menú
      function showSection(sectionId, buttonId) {
        // Oculta todas las secciones de contenido
        $('#mis-videos-content, #mi-info-content, #mi-cuenta-content').addClass('d-none');
        // Muestra la sección deseada
        $(sectionId).removeClass('d-none');

        // Elimina la clase 'active' de todos los botones del menú lateral
        $('.list-group-item-action').removeClass('active');
        // Añade la clase 'active' al botón clickeado
        $(buttonId).addClass('active');
      }

      // Eventos de click para los botones del menú lateral
      $('#btn-mis-videos').on('click', function(e) {
        e.preventDefault();
        showSection('#mis-videos-content', '#btn-mis-videos');
      });

      $('#btn-mi-info').on('click', function(e) {
        e.preventDefault();
        showSection('#mi-info-content', '#btn-mi-info');
      });

      $('#btn-mi-cuenta').on('click', function(e) {
        e.preventDefault();
        showSection('#mi-cuenta-content', '#btn-mi-cuenta');
      });

      // Asegurarse de que "Mis videos" esté activo y visible al cargar
      showSection('#mis-videos-content', '#btn-mis-videos');
    });
  </script>
</body>
</html>



