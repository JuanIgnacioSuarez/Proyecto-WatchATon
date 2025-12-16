<?php

//Esta primera parte es codigo para utilizar reCatpcha

$LlaveSecreta = "6LfnHUIrAAAAACINEsmvvZLB05dAuXuppiVDquf8";
$LlaveRespuesta = $_POST['g-recaptcha-response'];
$usuarioIP = $_SERVER['REMOTE_ADDR'];

$url = "https://www.google.com/recaptcha/api/siteverify?secret=$LlaveSecreta&response=$LlaveRespuesta&remoteip=$usuarioIP";

$respuesta = file_get_contents($url);
$respuesta = json_decode($respuesta);

$dominiosPermitidos = ['gmail.com', 'outlook.com', 'hotmail.com'];  //Solo permito este tipo de dominios de correo

if(!$respuesta->success){  //Si el captcha es incorrecto
	 echo json_encode(['status' => 'error', 'message' => 'malcaptcha']);
}
else{
    if(empty($_POST['email']) || empty($_POST['contra'])){        //Verificar que ambos campos tengan algo		
		 echo json_encode(['status' => 'error', 'message' => 'faltandatos']);
	}
	else{
		$partes=explode('@',$_POST['email']);			//Divido el email en dos partes , el dominio y el cuerpo
		$dominio=strtolower($partes[1]);			//Convierto la parte del dominio en minusculas
		if(in_array($dominio,$dominiosPermitidos)){     //Verifica si el dominio es valido
			
			// Verificar si es admin
			require_once('../classes/Conexion.php');
			$conexion = new Conexion();
			$sql = "SELECT ID, Permisos FROM usuarios WHERE Correo = ?";
			$datos = $conexion->consultar($sql, "s", [$_POST['email']]);
			
			$es_admin = false;
			$es_premium = false;

			if (!empty($datos)) {
				if ($datos[0]['Permisos'] == 1) {
					$es_admin = true;
				}

				// Verificar si es Premium (Tiene canjeo con ID_beneficio = 0)
				$idUsuario = $datos[0]['ID'];
				$sqlPremium = "SELECT COUNT(*) as total FROM canjeos WHERE ID_usuario = ? AND ID_beneficio = 0";
				$resPremium = $conexion->consultar($sqlPremium, "i", [$idUsuario]);
				
                // LOGGING
                $logMsg = date('Y-m-d H:i:s') . " - User: " . $_POST['email'] . " | ID: " . $idUsuario . " | Total Premium 0: " . print_r($resPremium, true) . "\n";
                file_put_contents(__DIR__ . '/debug_login.log', $logMsg, FILE_APPEND);

				if (!empty($resPremium) && $resPremium[0]['total'] > 0) {
					$es_premium = true;
				}
			}
			
			 echo json_encode(['status' => 'success', 'message' => 'bien', 'es_admin' => $es_admin, 'es_premium' => $es_premium]);
		}
		else{
			 echo json_encode(['status' => 'error', 'message' => 'nocuenta']);
		}
	}
}
?>