<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-analytics.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-auth.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script> 
  <script src="../js/firebase-config.js"></script>
  <link rel="stylesheet" href="../css/misestilos.css">
  <title>Inicio de sesión</title>
</head>

<body class="d-flex flex-column min-vh-100">

<?php include '../includes/cabecera.php'; ?>

<div id="toast-container"></div>

<main class="d-flex align-items-center justify-content-center min-vh-100">
  <section class="container" style="max-width: 450px;">
    <div class="glass-panel p-5 text-center">
      <h2 class="mb-4 fw-bold text-white">Bienvenido de nuevo</h2>
      <p class="text-white-50 mb-4">Ingresa tus credenciales para continuar</p>

      <div class="mb-3 text-start">
        <label for="email" class="form-label text-white">Correo electrónico</label>
        <input type="email" class="form-control form-control-glass" id="email" name="email" placeholder="nombre@ejemplo.com">
      </div>

      <div class="mb-4 text-start">
        <label for="contra" class="form-label text-white">Contraseña</label>
        <input type="password" id="contra" name="contra" class="form-control form-control-glass" placeholder="••••••••">
      </div>

      <div class="mb-4">
        <div class="g-recaptcha d-flex justify-content-center" data-sitekey="6LfnHUIrAAAAAKhJF3MQGWNEGLrqyfkUk5cwndqY"></div>
      </div>

      <div class="d-grid gap-2 mb-4">
        <button type="button" name="iniciar" id="iniciar" class="btn btn-gradient btn-lg">
            Iniciar sesión
        </button>
        <div id="loader" style="display:none;" class="spinner-border text-primary mx-auto mt-2" role="status">
          <span class="visually-hidden">Cargando</span>
        </div>
      </div>

      <p class="text-white-50 mb-0">
        ¿No tienes cuenta? <a href="../views/CrearCuenta.php" class="text-primary text-decoration-none fw-bold">Regístrate aquí</a>
      </p>
    </div>
  </section>
</main>

<?php include '../includes/footer.php'; ?>
<script src="../js/login.js"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>