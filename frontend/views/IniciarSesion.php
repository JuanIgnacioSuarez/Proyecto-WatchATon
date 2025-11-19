<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../css/misestilos.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-analytics.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-auth.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script> 
  <script src="../js/firebase-config.js"></script>
  <title>Inicio de sesión</title>
</head>

<body style="background-image:url('../assets/images/teclado.jpg'); background-size: cover;" class="d-flex flex-column min-vh-100">

<?php include '../includes/cabecera.php'; ?>

<main class="d-flex align-items-center justify-content-center min-vh-100">
  <section class="container" style="max-width: 400px;">
    <div class="card shadow-lg bg-dark text-white">
      <div class="card-body">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>

        <div class="mb-3">
          <label for="email" class="form-label">Dirección de correo:</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@gmail.com">
        </div>

        <div class="mb-3">
          <label for="contra" class="form-label">Contraseña:</label>
          <input type="password" id="contra" name="contra" class="form-control" aria-describedby="passwordHelpInline">
          <div id="passwordHelpInline" class="form-text text-white-50">
            (Tenga cuidado con las mayúsculas)
          </div>
        </div>

        <div class="mb-3">
          <div class="d-flex align-items-center gap-2">
            <div class="g-recaptcha mb-3" data-sitekey="6LfnHUIrAAAAAKhJF3MQGWNEGLrqyfkUk5cwndqY"></div> <!--Es la clave para que funcione el recaptcha-->
          </div>
        </div>

        <div class="d-grid gap-2 mb-3">
          <button type="button" name="iniciar" id="iniciar" class="btn btn-primary">Iniciar sesión</button>
          <div id="loader" style="display:none;" class="spinner-border text-primary mx-auto" role="status">
            <span class="visually-hidden">Cargando</span>
          </div>
        </div>

        <h5 class="text-center">O</h5>

        <div class="d-grid gap-2">
          <button type="button" name="crear" id="crear" onclick="window.location.href='../views/CrearCuenta.php'" class="btn btn-secondary">Crear una nueva cuenta</button>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include '../includes/footer.php'; ?>
<script src="../js/login.js"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>