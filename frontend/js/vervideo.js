
let reproductoranuncio;
let idVideo;
let reproductor;

// Variables para los comentarios
let currentCommentIdToDelete = null;
let currentCommentIdToEdit = null;

let inicioVisualizacionAnuncio = null;
let finVisualizacionAnuncio = null;
let navegador = navigator.userAgent; //Conseguimos el navegador del cliente
let idAnuncioActual = null;

// Variables globales para el contador del anuncio
let segundos = 8; // Tiempo inicial para saltar el anuncio
let timer; // Variable para almacenar el ID del setInterval

function showToast(message, type = 'info') {
  const toastContainer = document.getElementById('toast-container');
  const toast = document.createElement('div');
  toast.className = `toast fade text-white bg-${type} border-0`;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'assertive');
  toast.setAttribute('aria-atomic', 'true');
  toast.innerHTML = `
    <div class="toast-header bg-${type} text-white">
      <strong class="me-auto">Notificación</strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      ${message}
    </div>
  `;
  toastContainer.append(toast);

  const bootstrapToast = new bootstrap.Toast(toast, {
    autohide: true,
    delay: 3000
  });
  bootstrapToast.show();

  toast.addEventListener('hidden.bs.toast', () => {
    toast.remove();
  });

}

window.ExitoCaptcha=function(token){  //El captcha nos devuelve un token  que usaremos para verificar actividad sospechosa
$.post('../../backend/php/verificarCaptcha.php',{token:token},function(data){ //verificamos y usamos el captcha para que google nos devuelva si esta bien o no
	if(data == "tabien"){
			$.post('../../backend/php/sumarPuntos.php',{},function(data){   //Esto nos devolvera la cantidad de puntos que se sumaron , si es 0 , sabemos que el usuario no esta iniciado 
			if(data>0){
				showToast("Sumaste "+data+" puntos!", 'success');
			}
			else{
					showToast("Inicia sesion para empezar a acumular puntos", 'info');
			}
			reproducirVideoPrincipal();
		});
		}
		else{
			showToast("Error con el captcha, no se sumaron puntos", 'danger');
			reproducirVideoPrincipal();
		}

});
}


window.reproducirVideoPrincipal=function(){  //Funcion para cargar el video principal 
	 finVisualizacionAnuncio = new Date().toISOString().slice(0, 19).replace('T', ' '); //Guardamos cuando termina el anuncio
	 reproductoranuncio.dispose();           //Destruimos el reproductor del anuncio
 	$('#SaltarAnuncio').hide();	            //Ocultamos el boton de saltarse el anuncio
 	$('#ReproductorVideo').removeClass('d-none'); //Le quitamos la clase que lo oculta

 $.post('../../backend/php/RecuperarPublic_id.php',{idVideo:idVideo},function(data){ 	
 	 $.post('../../backend/php/guardarBitacora.php', {id_video: idVideo, id_anuncio: idAnuncioActual,navegador: navegador,inicio_visualizacion: inicioVisualizacionAnuncio,fin_visualizacion: finVisualizacionAnuncio},function(response){
 	});
 	 reproductor.source(data);   //Cargamos el reproductos con el video apropiado
});
}


