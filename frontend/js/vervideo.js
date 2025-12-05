
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

// Instancias de Modales Bootstrap
let deleteModalInstance;
let editModalInstance;

function showToast(message, type = 'info') {
  const icons = {
    success: 'bi-check-circle-fill',
    error: 'bi-exclamation-triangle-fill',
    warning: 'bi-exclamation-circle-fill',
    info: 'bi-info-circle-fill'
  };

  const icon = icons[type] || icons.info;

  const toastHtml = `
        <div class="custom-toast ${type}">
            <i class="bi ${icon}"></i>
            <span>${message}</span>
        </div>
    `;

  const $toast = $(toastHtml);
  $('#toast-container').append($toast);

  // Eliminar después de 4 segundos
  setTimeout(() => {
    $toast.css('animation', 'fadeOutRight 0.4s ease-in forwards');
    setTimeout(() => $toast.remove(), 400);
  }, 4000);
}

window.ExitoCaptcha = function (token) {  //El captcha nos devuelve un token  que usaremos para verificar actividad sospechosa
  $.post('../../backend/php/verificarCaptcha.php', { token: token }, function (data) { //verificamos y usamos el captcha para que google nos devuelva si esta bien o no
    if (data == "tabien") {
      $.post('../../backend/php/sumarPuntos.php', {}, function (data) {   //Esto nos devolvera la cantidad de puntos que se sumaron , si es 0 , sabemos que el usuario no esta iniciado 
        if (data > 0) {
          showToast("Sumaste " + data + " puntos!", 'success');
        } else if (data == -1) {
          showToast("Cuenta sancionada: No puedes sumar puntos.", 'error');
        } else {
          showToast("Inicia sesion para empezar a acumular puntos", 'info');
        }
        reproducirVideoPrincipal();
      });
    }
    else {
      showToast("Error con el captcha, no se sumaron puntos", 'error');
      reproducirVideoPrincipal();
    }

  });
}


window.reproducirVideoPrincipal = function () {  //Funcion para cargar el video principal 
  finVisualizacionAnuncio = new Date().toISOString().slice(0, 19).replace('T', ' '); //Guardamos cuando termina el anuncio
  reproductoranuncio.dispose();           //Destruimos el reproductor del anuncio
  $('#SaltarAnuncio').parent().hide();	            //Ocultamos el contenedor del boton de saltar
  $('#ReproductorVideo').removeClass('d-none'); //Le quitamos la clase que lo oculta

  $.post('../../backend/php/RecuperarPublic_id.php', { idVideo: idVideo }, function (data) {
    $.post('../../backend/php/guardarBitacora.php', {
      id_video: idVideo,
      id_anuncio: idAnuncioActual,
      navegador: navegador,
      inicio_visualizacion: inicioVisualizacionAnuncio,
      fin_visualizacion: finVisualizacionAnuncio
    }, function (response) {
      console.log("Bitácora guardada:", response);
    }).fail(function (xhr, status, error) {
      console.error("Error al guardar bitácora:", error);
    });
    reproductor.source(data);   //Cargamos el reproductos con el video apropiado
  });
}


