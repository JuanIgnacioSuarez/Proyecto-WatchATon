<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" href="../css/misestilos.css">
	<title>Canjear recompensas</title>
</head>
<body class="d-flex flex-column min-vh-100">
  <!-- Fondo Animado Global -->
  <div class="mesh-loader">
      <div class="mesh-gradient"></div>
  </div>

<?php include('../includes/cabecera.php');?>

<main class="container py-5 flex-grow-1 position-relative z-2">
    
    <!-- Header General -->
    <div class="glass-panel p-4 rounded-4 mb-5 fade-in-up mx-auto text-center" style="max-width: 800px;">
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-white bg-opacity-10 mb-3 border border-white border-opacity-10 shadow-lg">
            <i class="bi bi-gift text-accent display-4"></i>
        </div>
        <h2 class="display-4 fw-bold text-white mb-3">Tienda de Recompensas</h2>
        <p class="lead text-white-50 mb-0">Canjea tus puntos por premios exclusivos.</p> 
        <div class="mt-3">
            <span class="badge bg-primary bg-opacity-25 border border-primary text-white fs-5 px-4 py-2" id="user-points-badge">
                <i class="bi bi-coin me-2"></i>Tus puntos: <span id="user-points-val">...</span>
            </span>
        </div>
    </div>

    <!-- Sección Internas -->
    <div class="mb-5">
        <h3 class="text-white fw-bold mb-4 ps-3 border-start border-4 border-info">Recompensas Internas</h3>
        <p class="text-white-50 mb-4 ps-3">Beneficios exclusivos dentro de nuestra plataforma.</p>
        <div class="row g-4" id="internal-rewards">
            <!-- Carga dinámica -->
        </div>
    </div>

    <!-- Sección Externas -->
    <div class="mb-5">
        <h3 class="text-white fw-bold mb-4 ps-3 border-start border-4 border-warning">Recompensas Externas</h3>
        <p class="text-white-50 mb-4 ps-3">Canjea tus puntos por beneficios en sitios aliados.</p>
        <div class="row g-4" id="external-rewards">
            <!-- Carga dinámica -->
        </div>
    </div>

</main>

    <!-- Modal Confirmación -->
    <div class="modal fade" id="confirmRedemptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-panel border-0 text-white">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title fw-bold">Confirmar Canje</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-box-arrow-up-right text-warning display-1"></i>
                    </div>
                    <h4 class="mb-3">¿Vas a salir de WatchATon?</h4>
                    <p class="text-white-50 mb-4">
                        Al canjear esta recompensa, serás redirigido a un sitio externo (<span id="modal-external-link" class="text-info"></span>).
                        <br>¿Estás seguro de continuar?
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-gradient rounded-pill px-4" id="btn-proceed-redemption">
                            Confirmar y Canjear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmación Interna -->
    <div class="modal fade" id="confirmInternalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-panel border-0 text-white">
                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title fw-bold">Confirmar Canje Interno</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-gift-fill text-primary display-1"></i>
                    </div>
                    <h4 class="mb-3">¿Canjear Recompensa?</h4>
                    <p class="text-white-50 mb-4">
                        Se descontarán <span id="modal-internal-cost" class="fw-bold text-white"></span> puntos de tu cuenta.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-gradient rounded-pill px-4" id="btn-proceed-internal">
                            Sí, Canjear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Status (Exito/Error) -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-panel border-0 text-white">
                <div class="modal-body py-5 text-center">
                    <div class="mb-4">
                        <i id="status-icon" class="bi display-1"></i>
                    </div>
                    <h3 id="status-title" class="fw-bold mb-3"></h3>
                    <p id="status-msg" class="text-white-50 mb-4"></p>
                    <button type="button" class="btn btn-outline-light rounded-pill px-5" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>


<?php include('../includes/footer.php');?>
<script src="../js/canjear.js"></script>
<script src="../js/confirm-logout.js"></script>

</body>
</html>