$(document).ready(function(){
    const params = new URLSearchParams(window.location.search); //Tomamos la id del video para poder usarla y cargarlo, la misma viene ya en la url
    idVideo = params.get("id_video");

 		$('#TituloDescripcion').load('../../backend/php/cargarTyD.php',{idVideo:idVideo});
 		loadComments(); // Cargar comentarios usando la nueva función

    reproductoranuncio = cloudinary.videoPlayer('ReproductorAnuncio',{
   	cloud_name: 'dqrxdpqef',
 	 	controls: true,   // Ahora mostramos los controles para que el usuario pueda gestionar el volumen
  	autoplay: true,
 		muted: true,
 		width: 720,
 	 	height: 400
    });

    reproductor = cloudinary.videoPlayer('ReproductorVideo', {   //Creamos el reproductor del video de verdad(El mismo estara oculto al principio)
 	 cloud_name: 'dqrxdpqef',
 	 controls: true,
  	autoplay: false,
 	 muted: false,
 	 width: 720,
 	 height: 400
 	});
				


$.post('../../backend/php/RecuperarAnuncio.php',{},function(data){ 	
 	  let anuncio = JSON.parse(data); //Recupero el anuncio
    idAnuncioActual = anuncio.ID_anuncio; 
    reproductoranuncio.source(anuncio.public_id);        //Cargamos un anuncio aleatorio de la lista de los posibles
 	  reproductoranuncio.play(); // Añadimos esta línea para asegurar la reproducción
 	 inicioVisualizacionAnuncio = new Date().toISOString().slice(0, 19).replace('T', ' ');  //Guardamos cuando arranca el anuncio a reproducirse            	
    startTimer(); // Iniciar el contador cuando el anuncio se carga
});

// Funciones para controlar el contador del botón de saltar anuncio
function startTimer() {
  stopTimer(); // Asegurarse de que no haya múltiples temporizadores corriendo
  timer = setInterval(function() {  // para manejar el tiempo para permitir saltar el anuncio
    segundos--;
    $('#TiempoSaltar').text(segundos);         //Para que se actualice el tiempo que falta para que el usuario pueda saltearse el anuncio

    if (segundos < 0) {
      stopTimer();
      $('#SaltarAnuncio').prop('disabled',false).html('Saltar anuncio <i class="bi bi-arrow-right-circle-fill"></i> (No sumará puntos)');  //Pasados los 5 segundos ,se le permitira al usuario al usuario saltearse el anuncio
    }
  }, 1000);
}

function stopTimer() {
  if (timer) {
    clearInterval(timer);
    timer = null;
  }
}

// Funciones para los modales de comentarios
function showDeleteCommentModal(commentId) {
  currentCommentIdToDelete = commentId;
  document.getElementById('deleteCommentModal').style.display = 'flex';
}

function closeDeleteCommentModal() {
  document.getElementById('deleteCommentModal').style.display = 'none';
  currentCommentIdToDelete = null;
}

function showEditCommentModal(commentId, currentContent) {
  currentCommentIdToEdit = commentId;
  document.getElementById('editCommentTextarea').value = currentContent;
  document.getElementById('editCommentModal').style.display = 'flex';
  $('#editCommentError').hide(); // Ocultar errores previos al abrir el modal
}

function closeEditCommentModal() {
  document.getElementById('editCommentModal').style.display = 'none';
  currentCommentIdToEdit = null;
  document.getElementById('editCommentTextarea').value = '';
  $('#editCommentError').hide(); // Ocultar errores al cerrar el modal
}

// Función para cargar y renderizar comentarios
function loadComments() {
  $.post('../../backend/php/cargarComentarios.php', { idVideo: idVideo }, function(data) {
    const commentsContainer = $('#Comentarios');
    commentsContainer.empty(); // Limpiar comentarios existentes

    if (data.length > 0) {
      commentsContainer.append('<h5 class="mb-3 text-secondary">Comentarios:</h5><div class="list-group"></div>');
      const listGroup = commentsContainer.find('.list-group');
      data.forEach(comment => {
        let commentHtml = '<div class="list-group-item d-flex justify-content-between align-items-center mb-2 shadow-sm rounded border-0">';
        commentHtml += '<div class="flex-grow-1">';
        commentHtml += '<h6 class="mb-1 text-primary">' + comment.correo + '</h6>';
        commentHtml += '<p class="mb-0 text-muted small">' + comment.contenido + '</p>';
        commentHtml += '</div>';

        if (comment.es_autor) {
          commentHtml += '<div class="comment-actions ms-3">';
          commentHtml += '<button class="btn btn-sm btn-outline-info me-2 edit-comment" data-id-comentario="' + comment.id_comentario + '" data-content="' + comment.contenido + '"><i class="bi bi-pencil-fill"></i> Modificar</button>';
          commentHtml += '<button class="btn btn-sm btn-outline-danger delete-comment" data-id-comentario="' + comment.id_comentario + '"><i class="bi bi-trash-fill"></i> Borrar</button>';
          commentHtml += '</div>';
        }
        commentHtml += '</div>';
        listGroup.append(commentHtml);
      });

      // Adjuntar eventos a los botones recién creados
      $('.delete-comment').off('click').on('click', function() {
        const commentId = $(this).data('id-comentario');
        showDeleteCommentModal(commentId);
      });

      $('.edit-comment').off('click').on('click', function() {
        const commentId = $(this).data('id-comentario');
        const content = $(this).data('content');
        showEditCommentModal(commentId, content);
      });

    } else {              //En caso de no tener ningun comentario
      commentsContainer.append('<div class="alert alert-info" role="alert">Este video aún no tiene comentarios. ¿Por qué no eres el primero?</div>');
    }
  }, 'json'); // Esperar una respuesta JSON
}

// Eventos del reproductor de anuncios para pausar/reanudar el contador
reproductoranuncio.on('pause', function() {
  stopTimer();
});

reproductoranuncio.on('play', function() {
  if (segundos >= 0) { // Solo reanudar si el contador no ha terminado
    startTimer();
  }
});

reproductoranuncio.on('ended',function(){ //Podemos comprobar si el anuncio finalizo por completo
  stopTimer(); // Detener el contador si el anuncio termina
  grecaptcha.execute();  //grecaptcha es parte de la api de reCaptcha , permite justamente que se active el captcha
});




$('#SaltarAnuncio').click(function(){
				reproducirVideoPrincipal();
});

$('#crearComentario').click(function(){  //Esto es para verificar el comentario y cargarlo , ademas de refrescar la lista de comentarios
let comentario=$('#nuevoComentario').val();
$.post('../../backend/php/nuevoComentario.php',{comentario:comentario,idVideo:idVideo},function(data){
	switch(data){
		

		case "bien":showToast("Comentario cargado con exito!", 'success');
								$('#nuevoComentario').val(''); // Limpiar el textarea
								loadComments(); // Refrescar comentarios
								break;
	
		case "largo":showToast("Revise el largo del comentario", 'warning');
									break;

		case "noiniciado":showToast("Inicie sesion para poder comentar!", 'info');
											break;
	}
});
});

// Manejo del modal de eliminación de comentario
$('#confirmDeleteComment').on('click', function() {
  $.post('../../backend/php/EliminarComentario.php', { id_comentario: currentCommentIdToDelete }, function(response) {
    if (response.success) {
      showToast(response.message, 'success');
      loadComments(); // Refrescar la lista de comentarios
    } else {
      showToast(response.message, 'danger');
    }
    closeDeleteCommentModal();
  }, 'json');
});

$('#cancelDeleteComment').on('click', function() {
  closeDeleteCommentModal();
});

// Manejo del modal de edición de comentario
$('#saveEditComment').on('click', function() {
  const nuevoComentario = $('#editCommentTextarea').val().trim();
  const editCommentError = $('#editCommentError');
  editCommentError.hide(); // Ocultar cualquier error anterior

  if (nuevoComentario === '') {
    editCommentError.text('Por favor, no deje vacío el comentario.').show();
    return;
  }
  if (nuevoComentario.length > 200) {
    editCommentError.text('El comentario supera el límite de 200 caracteres.').show();
    return;
  }

  $.post('../../backend/php/ModificarComentario.php', {
    id_comentario: currentCommentIdToEdit,
    nuevo_comentario: nuevoComentario
  }, function(response) {
    if (response.success) {
      showToast(response.message, 'success');
      loadComments(); // Refrescar la lista de comentarios
    } else {
      showToast(response.message, 'danger');
    }
    closeEditCommentModal();
  }, 'json');
});

$('#cancelEditComment').on('click', function() {
  closeEditCommentModal();
});

});

// Event listener para ocultar el mensaje de error al escribir en el textarea de edición
$('#editCommentTextarea').on('input', function() {
  $('#editCommentError').hide();
});
