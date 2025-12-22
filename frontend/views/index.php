<!DOCTYPE html>             <!--Este archivo es el inicio , aqui el usuario puede buscar un video y seleccionarlo, lo que lo rederijira al video-->
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js"></script>
	<link rel="stylesheet" href="../css/misestilos.css">
	<title>Videos</title>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include '../includes/cabecera.php';?>

<div class="container my-5">
  <div class="search-container text-center">
    <h2 class="mb-4 fw-bold">Explora nuestro contenido</h2>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="mb-4 text-start">
          <label for="titulo" class="form-label">¿Qué quieres ver hoy?</label>
          <input type="text" id="titulo" name="titulo" class="form-control form-control-glass form-control-lg" placeholder="Buscar videos...">
        </div>
        <button class="btn btn-gradient btn-lg w-100" id="buscar">
          <i class="bi bi-search me-2"></i>Buscar
        </button>
      </div>
    </div>
  </div>

  <div id="videos" class="row gy-4">
  </div>
</div>

<?php include '../includes/footer.php';?>
<script src="../js/index.js"></script>
<script src="../js/confirm-logout.js"></script>

</body>
</html>