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
        <div class="container px-4 px-lg-5">
            <div class="row w-100 m-0 justify-content-center">
                <div class="col-lg-10 reveal-on-scroll slide-up">
                    <div class="glass-panel p-5 rounded-5 text-center position-relative overflow-hidden">
                        <!-- Decorative bg -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary-soft opacity-10" style="pointer-events: none;"></div>
                        
                        <h2 class="display-4 fw-bold mb-4 text-white">¿Quiénes somos?</h2>
                        <div class="d-flex justify-content-center mb-4">
                            <div class="bg-primary rounded-pill" style="width: 60px; height: 4px;"></div>
                        </div>
                        <p class="fs-4 text-white-50 mb-4 lh-lg">
                            Somos una plataforma innovadora que conecta a creadores de contenido con espectadores apasionados. 
                            En <span class="text-white fw-bold">WatchATon</span>, creemos que tu tiempo vale oro. Por eso, 
                            <span class="text-white">hemos transformado la publicidad</span>: dejamos atrás la idea del anuncio como una barrera molesta y lo convertimos en un 
                            <span class="text-warning fw-bold">beneficio tangible para ti</span>.
                        </p>
                        <p class="fs-5 text-white-50">
                            Aquí, cada segundo que inviertes en ver contenido no es tiempo perdido, sino recompensas reales que van directo a tu bolsillo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección 2: Como empezar a ganar -->
    <section class="landing-section">
        <div class="container px-4 px-lg-5">
            <div class="text-center mb-5 reveal-on-scroll slide-up">
                <h2 class="display-4 fw-bold text-white mb-3">¿Cómo empezar a ganar?</h2>
                <p class="fs-5 text-white-50">Es tan fácil como contar hasta tres</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Paso 1 -->
                <div class="col-md-6 col-lg-3 reveal-on-scroll slide-up" style="transition-delay: 100ms;">
                    <div class="glass-panel p-4 h-100 rounded-5 text-center hover-scale">
                        <div class="mb-4 d-inline-block p-3 rounded-circle bg-primary bg-opacity-25 border border-primary border-opacity-50">
                            <i class="bi bi-person-plus-fill fs-2 text-white"></i>
                        </div>
                        <h3 class="h5 text-white fw-bold mb-3">1. Regístrate Gratis</h3>
                        <p class="text-white-50 small">Crea tu cuenta en segundos y únete a nuestra comunidad.</p>
                    </div>
                </div>

                <!-- Paso 2 (Nuevo) -->
                <div class="col-md-6 col-lg-3 reveal-on-scroll slide-up" style="transition-delay: 200ms;">
                    <div class="glass-panel p-4 h-100 rounded-5 text-center hover-scale">
                        <div class="mb-4 d-inline-block p-3 rounded-circle bg-info bg-opacity-25 border border-info border-opacity-50">
                            <i class="bi bi-search fs-2 text-white"></i>
                        </div>
                        <h3 class="h5 text-white fw-bold mb-3">2. Explora</h3>
                        <p class="text-white-50 small">Busca y descubre los videos que realmente te apasionan.</p>
                    </div>
                </div>

                <!-- Paso 3 (Modificado) -->
                <div class="col-md-6 col-lg-3 reveal-on-scroll slide-up" style="transition-delay: 300ms;">
                    <div class="glass-panel p-4 h-100 rounded-5 text-center hover-scale">
                        <div class="mb-4 d-inline-block p-3 rounded-circle bg-warning bg-opacity-25 border border-warning border-opacity-50">
                            <i class="bi bi-hourglass-split fs-2 text-white"></i>
                        </div>
                        <h3 class="h5 text-white fw-bold mb-3">3. Tómate un tiempo</h3>
                        <p class="text-white-50 small">Mira un anuncio corto antes de tu video. Es solo un momento.</p>
                    </div>
                </div>

                <!-- Paso 4 -->
                <div class="col-md-6 col-lg-3 reveal-on-scroll slide-up" style="transition-delay: 400ms;">
                    <div class="glass-panel p-4 h-100 rounded-5 text-center hover-scale">
                        <div class="mb-4 d-inline-block p-3 rounded-circle bg-success bg-opacity-25 border border-success border-opacity-50">
                            <i class="bi bi-coin fs-2 text-white"></i>
                        </div>
                        <h3 class="h5 text-white fw-bold mb-3">4. Acumula Puntos</h3>
                        <p class="text-white-50 small">Suma puntos automáticamente a tu billetera y canjéalos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección 3: Como canjear mis puntos -->
    <section class="landing-section mb-5">
        <div class="container px-4 px-lg-5">
            <div class="glass-panel p-5 rounded-5 reveal-on-scroll slide-up">
                <div class="row align-items-center">
                    <div class="col-lg-8 mx-auto text-center">
                        <h2 class="display-4 fw-bold mb-4 text-white">¿Cómo canjear mis puntos?</h2>
                        <p class="fs-4 text-white-50 mb-5">
                            ¡Es la mejor parte! Una vez que hayas acumulado suficientes puntos, dirígete a nuestra tienda de recompensas.
                        </p>
                        
                        <div class="row g-4 mb-5 justify-content-center">
                            <div class="col-4 col-md-3">
                                <div class="bg-white bg-opacity-10 rounded-circle p-3 mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-gift fs-1 text-white"></i>
                                </div>
                                <span class="d-block text-white fw-bold">Tarjetas Regalo</span>
                            </div>
                            <div class="col-4 col-md-3">
                                <div class="bg-white bg-opacity-10 rounded-circle p-3 mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-cash-coin fs-1 text-white"></i>
                                </div>
                                <span class="d-block text-white fw-bold">Dinero Real</span>
                            </div>
                            <div class="col-4 col-md-3">
                                <div class="bg-white bg-opacity-10 rounded-circle p-3 mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-stars fs-1 text-white"></i>
                                </div>
                                <span class="d-block text-white fw-bold">Beneficios VIP</span>
                            </div>
                        </div>

                        <?php if(isset($_COOKIE['iniciado'])): ?>
                            <a href="Canjear.php" class="btn btn-light rounded-pill px-5 py-3 fw-bold fs-5 shadow hover-scale">
                                <i class="bi bi-cart-check-fill me-2"></i>Ver Catálogo
                            </a>
                        <?php else: ?>
                            <a href="IniciarSesion.php" class="btn btn-primary rounded-pill px-5 py-3 fw-bold fs-5 shadow hover-scale">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/confirm-logout.js"></script>
    <script src="../js/landing.js"></script>
</body>
</html>
