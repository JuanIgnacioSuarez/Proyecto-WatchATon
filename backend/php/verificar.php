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
			 echo json_encode(['status' => 'success', 'message' => 'bien']);
		}
		else{
			 echo json_encode(['status' => 'error', 'message' => 'nocuenta']);
		}
	}
}
?>