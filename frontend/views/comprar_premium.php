<?php
// frontend/views/comprar_premium.php
require_once '../../backend/classes/Conexion.php';

// Verificar autenticación
if (!isset($_COOKIE['iniciado'])) {
    header("Location: ../../index.php");
    exit();
}
$email = $_COOKIE['iniciado'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar Premium - WatchATon</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/misestilos.css">
    
    <!-- SDK Mercado Pago V2 -->
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body class="bg-dark text-white">

    <?php include '../includes/cabecera.php'; ?>

    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold text-warning mb-3">
                        <i class="bi bi-star-fill me-2"></i>Hazte Premium
                    </h1>
                    <p class="lead text-white-50">Desbloquea funciones exclusivas y apoya a la comunidad.</p>
                </div>

                <div class="card bg-secondary text-white border-0 shadow-lg">
                    <div class="card-body p-5">
                        
                        <!-- Beneficios -->
                        <div class="mb-4">
                            <h4 class="mb-3">Beneficios Premium:</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Insignia Dorada en tu perfil</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Acceso anticipado a novedades</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Sin publicidad (Próximamente)</li>
                                <li class="mb-2"><i class="bi bi-heart-fill text-danger me-2"></i>Apoyo directo a los creadores</li>
                            </ul>
                        </div>

                        <hr class="border-secondary my-4">

                        <!-- Selector de Plan -->
                        <div class="row g-3 mb-4" id="plan-selection-container">
                            <!-- Plan Mensual -->
                            <div class="col-6">
                                <label class="cursor-pointer w-100">
                                    <input type="radio" name="plan" value="monthly" class="d-none peer-radio" checked>
                                    <div class="card bg-dark border-secondary h-100 plan-card transition-all">
                                        <div class="card-body text-center p-3">
                                            <h5 class="text-white mb-0">Mensual</h5>
                                            <hr class="border-secondary my-2 opacity-25">
                                            <h3 class="fw-bold text-white mb-0">$2.000</h3>
                                            <small class="text-white-50">/mes</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Plan Anual -->
                            <div class="col-6">
                                <label class="cursor-pointer w-100 position-relative">
                                    <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-warning text-dark border border-white small" style="z-index: 10;">
                                        ★ Ahorras $4.000
                                    </span>
                                    <input type="radio" name="plan" value="yearly" class="d-none peer-radio">
                                    <div class="card bg-dark border-secondary h-100 plan-card transition-all">
                                        <div class="card-body text-center p-3">
                                            <h5 class="text-white mb-0">Anual</h5>
                                            <hr class="border-secondary my-2 opacity-25">
                                            <h3 class="fw-bold text-warning mb-0">$20.000</h3>
                                            <small class="text-white-50">/año</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Estilos inline temporales para feedback visual -->
                        <style>
                            .peer-radio:checked + .plan-card {
                                border-color: #ffc107 !important;
                                background-color: rgba(255, 193, 7, 0.1) !important;
                                transform: scale(1.02);
                            }
                            .plan-card:hover {
                                border-color: rgba(255, 255, 255, 0.5) !important;
                            }
                            .transition-all {
                                transition: all 0.2s ease;
                            }
                            .cursor-pointer {
                                cursor: pointer;
                            }
                        </style>

                        <!-- Botón de Pago -->
                        <div id="wallet_container"></div>
                        
                        <div id="loading-payment" class="text-center mt-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 small text-muted">Cargando pasarela de pago...</p>
                        </div>

                    </div>
                </div>

                <!-- Botón de Debug para Simular Pago (Solo Desarrollo) -->
            <div class="mt-4 pt-4 border-top border-secondary border-opacity-25 text-center">
                <p class="text-white-50 small mb-2"><i class="bi bi-bug me-1"></i>Modo Desarrollo</p>
                <div class="alert alert-info bg-opacity-10 border-info border-opacity-25 text-white small">
                    <i class="bi bi-info-circle me-2"></i>
                    Si Mercado Pago no muestra el botón "Volver al sitio", usa este botón para simular que el pago fue exitoso y activar tu Premium.
                </div>
                <!-- Link directo a un script de simulación que crearemos -->
                <a href="../../backend/php/simular_pago_debug.php?plan=monthly" id="btn-debug-pay" class="btn btn-outline-info btn-sm rounded-pill">
                    <i class="bi bi-magic me-2"></i>Simular Pago Exitoso
                </a>
            </div>
            
            <div class="text-center mt-4">
                <a href="perfil.php" class="text-white-50 text-decoration-none small hover-link">
                    <i class="bi bi-arrow-left me-1"></i>Volver a mi perfil
                </a>
            </div>

            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="../js/comprar_premium.js"></script>

</body>
</html>
