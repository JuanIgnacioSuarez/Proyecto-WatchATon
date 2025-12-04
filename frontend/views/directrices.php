<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Directrices de la Comunidad - WatchATon</title>
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
        
        <!-- Encabezado -->
        <div class="text-center mb-5 fade-in-up">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-white bg-opacity-10 mb-3 border border-white border-opacity-10 shadow-lg">
                <i class="bi bi-shield-check text-success display-4"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">Directrices de la Comunidad</h1>
            <p class="lead text-white-50">Para mantener WatchATon como un espacio seguro y divertido para todos, te pedimos que sigas estas reglas fundamentales.</p>
        </div>

        <!-- Tarjeta de Tolerancia Cero -->
        <div class="glass-panel p-4 p-md-5 rounded-4 mb-4 fade-in-up" style="animation-delay: 0.1s;">
            <div class="d-flex align-items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="bi bi-slash-circle-fill text-danger display-5"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-white mb-2">Tolerancia Cero al Odio y Violencia</h3>
                    <p class="text-white-50 mb-0">
                        No toleramos ningún tipo de contenido que incite al odio, la violencia, la discriminación o el acoso hacia individuos o grupos basados en su raza, etnia, religión, discapacidad, género, edad, orientación sexual o identidad de género. Cualquier contenido de esta naturaleza será eliminado inmediatamente.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Copyright -->
        <div class="glass-panel p-4 p-md-5 rounded-4 mb-4 fade-in-up" style="animation-delay: 0.2s;">
            <div class="d-flex align-items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="bi bi-badge-cc-fill text-warning display-5"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-white mb-2">Respeto a los Derechos de Autor</h3>
                    <p class="text-white-50 mb-0">
                        Sube solo contenido que hayas creado tú mismo o para el cual tengas permiso de uso. Respetamos estrictamente la propiedad intelectual. La infracción repetida de derechos de autor resultará en la eliminación de tu contenido y posibles sanciones a tu cuenta.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Respeto Mutuo -->
        <div class="glass-panel p-4 p-md-5 rounded-4 mb-4 fade-in-up" style="animation-delay: 0.3s;">
            <div class="d-flex align-items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="bi bi-heart-fill text-accent display-5"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-white mb-2">Respeto Mutuo</h3>
                    <p class="text-white-50 mb-0">
                        Trata a los demás usuarios con amabilidad y respeto. Los comentarios ofensivos, el trolling y el comportamiento tóxico no tienen cabida en nuestra comunidad. Fomentamos un ambiente positivo donde todos puedan disfrutar y compartir.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Sanciones -->
        <div class="bg-danger bg-opacity-10 border border-danger border-opacity-25 p-4 rounded-4 text-center fade-in-up" style="animation-delay: 0.4s;">
            <h5 class="text-danger fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Consecuencias del Incumplimiento</h5>
            <p class="text-white-50 mb-0 small">
                El incumplimiento de cualquiera de estas normas puede llevar a la eliminación de contenido, advertencias, y en casos graves o reiterados, a la <strong>suspensión temporal o permanente</strong> de tu cuenta de WatchATon.
            </p>
        </div>

      </div>
    </div>
  </main>

  <?php include '../includes/footer.php';?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="../js/confirm-logout.js"></script>
</body>
</html>
