$(document).ready(function () {
	$('#buscar').click(function () {
		let titulo = $('#titulo').val();
		$('#videos').load('../../backend/php/cargarVideos.php', { titulo: titulo }, function () {
			// Agregar animación a las tarjetas después de que carguen
			$('#videos .col-12').each(function (index) {
				$(this).addClass('video-fade-in');
				$(this).css('animation-delay', (index * 0.1) + 's');
			});
		});
	});
	$('#buscar').click();  //Esto es para cargar todos los videos cuando el usuario ingresa a la pagina
});
