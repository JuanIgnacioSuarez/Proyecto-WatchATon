<?php
$llaveSecreta = '6LeSLEQrAAAAAOiY8FPfitDbvJCnrg3hPWmPfioA';
$token = $_POST['token'];
$url = 'https://www.google.com/recaptcha/api/siteverify';



$datos=['secret'=>$llaveSecreta,'response'=>$token];   //secret y response son necesarios para que la api de google los reciba bien , ya que asi los espera


$opciones = [
  'http' => [
    'method' => 'POST',
    'header' => 'Content-Type: application/x-www-form-urlencoded',
    'content' => http_build_query($datos)
  ]
];

$contexto = stream_context_create($opciones);
$resultado = file_get_contents($url, false, $contexto); //Le mandamos a google la peticion y nos devuelve si fue un exito o no
$resultadojson = json_decode($resultado);


if($resultadojson->success){ //Succes lo manda el propio google , para ver si salio bien o mal el captcha
	echo 'tabien';
}
else{
	echo 'tamal';
}
?>