// Variables globales
let reproductoranuncio;
let idVideo;
let reproductor;

// Variables para los comentarios
let currentCommentIdToDelete = null;
let currentCommentIdToEdit = null;

// Variables para metricas
let inicioVisualizacionAnuncio = null;
let finVisualizacionAnuncio = null;
let navegador = navigator.userAgent;
let idAnuncioActual = null;

let anuncioClick = 0;
let anuncioEstado = "desconocido";
let anuncioPorcentaje = 0;

// Variables temporizadores
let segundos = 5;
let timer;

// Instancias Modales
let deleteModalInstance;
let editModalInstance;
let sanctionModalInstance;

// Funciones Helper Globales
function detectDevice() {
  const ua = navigator.userAgent;
  if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
    return "Tablet";
  }
  else if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
    return "Movil";
  }
  return "Escritorio";
}
let dispositivoTipo = detectDevice();

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
}

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

  setTimeout(() => {
    $toast.css('animation', 'fadeOutRight 0.4s ease-in forwards');
    setTimeout(() => $toast.remove(), 400);
  }, 4000);
}

// Inicialización Principal
$(document).ready(function () {
  const params = new URLSearchParams(window.location.search);
  idVideo = params.get("id_video");
  idVideo = params.get("id_video");
  // const esAdmin = getCookie('es_admin') === 'true'; // Eliminado para usar la variable global inyectada

  // Inicializar Modales Bootstrap
  if (document.getElementById('deleteCommentModal')) {
    deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteCommentModal'));
  }
  if (document.getElementById('editCommentModal')) {
    editModalInstance = new bootstrap.Modal(document.getElementById('editCommentModal'));
  }
  if (document.getElementById('sanctionModal')) {
    sanctionModalInstance = new bootstrap.Modal(document.getElementById('sanctionModal'));
  }

  // Cargar Título y Descripción e Inyectar Botón Admin
  $('#TituloDescripcion').load('../../backend/php/cargarTyD.php', { idVideo: idVideo }, function () {
    if (esAdmin) {
      $('#TituloDescripcion').append(`
              <div class="mt-3 border-top border-secondary pt-3">
                  <button id="btnSanctionVideo" class="btn btn-outline-danger btn-sm">
                      <i class="bi bi-shield-exclamation me-2"></i>Administrar / Sancionar Video
                  </button>
              </div>
          `);

      $('#btnSanctionVideo').off('click').on('click', function () {
        openSanctionModal(idVideo, 'video');
      });
    }
  });

  // Funciones de Modales (Definidas en scope pero accesibles)
  window.showDeleteCommentModal = function (commentId) { // Expose to window if needed or keep local usage via delegation
    if (!deleteModalInstance) { console.error("Error: deleteModalInstance no inicializado"); return; }
    currentCommentIdToDelete = commentId;
    deleteModalInstance.show();
  };

  function closeDeleteCommentModal() {
    if (deleteModalInstance) deleteModalInstance.hide();
    currentCommentIdToDelete = null;
  }

  window.showEditCommentModal = function (commentId, currentContent) {
    if (!editModalInstance) { console.error("Error: editModalInstance no inicializado"); return; }
    currentCommentIdToEdit = commentId;
    document.getElementById('editCommentTextarea').value = currentContent;
    editModalInstance.show();
    $('#editCommentError').hide();
  };

  function closeEditCommentModal() {
    if (editModalInstance) editModalInstance.hide();
    currentCommentIdToEdit = null;
    document.getElementById('editCommentTextarea').value = '';
    $('#editCommentError').hide();
  }

  window.openSanctionModal = function (id, type) {
    if (!sanctionModalInstance) { console.error("Error: sanctionModalInstance no inicializado"); return; }
    $('#sanctionTargetId').val(id);
    $('#sanctionTargetType').val(type);
    $('#sanctionReason').val('');
    $('#sanctionDescription').val('');
    $('#applySanction').prop('checked', false);

    const title = type === 'video' ? 'Sancionar Video' : 'Sancionar Comentario';
    $('#sanctionModal .modal-title').text(title);

    sanctionModalInstance.show();
  };

  // Event Delegation para Comentarios
  const $commentsContainer = $('#Comentarios');

  loadComments();

  $commentsContainer.on('click', '.delete-comment', function (e) {
    e.preventDefault();
    console.log("Click en eliminar comentario");
    const commentId = $(this).data('id-comentario');
    showDeleteCommentModal(commentId);
  });

  $commentsContainer.on('click', '.edit-comment', function (e) {
    e.preventDefault();
    console.log("Click en editar comentario");
    const commentId = $(this).data('id-comentario');
    const content = decodeURIComponent($(this).data('content'));
    showEditCommentModal(commentId, content);
  });

  $commentsContainer.on('click', '.sanction-comment', function (e) {
    e.preventDefault();
    console.log("Click en sancionar comentario");
    const commentId = $(this).data('id-comentario');
    openSanctionModal(commentId, 'comment');
  });

  // Cargar Comentarios
  function loadComments() {
    $.post('../../backend/php/cargarComentarios.php', { idVideo: idVideo }, function (data) {
      $commentsContainer.empty();
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
                                <li><a class="dropdown-item edit-comment" href="#" data-id-comentario="${comment.id_comentario}" data-content="${encodeURIComponent(comment.contenido_raw)}"><i class="bi bi-pencil me-2"></i>Editar</a></li>
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
          $commentsContainer.append(commentHtml);
        });
      } else {
        $commentsContainer.append(`
        <div class="text-center py-5 text-white-50">
            <i class="bi bi-chat-square-text display-4 mb-3 d-block opacity-50"></i>
            <p>Aún no hay comentarios.</p>
            <small>¡Sé el primero en opinar!</small>
        </div>
      `);
      }
    }, 'json');
  }

  // --- Manejo de Eventos de Modales ---

  // Borrar Comentario
  $('#confirmDeleteComment').on('click', function () {
    $.post('../../backend/php/EliminarComentario.php', { id_comentario: currentCommentIdToDelete }, function (response) {
      if (response.success) {
        showToast(response.message, 'success');
        loadComments();
      } else {
        showToast(response.message, 'error');
      }
      closeDeleteCommentModal();
    }, 'json');
  });

  $('#cancelDeleteComment').on('click', function () { closeDeleteCommentModal(); });

  // Editar Comentario
  $('#saveEditComment').on('click', function () {
    const nuevoComentario = $('#editCommentTextarea').val().trim();
    const editCommentError = $('#editCommentError');
    editCommentError.hide();

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
        loadComments();
      } else {
        showToast(response.message, 'error');
      }
      closeEditCommentModal();
    }, 'json');
  });

  $('#cancelEditComment').on('click', function () { closeEditCommentModal(); });
  $('#editCommentTextarea').on('input', function () { $('#editCommentError').hide(); });

  // Sancionar (Admin)
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
          loadComments();
        }
      } else {
        showToast(response.message || 'Error al procesar la solicitud', 'error');
      }
    }, 'json').fail(function () {
      showToast('Error de conexión con el servidor', 'error');
    });
  });


  // --- Reproductores y Lógica de Anuncios ---

  reproductoranuncio = cloudinary.videoPlayer('ReproductorAnuncio', {
    cloud_name: 'dqrxdpqef',
    controls: true,
    autoplay: true,
    muted: true,
    fluid: true,
    aspectRatio: '16:9'
  });

  reproductor = cloudinary.videoPlayer('ReproductorVideo', {
    cloud_name: 'dqrxdpqef',
    controls: true,
    autoplay: false,
    muted: false,
    fluid: true,
    aspectRatio: '16:9'
  });

  $.post('../../backend/php/RecuperarAnuncio.php', {}, function (data) {
    let anuncio = JSON.parse(data);
    idAnuncioActual = anuncio.ID_anuncio;
    reproductoranuncio.source(anuncio.public_id);
    reproductoranuncio.play();
    inicioVisualizacionAnuncio = new Date().toISOString().slice(0, 19).replace('T', ' ');
    startTimer();
  });

  function startTimer() {
    stopTimer();
    timer = setInterval(function () {
      segundos--;
      $('#TiempoSaltar').text(segundos);
      if (segundos < 0) {
        stopTimer();
        $('#SaltarAnuncio').prop('disabled', false).html('Saltar anuncio <i class="bi bi-arrow-right-circle-fill"></i> (No sumará puntos)');
      }
    }, 1000);
  }

  function stopTimer() {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
  }

  reproductoranuncio.on('pause', function () { stopTimer(); });
  reproductoranuncio.on('play', function () { if (segundos >= 0) startTimer(); });
  reproductoranuncio.on('ended', function () {
    stopTimer();
    anuncioEstado = "completado";
    anuncioPorcentaje = 100;
    grecaptcha.execute();
  });

  $('#ReproductorAnuncio').on('click', function () { anuncioClick = 1; });

  $('#SaltarAnuncio').click(function () {
    anuncioEstado = "saltado";
    reproducirVideoPrincipal();
  });

  $('#crearComentario').click(function () {
    let comentario = $('#nuevoComentario').val().trim(); // Added .trim() here
    $.post('../../backend/php/nuevoComentario.php', { comentario: comentario, idVideo: idVideo }, function (data) {
      data = data ? data.trim() : ''; // Limpiar espacios en blanco
      switch (data) {
        case "bien": showToast("Comentario cargado con exito!", 'success');
          $('#nuevoComentario').val('');
          loadComments();
          break;
        case "largo": showToast("Revise el largo del comentario", 'warning');
          break;
        case "noiniciado": showToast("Inicie sesion para poder comentar!", 'info');
          break;
        case "sancionado": showToast("Cuenta sancionada: No puedes comentar.", 'error');
          break;
        default: console.warn("Respuesta desconocida al crear comentario:", data);
      }
    });
  });

}); // End $(document).ready

