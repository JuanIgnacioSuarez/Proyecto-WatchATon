<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ayuda y FAQ - WatchATon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <!-- Cargar estilos personalizados DESPUÉS de bootstrap -->
  <link rel="stylesheet" href="../css/misestilos.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
  <!-- Fondo Animado Global -->
  <div class="mesh-loader">
      <div class="mesh-gradient"></div>
  </div>

  <?php include '../includes/cabecera.php';?>

  <main class="container py-5 flex-grow-1 position-relative z-2">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-8">
        
        <!-- Encabezado de la Página -->
        <div class="text-center mb-5 fade-in-up">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-white bg-opacity-10 mb-3 border border-white border-opacity-10 shadow-lg">
                <i class="bi bi-life-preserver text-primary display-4"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">¿Cómo podemos ayudarte?</h1>
            <p class="lead text-white-50">Encuentra respuestas rápidas y soporte para disfrutar al máximo de WatchATon.</p>
        </div>

        <!-- Tarjeta Principal Glassmorphism -->
        <div class="glass-panel p-4 p-md-5 rounded-4 mb-5 fade-in-up" style="animation-delay: 0.1s;">
            
            <!-- Sección de Introducción -->
            <div class="text-center mb-5">
                <h3 class="fw-bold text-white mb-3">Gana Puntos y Canjea Recompensas</h3>
                <p class="text-white-50">
                    ¡En WatchATon, tu tiempo vale oro! Gana puntos por cada anuncio que visualices por completo y úsalos para canjear increíbles premios en nuestra tienda exclusiva.
                </p>
            </div>

            <hr class="border-secondary opacity-25 my-5">

            <!-- Sección de Preguntas Frecuentes -->
            <h4 class="fw-bold text-white mb-4 d-flex align-items-center">
                <i class="bi bi-question-circle-fill text-accent me-2"></i>Preguntas Frecuentes
            </h4>
            
            <div class="accordion accordion-glass" id="faqAccordion">
              
              <!-- Pregunta 1 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    <i class="bi bi-coin me-3 text-warning"></i>¿Cómo gano puntos?
                  </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    Es muy sencillo. Solo necesitas ver los anuncios completos que aparecen antes de los videos. Una vez finalizado el anuncio, los puntos se sumarán automáticamente a tu cuenta. <strong>Importante:</strong> Debes haber iniciado sesión para que los puntos se registren.
                  </div>
                </div>
              </div>

              <!-- Pregunta 2 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <i class="bi bi-gift me-3 text-danger"></i>¿Cómo canjeo mis puntos?
                  </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    Dirígete a la sección <strong>"Canjear recompensas"</strong> en el menú principal. Allí encontrarás un catálogo de premios disponibles. Si tienes suficientes puntos, simplemente haz clic en "Canjear" y sigue las instrucciones.
                  </div>
                </div>
              </div>

              <!-- Pregunta 3 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <i class="bi bi-clock-history me-3 text-info"></i>¿Los puntos caducan?
                  </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    ¡No! Tus puntos son tuyos para siempre. No tienen fecha de caducidad, así que puedes acumularlos con calma hasta alcanzar el premio que deseas.
                  </div>
                </div>
              </div>

              <!-- Pregunta 4 -->
              <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    <i class="bi bi-camera-video me-3 text-success"></i>¿Puedo subir cualquier tipo de video?
                  </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    Aceptamos la mayoría de los videos en formato <strong>MP4</strong>. Sin embargo, tenemos una política estricta contra contenido ofensivo, violento o que infrinja derechos de autor. Te recomendamos leer nuestras <a href="directrices.php" class="text-accent text-decoration-none fw-bold">directrices de comunidad</a> antes de subir contenido.
                  </div>
                </div>
              </div>

            </div>
        </div>

        <!-- Sección de Contacto Rápido -->
        <div class="row g-4 fade-in-up" style="animation-delay: 0.2s;">
            <div class="col-md-6">
                <div class="glass-panel p-4 rounded-4 h-100 text-center hover-scale">
                    <i class="bi bi-envelope-paper text-primary display-5 mb-3"></i>
                    <h5 class="text-white fw-bold">Soporte por Correo</h5>
                    <p class="text-white-50 small mb-3">¿Tienes un problema técnico? Escríbenos.</p>
                    <p class="text-white fw-bold mb-0">WatchATonSoporte@gmail.com</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glass-panel p-4 rounded-4 h-100 text-center hover-scale">
                    <i class="bi bi-discord text-primary display-5 mb-3"></i>
                    <h5 class="text-white fw-bold">Comunidad Discord</h5>
                    <p class="text-white-50 small mb-3">Únete a nuestra comunidad y chatea en vivo.</p>
                    <a href="#" class="btn btn-gradient rounded-pill px-4 btn-sm">Unirse al Servidor</a>
                    <p class="text-white-50 small mt-2 fst-italic">(Ilustrativo)</p>
                </div>
            </div>
        </div>

      </div>
    </div>
  </main>

  <!-- Contenedor de Toasts -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container" style="z-index: 2000;"></div>

  <?php include '../includes/footer.php';?>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="../js/confirm-logout.js"></script>
</body>
</html>
