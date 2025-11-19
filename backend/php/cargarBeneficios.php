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
<div class="text-center my-4">
  <h2 class="fw-bold" style="color: #333;">Beneficios 🎁 </h2>
  <p class="text-muted">Tus puntos:'.htmlspecialchars($puntosUsuario).'</p>
</div>
<div class="row justify-content-center">';

foreach($resultado as $i){        //Recorremos los beneficios y los mostramos
    echo '
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card h-100 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title">'.htmlspecialchars($i['Descripcion']).'</h5>
        </div>';

        if(in_array($i['ID_beneficio'],$beneficiosCanjeados)){       //En caso de que se encuentre en el array , significa que ya lo canejo , por lo que no puede volver a canjearlo
            echo '<div class="card-footer bg-white border-0 text-center">
                    <button class="btn btn-secondary w-100" disabled><i class="bi bi-check-circle"></i> Ya tienes este beneficio</button>
                  </div>';
        } else {
            echo '<div class="card-footer bg-white border-0 text-center">
                    <a href="direccionbeneficio.php?id='.$i['ID_beneficio'].'" class="btn btn-success w-100">Obtener beneficio</a>
                  </div>';
        }

    echo ' 
      </div>
    </div>';
}

echo '</div>';
?>