// Funciones globales necesarias fuera del ready (si las hubiera, pero las moví la mayoría adentro o las expuse con window)

window.ExitoCaptcha = function (token) {
  $.post('../../backend/php/verificarCaptcha.php', { token: token }, function (data) {
    if (data == "tabien") {
      $.post('../../backend/php/sumarPuntos.php', {}, function (data) {
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

window.reproducirVideoPrincipal = function () {
  finVisualizacionAnuncio = new Date().toISOString().slice(0, 19).replace('T', ' ');

  if (reproductoranuncio) {
    const duration = reproductoranuncio.duration();
    const current = reproductoranuncio.currentTime();
    if (duration > 0) {
      anuncioPorcentaje = Math.round((current / duration) * 100);
    }
    if (anuncioPorcentaje > 100) anuncioPorcentaje = 100;
  }

  reproductoranuncio.dispose();
  $('#SaltarAnuncio').parent().hide();
  $('#ReproductorVideo').removeClass('d-none');

  $.post('../../backend/php/RecuperarPublic_id.php', { idVideo: idVideo }, function (data) {
    $.post('../../backend/php/guardarBitacora.php', {
      id_video: idVideo,
      id_anuncio: idAnuncioActual,
      navegador: navegador,
      inicio_visualizacion: inicioVisualizacionAnuncio,
      fin_visualizacion: finVisualizacionAnuncio,
      estado: anuncioEstado,
      click: anuncioClick,
      dispositivo: dispositivoTipo,
      porcentaje_visto: anuncioPorcentaje
    }, function (response) {
      console.log("Bitácora guardada:", response);
    }).fail(function (xhr, status, error) {
      console.error("Error al guardar bitácora:", error);
    });
    reproductor.source(data);
  });
}
