<?php
require_once('../classes/Conexion.php');

$conexion = new Conexion();

// Traer todos los beneficios
$sql = "SELECT * FROM beneficios";
$resultado = $conexion->consultar($sql);

// Recuperar el id del usuario
$id_usuario = $conexion->existeDato('usuarios', 'ID', 'correo', $_COOKIE['iniciado']);

// Buscar los puntos del usuario
$puntosUsuario = $conexion->existeDato('usuarios', 'Puntos', 'ID', $id_usuario);

// Buscar todos los beneficios que ya canjeó el usuario
$sql = "SELECT ID_beneficio FROM canjeos WHERE ID_usuario = ?";
$tipos = "i";
$parametros = [$id_usuario];
$yaCanjeados = $conexion->consultar($sql, $tipos, $parametros);

$beneficiosCanjeados = [];
foreach ($yaCanjeados as $i) {
    $beneficiosCanjeados[] = $i['ID_beneficio'];
}

echo '
<div class="glass-panel p-4 rounded-4 mb-5 fade-in-up mx-auto" style="max-width: 800px;">
    <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-white bg-opacity-10 mb-3 border border-white border-opacity-10 shadow-lg">
        <i class="bi bi-gift text-accent display-4"></i>
    </div>
    <h2 class="display-4 fw-bold text-white mb-3">Tienda de Recompensas</h2>
    <p class="lead text-white-50 mb-0">Canjea tus puntos por premios exclusivos. <span class="badge bg-primary bg-opacity-25 border border-primary text-white ms-2">Tus puntos: '.htmlspecialchars($puntosUsuario).'</span></p>
</div>
<div class="row justify-content-center">';

foreach($resultado as $index => $i){        //Recorremos los beneficios y los mostramos
    // Calcular retraso de animación
    $delay = ($index * 0.1) . 's';
    
    echo '
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="glass-panel h-100 rounded-4 overflow-hidden hover-scale fade-in-up" style="animation-delay: '.$delay.';">
        <div class="p-4 text-center border-bottom border-white border-opacity-10">
            <i class="bi bi-trophy-fill display-1 text-warning mb-3 d-block drop-shadow"></i>
        </div>
        <div class="p-4 d-flex flex-column h-100">
          <h5 class="card-title text-white fw-bold text-center mb-4">'.htmlspecialchars($i['Descripcion']).'</h5>
          <div class="mt-auto">';

        if(in_array($i['ID_beneficio'],$beneficiosCanjeados)){       //En caso de que se encuentre en el array , significa que ya lo canejo
            echo '<button class="btn btn-secondary w-100 rounded-pill" disabled>
                    <i class="bi bi-check-circle-fill me-2"></i>Canjeado
                  </button>';
        } else {
            echo '<a href="direccionbeneficio.php?id='.$i['ID_beneficio'].'" class="btn btn-gradient w-100 rounded-pill">
                    <i class="bi bi-cart-plus-fill me-2"></i>Canjear
                  </a>';
        }

    echo ' 
          </div>
        </div>
      </div>
    </div>';
}

echo '</div>';
?>