$(document).ready(function () {
  const params = new URLSearchParams(window.location.search); //Tomamos la id del video para poder usarla y cargarlo, la misma viene ya en la url
  idVideo = params.get("id_video");

  // Inicializar modales de Bootstrap
  deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteCommentModal'));
  editModalInstance = new bootstrap.Modal(document.getElementById('editCommentModal'));

  $('#TituloDescripcion').load('../../backend/php/cargarTyD.php', { idVideo: idVideo });
  loadComments(); // Cargar comentarios usando la nueva función

  reproductoranuncio = cloudinary.videoPlayer('ReproductorAnuncio', {
    cloud_name: 'dqrxdpqef',
    controls: true,   // Ahora mostramos los controles para que el usuario pueda gestionar el volumen
    autoplay: true,
    muted: true,
    fluid: true,
    aspectRatio: '16:9'
  });

  reproductor = cloudinary.videoPlayer('ReproductorVideo', {   //Creamos el reproductor del video de verdad(El mismo estara oculto al principio)
    cloud_name: 'dqrxdpqef',
    controls: true,
    autoplay: false,
    muted: false,
    fluid: true,
    aspectRatio: '16:9'
  });



  $.post('../../backend/php/RecuperarAnuncio.php', {}, function (data) {
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
    timer = setInterval(function () {  // para manejar el tiempo para permitir saltar el anuncio
      segundos--;
      $('#TiempoSaltar').text(segundos);         //Para que se actualice el tiempo que falta para que el usuario pueda saltearse el anuncio

      if (segundos < 0) {
        stopTimer();
        $('#SaltarAnuncio').prop('disabled', false).html('Saltar anuncio <i class="bi bi-arrow-right-circle-fill"></i> (No sumará puntos)');  //Pasados los 5 segundos ,se le permitira al usuario al usuario saltearse el anuncio
      }
    }, 1000);
  }

  function stopTimer() {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
  }

  // Funciones para los modales de comentarios (Bootstrap 5)
  function showDeleteCommentModal(commentId) {
    currentCommentIdToDelete = commentId;
    deleteModalInstance.show();
  }

  function closeDeleteCommentModal() {
    deleteModalInstance.hide();
    currentCommentIdToDelete = null;
  }

  function showEditCommentModal(commentId, currentContent) {
    currentCommentIdToEdit = commentId;
    document.getElementById('editCommentTextarea').value = currentContent;
    editModalInstance.show();
    $('#editCommentError').hide(); // Ocultar errores previos al abrir el modal
  }

  function closeEditCommentModal() {
    editModalInstance.hide();
    currentCommentIdToEdit = null;
    document.getElementById('editCommentTextarea').value = '';
    $('#editCommentError').hide(); // Ocultar errores al cerrar el modal
  }

  // Función para cargar y renderizar comentarios
  function loadComments() {
    $.post('../../backend/php/cargarComentarios.php', { idVideo: idVideo }, function (data) {
      const commentsContainer = $('#Comentarios');
      commentsContainer.empty(); // Limpiar comentarios existentes

      if (data.length > 0) {
        data.forEach(comment => {
          const showDropdown = comment.es_autor || esAdmin;
          let commentHtml = `
            <div class="d-flex gap-3 mb-3 p-2 rounded hover-bg-glass">
                <div class="flex-shrink-0">
                    <a href="perfilPublico.php?id=${comment.id_usuario}">
                        <img src="${comment.foto_perfil}" alt="Perfil" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid rgba(255,255,255,0.2);">
                    </a>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-0 text-white fw-bold" style="font-size: 0.9rem;">
                            <a href="perfilPublico.php?id=${comment.id_usuario}" class="text-white text-decoration-none hover-text-primary">
                                ${comment.correo}
                            </a>
                        </h6>
                        ${showDropdown ? `
                        <div class="dropdown">
                            <button class="btn btn-link text-white-50 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                ${comment.es_autor ? `
                                <li><a class="dropdown-item edit-comment" href="#" data-id-comentario="${comment.id_comentario}" data-content="${comment.contenido}"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                <li><a class="dropdown-item delete-comment text-danger" href="#" data-id-comentario="${comment.id_comentario}"><i class="bi bi-trash me-2"></i>Eliminar</a></li>
                                ` : ''}
                                ${esAdmin ? `
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sanction-comment text-warning" href="#" data-id-comentario="${comment.id_comentario}"><i class="bi bi-shield-exclamation me-2"></i>Sancionar</a></li>
                                ` : ''}
                            </ul>
                        </div>
                        ` : ''}
                    </div>
                    <p class="mb-0 text-white-50 small mt-1" style="word-break: break-word;">${comment.contenido}</p>
                </div>
            </div>
        `;
          commentsContainer.append(commentHtml);
        });

        // Adjuntar eventos a los botones recién creados (Delegación o directo)
        $('.delete-comment').off('click').on('click', function (e) {
          e.preventDefault();
          const commentId = $(this).data('id-comentario');
          showDeleteCommentModal(commentId);
        });

        $('.edit-comment').off('click').on('click', function (e) {
          e.preventDefault();
          const commentId = $(this).data('id-comentario');
          const content = $(this).data('content');
          showEditCommentModal(commentId, content);
        });

        $('.sanction-comment').off('click').on('click', function (e) {
          e.preventDefault();
          const commentId = $(this).data('id-comentario');
          openSanctionModal(commentId, 'comment');
        });

      } else {              //En caso de no tener ningun comentario
        commentsContainer.append(`
        <div class="text-center py-5 text-white-50">
            <i class="bi bi-chat-square-text display-4 mb-3 d-block opacity-50"></i>
            <p>Aún no hay comentarios.</p>
            <small>¡Sé el primero en opinar!</small>
        </div>
      `);
      }
    }, 'json'); // Esperar una respuesta JSON
  }

  // Eventos del reproductor de anuncios para pausar/reanudar el contador
  reproductoranuncio.on('pause', function () {
    stopTimer();
  });

  reproductoranuncio.on('play', function () {
    if (segundos >= 0) { // Solo reanudar si el contador no ha terminado
      startTimer();
    }
  });

  reproductoranuncio.on('ended', function () { //Podemos comprobar si el anuncio finalizo por completo
    stopTimer(); // Detener el contador si el anuncio termina
    grecaptcha.execute();  //grecaptcha es parte de la api de reCaptcha , permite justamente que se active el captcha
  });




  $('#SaltarAnuncio').click(function () {
    reproducirVideoPrincipal();
  });

  $('#crearComentario').click(function () {  //Esto es para verificar el comentario y cargarlo , ademas de refrescar la lista de comentarios
    let comentario = $('#nuevoComentario').val();
    $.post('../../backend/php/nuevoComentario.php', { comentario: comentario, idVideo: idVideo }, function (data) {
      switch (data) {


        case "bien": showToast("Comentario cargado con exito!", 'success');
          $('#nuevoComentario').val(''); // Limpiar el textarea
          loadComments(); // Refrescar comentarios
          break;

        case "largo": showToast("Revise el largo del comentario", 'warning');
          break;

        case "noiniciado": showToast("Inicie sesion para poder comentar!", 'info');
          break;

        case "sancionado": showToast("Cuenta sancionada: No puedes comentar.", 'error');
          break;
      }
    });
  });

  // Manejo del modal de eliminación de comentario
  $('#confirmDeleteComment').on('click', function () {
    $.post('../../backend/php/EliminarComentario.php', { id_comentario: currentCommentIdToDelete }, function (response) {
      if (response.success) {
        showToast(response.message, 'success');
        loadComments(); // Refrescar la lista de comentarios
      } else {
        showToast(response.message, 'error');
      }
      closeDeleteCommentModal();
    }, 'json');
  });

  $('#cancelDeleteComment').on('click', function () {
    closeDeleteCommentModal();
  });

  // Manejo del modal de edición de comentario
  $('#saveEditComment').on('click', function () {
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
    }, function (response) {
      if (response.success) {
        showToast(response.message, 'success');
        loadComments(); // Refrescar la lista de comentarios
      } else {
        showToast(response.message, 'error');
      }
      closeEditCommentModal();
    }, 'json');
  });

  $('#cancelEditComment').on('click', function () {
    closeEditCommentModal();
  });

});

// Event listener para ocultar el mensaje de error al escribir en el textarea de edición
$('#editCommentTextarea').on('input', function () {
  $('#editCommentError').hide();
});

// --- Lógica de Administración ---

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

const esAdmin = getCookie('es_admin') === 'true';
let sanctionModalInstance;

$(document).ready(function () {
  sanctionModalInstance = new bootstrap.Modal(document.getElementById('sanctionModal'));

  if (esAdmin) {
    // Inyectar botón de sancionar video
    // Esperamos un poco a que cargue la descripción o usamos un intervalo/evento
    // Como es .load(), podemos usar el callback del load si lo modificamos, pero aquí lo haremos con un observer o timeout simple
    setTimeout(() => {
      $('#TituloDescripcion').append(`
                <div class="mt-3 border-top border-secondary pt-3">
                    <button id="btnSanctionVideo" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-shield-exclamation me-2"></i>Administrar / Sancionar Video
                    </button>
                </div>
            `);

      $('#btnSanctionVideo').on('click', function () {
        openSanctionModal(idVideo, 'video');
      });
    }, 1000); // Esperar a que cargue el contenido dinámico
  }

  // Manejo del formulario de sanción
  $('#sanctionForm').on('submit', function (e) {
    e.preventDefault();

    const targetId = $('#sanctionTargetId').val();
    const targetType = $('#sanctionTargetType').val();
    const reason = $('#sanctionReason').val();
    const description = $('#sanctionDescription').val();
    const applySanction = $('#applySanction').is(':checked') ? 1 : 0;

    if (!reason) {
      showToast('Por favor seleccione un motivo', 'warning');
      return;
    }

    $.post('../../backend/php/admin_actions.php', {
      action: 'sanction',
      targetId: targetId,
      targetType: targetType,
      reason: reason,
      description: description,
      applySanction: applySanction
    }, function (response) {
      if (response.success) {
        showToast(response.message, 'success');
        sanctionModalInstance.hide();

        if (targetType === 'video') {
          setTimeout(() => window.location.href = 'index.php', 1500);
        } else {
          loadComments(); // Recargar comentarios
        }
      } else {
        showToast(response.message || 'Error al procesar la solicitud', 'error');
      }
    }, 'json').fail(function () {
      showToast('Error de conexión con el servidor', 'error');
    });
  });
});

function openSanctionModal(id, type) {
  $('#sanctionTargetId').val(id);
  $('#sanctionTargetType').val(type);
  $('#sanctionReason').val('');
  $('#sanctionDescription').val('');
  $('#applySanction').prop('checked', false);

  // Actualizar título o texto según tipo
  const title = type === 'video' ? 'Sancionar Video' : 'Sancionar Comentario';
  $('#sanctionModal .modal-title').text(title);

  sanctionModalInstance.show();
}
