<!DOCTYPE html>             <!--Este archivo es el inicio , aqui el usuario puede buscar un video y seleccionarlo, lo que lo rederijira al video-->
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../css/misestilos.css">
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		 <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js"></script>
	<title>Videos</title>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include '../includes/cabecera.php';?>

<div class="container my-4">
  <div class="mb-4">
    <label for="titulo" class="form-label fs-3">Título del video <small class="text-muted">(Deje vacío para ver todos)</small></label>
    <input type="text" id="titulo" name="titulo" class="form-control form-control-lg">
  </div>
  <button class="btn btn-primary mb-4" id="buscar">Buscar</button>

  <div id="videos" class="row gy-4">
  </div>
</div>

<?php include '../includes/footer.php';?>
<script src="../js/index.js"></script>
<script src="../js/confirm-logout.js"></script>
</body>
</html>