$(document).ready(function(){
	$('#buscar').click(function(){
		let titulo=$('#titulo').val();
		$('#videos').load('../../backend/php/cargarVideos.php',{titulo:titulo});
	});
	$('#buscar').click();  //Esto es para cargar todos los videos cuando el usuario ingresa a la pagina//
});
