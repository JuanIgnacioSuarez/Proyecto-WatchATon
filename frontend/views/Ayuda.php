<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../css/misestilos.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <title>Ayuda y Preguntas Frecuentes</title>
</head>
<body class="bg-dark text-light d-flex flex-column min-vh-100" style="background-image: url('../assets/images/fondo-suave.avif'); background-size: cover; background-position: center center; background-attachment: fixed;">
<?php include '../includes/cabecera.php';?>

<!-- Sección de Explicación de Puntos y Preguntas Frecuentes -->
<main class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
  <div class="card bg-dark text-light shadow-lg p-4" style="max-width: 800px; width: 100%;">
    <h3 class="text-center mb-4">Gana Puntos y Canjea Recompensas</h3>
    <p class="lead text-center mb-4">
      ¡En WatchATon, ver videos tiene su recompensa! Gana puntos por cada anuncio que visualices por completo y úsalos para canjear increíbles premios.
    </p>

    <h4 class="mt-5 mb-3">Preguntas Frecuentes</h4>
    <div class="accordion accordion-flush" id="faqAccordion">
      <div class="accordion-item bg-secondary text-light mb-2 rounded">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button bg-secondary text-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            ¿Cómo gano puntos?
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Al visualizar los anuncios por completo, recibirás puntos. Recuerda iniciar sesión para que tus puntos se registren correctamente.
          </div>
        </div>
      </div>
      <div class="accordion-item bg-secondary text-light rounded">
        <h2 class="accordion-header" id="headingTwo">
          <button class="accordion-button bg-secondary text-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            ¿Cómo canjeo mis puntos?
          </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            En la parte superior, verás la barra de navegación. Desde allí, podrás acceder a la sección "Canjear recompensas" y canjear tus puntos por los premios disponibles.
          </div>
        </div>
      </div>
      <div class="accordion-item bg-secondary text-light mb-2 rounded">
        <h2 class="accordion-header" id="headingThree">
          <button class="accordion-button bg-secondary text-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
            ¿Los puntos caducan?
          </button>
        </h2>
        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            No, tus puntos en WatchATon no caducan. Puedes acumularlos y canjearlos cuando desees.
          </div>
        </div>
      </div>
      <div class="accordion-item bg-secondary text-light rounded">
        <h2 class="accordion-header" id="headingFour">
          <button class="accordion-button bg-secondary text-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
            ¿Puedo subir cualquier tipo de video?
          </button>
        </h2>
        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Puedes subir videos en formato MP4 que cumplan con nuestras directrices de contenido. Asegúrate de que no contengan material inapropiado o que infrinja derechos de autor.
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include '../includes/footer.php';?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>
