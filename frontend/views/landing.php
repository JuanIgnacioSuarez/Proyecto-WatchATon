<?php
require_once '../includes/cabecera.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a WatchATon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Cargar estilos personalizados DESPUÉS de bootstrap -->
    <link rel="stylesheet" href="../css/misestilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>

    <!-- Sección Hero -->
    <section class="hero-section d-flex align-items-center justify-content-center text-center position-relative overflow-hidden">
        <div class="container position-relative z-2">
            <div class="row justify-content-center">
                <div class="col-lg-10 fade-in-up">
                    <h1 class="display-1 fw-bold mb-4 brand-text" style="font-size: 5rem;">
                        WatchATon
                    </h1>
                    <p class="lead text-white mb-5 fs-3" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                        Mira videos, acumula puntos y gana premios reales.
                        <br>
                        <span class="text-white-50 fs-5">La nueva forma de entretenimiento recompensado.</span>
                    </p>
                    
                    <div class="mt-5 fade-in-up" style="animation-delay: 0.3s;">
                        <p class="text-white-50 mb-2">Conocer más</p>
                        <a href="#quienes-somos" class="text-white fs-1 bounce-animation d-inline-block">
                            <i class="bi bi-chevron-down"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Elementos Decorativos -->
        <div class="position-absolute top-50 start-0 translate-middle-y opacity-25 d-none d-lg-block">
            <div class="blob-shape bg-primary blur-3xl" style="width: 400px; height: 400px; border-radius: 50%;"></div>
        </div>
        <div class="position-absolute bottom-0 end-0 opacity-25 d-none d-lg-block">
            <div class="blob-shape bg-secondary blur-3xl" style="width: 500px; height: 500px; border-radius: 50%;"></div>
        </div>
    </section>

    <!-- Sección 1: Quienes Somos -->
    <section class="landing-section" id="quienes-somos">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-2 order-lg-1 reveal-on-scroll slide-left">
                    <div class="glass-panel p-5 rounded-5" style="min-height: 400px; display: flex; flex-direction: column; justify-content: center;">
                        <h2 class="display-5 fw-bold mb-4 text-white">¿Quiénes somos?</h2>
                        <p class="fs-5 text-white-50 mb-4">
                            Somos una plataforma innovadora que conecta a creadores de contenido con espectadores apasionados. 
                            En WatchATon, creemos que tu tiempo vale oro. Por eso, hemos creado un ecosistema donde 
                            cada minuto que pasas disfrutando de contenido de calidad se traduce en recompensas tangibles.
                        </p>
                        <p class="fs-5 text-white-50">
                            Nuestra misión es democratizar el entretenimiento y ofrecer una experiencia justa y divertida para todos.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 reveal-on-scroll slide-right">
                    <div class="img-placeholder rounded-5 d-flex align-items-center justify-content-center glass-panel" style="height: 400px; border: 2px dashed rgba(255,255,255,0.2);">
                        <div class="text-center text-white-50">
                            <i class="bi bi-people fs-1 mb-3 d-block"></i>
                            <span>Imagen: Equipo / Comunidad</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección 2: Como empezar a ganar -->
    <section class="landing-section">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 reveal-on-scroll slide-left">
                    <div class="img-placeholder rounded-5 d-flex align-items-center justify-content-center glass-panel" style="height: 400px; border: 2px dashed rgba(255,255,255,0.2);">
                        <div class="text-center text-white-50">
                            <i class="bi bi-coin fs-1 mb-3 d-block"></i>
                            <span>Imagen: Ganando Puntos / Interfaz</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 reveal-on-scroll slide-right">
                    <div class="glass-panel p-5 rounded-5">
                        <h2 class="display-5 fw-bold mb-4 text-white">¿Cómo empezar a ganar?</h2>
                        <ul class="list-unstyled fs-5 text-white-50">
                            <li class="mb-4 d-flex align-items-start">
                                <i class="bi bi-1-circle-fill text-primary me-3 fs-3"></i>
                                <div>
                                    <strong class="text-white d-block mb-1">Regístrate Gratis</strong>
                                    Crea tu cuenta en segundos y únete a nuestra comunidad.
                                </div>
                            </li>
                            <li class="mb-4 d-flex align-items-start">
                                <i class="bi bi-2-circle-fill text-primary me-3 fs-3"></i>
                                <div>
                                    <strong class="text-white d-block mb-1">Mira Anuncios</strong>
                                    Mira tus videos favoritos y tomate un momento para ver el anuncio antes del video.
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="bi bi-3-circle-fill text-primary me-3 fs-3"></i>
                                <div>
                                    <strong class="text-white d-block mb-1">Acumula Puntos</strong>
                                    Cada anuncio visto completo suma puntos automáticamente a tu billetera virtual.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección 3: Como canjear mis puntos -->
    <section class="landing-section mb-5">
        <div class="container-fluid px-4 px-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-2 order-lg-1 reveal-on-scroll slide-left">
                    <div class="glass-panel p-5 rounded-5">
                        <h2 class="display-5 fw-bold mb-4 text-white">¿Cómo canjear mis puntos?</h2>
                        <p class="fs-5 text-white-50 mb-4">
                            ¡Es la mejor parte! Una vez que hayas acumulado suficientes puntos, dirígete a nuestra tienda de recompensas.
                        </p>
                        <div class="d-flex gap-4 mb-4">
                            <div class="text-center">
                                <div class="bg-white bg-opacity-10 rounded-circle p-3 mb-2 mx-auto" style="width: 60px; height: 60px;">
                                    <i class="bi bi-gift fs-3 text-white"></i>
                                </div>
                                <span class="small text-white-50">Tarjetas de Regalo</span>
                            </div>
                            <div class="text-center">
                                <div class="bg-white bg-opacity-10 rounded-circle p-3 mb-2 mx-auto" style="width: 60px; height: 60px;">
                                    <i class="bi bi-cash-coin fs-3 text-white"></i>
                                </div>
                                <span class="small text-white-50">Dinero Real</span>
                            </div>
                            <div class="text-center">
                                <div class="bg-white bg-opacity-10 rounded-circle p-3 mb-2 mx-auto" style="width: 60px; height: 60px;">
                                    <i class="bi bi-stars fs-3 text-white"></i>
                                </div>
                                <span class="small text-white-50">Beneficios VIP</span>
                            </div>
                        </div>
                        
                        <?php if(isset($_COOKIE['iniciado'])): ?>
                            <a href="Canjear.php" class="btn btn-outline-light rounded-pill px-4">Ver Catálogo de Premios</a>
                        <?php else: ?>
                            <a href="IniciarSesion.php" class="btn btn-outline-light rounded-pill px-4">Iniciar Sesión para Ver Premios</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 reveal-on-scroll slide-right">
                    <div class="img-placeholder rounded-5 d-flex align-items-center justify-content-center glass-panel" style="height: 400px; border: 2px dashed rgba(255,255,255,0.2);">
                        <div class="text-center text-white-50">
                            <i class="bi bi-bag-check fs-1 mb-3 d-block"></i>
                            <span>Imagen: Tienda / Premios</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/confirm-logout.js"></script>
    <script src="../js/landing.js"></script>
</body>
</html>
