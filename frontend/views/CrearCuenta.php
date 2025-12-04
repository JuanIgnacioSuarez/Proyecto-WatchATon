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

  <script src="../js/firebase-config.js"></script>
  <link rel="stylesheet" href="../css/misestilos.css">

  <title>Crear cuenta !</title>
</head>

<body class="d-flex flex-column min-vh-100">

<?php include '../includes/cabecera.php'; ?>

<div id="toast-container"></div>

<main class="d-flex align-items-center justify-content-center min-vh-100">
  <section class="container" style="max-width: 450px;">
    <div class="glass-panel p-5 text-center">
      <h2 class="mb-4 fw-bold text-white">Crear una cuenta</h2>
      <p class="text-white-50 mb-4">Únete a nuestra comunidad hoy mismo</p>

      <div class="mb-3 text-start">
        <label for="email" class="form-label text-white">Dirección de correo:</label>
        <input type="email" class="form-control form-control-glass" id="email" name="email" placeholder="ejemplo@gmail.com">
      </div>

      <div class="mb-4 text-start">
        <label for="contra" class="form-label text-white">Contraseña:</label>
        <input type="password" id="contra" name="contra" class="form-control form-control-glass" placeholder="••••••••">
        <div class="form-text text-white-50 mt-2">Mínimo 6 caracteres</div>
      </div>

      <div class="d-grid gap-2 mb-4">
        <button type="button" name="crear" id="crear" class="btn btn-gradient btn-lg">Crear cuenta</button>
      </div>

      <p class="text-white-50 mb-0">
        ¿Ya tienes cuenta? <a href="../views/IniciarSesion.php" class="text-primary text-decoration-none fw-bold">Inicia sesión</a>
      </p>

    </div>
  </section>
</main>

<?php include '../includes/footer.php'; ?>

<script src="../js/create-account.js"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>