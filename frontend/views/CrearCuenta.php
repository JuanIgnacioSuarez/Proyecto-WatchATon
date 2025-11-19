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

  <script src="../js/firebase-config.js"></script>

  <title>Crear cuenta !</title>
</head>

<body style="background-image:url('../assets/images/teclado.jpg'); background-size: cover;" class="d-flex flex-column min-vh-100">

<?php include '../includes/cabecera.php'; ?>

<main class="d-flex align-items-center justify-content-center min-vh-100">
  <section class="container" style="max-width: 400px;">
    <div class="card shadow-lg bg-dark text-white">
      <div class="card-body">
        <h2 class="text-center mb-4">Crear una cuenta</h2>

        <div class="mb-3">
          <label for="email" class="form-label">Dirección de correo:</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@gmail.com">
        </div>

        <div class="mb-3">
          <label for="contra" class="form-label">Contraseña:</label>
          <input type="password" id="contra" name="contra" class="form-control" aria-describedby="passwordHelpInline">
        </div>

        <div class="d-grid">
          <button type="button" name="crear" id="crear" class="btn btn-primary">Crear cuenta</button>
        </div>

      </div>
    </div>
  </section>
</main>

<?php include '../includes/footer.php'; ?>

<script src="../js/create-account.js"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>