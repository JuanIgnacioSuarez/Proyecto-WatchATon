var ID = "";
var videosubido = false;   //Esto lo usamos para saber si el video ya fue subido a cloudinary

// Variables para la portada
var portadaID = "";
var portadaUrl = "";
var portadasubida = false;

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

$(document).ready(function () {
  var Widget = cloudinary.createUploadWidget({   //Creamos el widget de cloudinary para la subida del video (Lo limitamos a MP4)
    cloudName: 'dqrxdpqef',
    uploadPreset: 'mi_preset',
    resourceType: 'video',
    clientAllowedFormats: ['mp4'],
    folder: 'videos',
    multiple: false
  }, (error, result) => {
    if (!error && result && result.event === "success") {     //Si se subio con exito el video
      showToast("¡Video subido con éxito! Ahora llena los demás campos y guarda los datos.", 'success');
      ID = result.info.public_id;
      videosubido = true;
      $("#SubirVideo").prop("disabled", true);    //No dejamos subir mas videos , desactivamos el boton

      // Mostrar el botón de borrar video
      $("#borrarVideoCargado").show();
      $("#borrarVideoCargado").prop("disabled", false); // Asegurar que el botón de borrar esté habilitado

      // Mostrar previsualización del video usando cld-video-player
      if (window.cloudinary && window.cloudinary.videoPlayer) {
        // Limpiar el contenedor antes de crear un nuevo elemento de video
        $("#videoPreviewContainer").empty();
        // Crear un nuevo elemento de video dinámicamente
        let newVideoElement = $('<video>', {
          id: 'videoPreview',
          controls: true,
          class: 'cld-video-player',
          playsinline: true,
          style: 'width: 100%; height: 100%;'
        });
        $("#videoPreviewContainer").append(newVideoElement);


        try {
          var player = cloudinary.videoPlayer('videoPreview', {
            cloud_name: 'dqrxdpqef',
            controls: true,
            autoplay: false,
            muted: true, // Mantenerlo silenciado para evitar bloqueos de navegador
            width: "100%", // Ocupa el 100% del contenedor
            height: "100%" // Ocupa el 100% del contenedor
          });
          player.source(ID); // Cargar el video por su public_id
          player.play(); // Añadir play explícito para la previsualización del video
          $("#videoPreviewContainer").show(); // Mostrar el contenedor del reproductor

        } catch (e) {
          console.error("Error al inicializar cld-video-player: ", e);
        }
      } else {
        // Fallback si cld-video-player no está disponible
        $("#videoPreviewContainer").show();
      }
    }
  });

  // Widget de Cloudinary para subir la portada del video
  var WidgetPortada = cloudinary.createUploadWidget({
    cloudName: 'dqrxdpqef',

    uploadPreset: 'mi_preset',
    resourceType: 'image',
    clientAllowedFormats: ['jpg', 'jpeg', 'png', 'webp'],
    folder: 'portadas_de_videos',
    multiple: false
  }, (error, result) => {
    console.log("Cloudinary Portada Widget Callback:", error, result); // Depuración
    if (!error && result && result.event === "success") {
      showToast("¡Portada subida con éxito! Ahora puedes previsualizarla.", 'success');
      portadaID = result.info.public_id;
      portadaUrl = result.info.secure_url;
      portadasubida = true;
      $("#SubirPortada").prop("disabled", true); // Desactivar botón de subir portada

      // Mostrar el botón de borrar portada
      $("#borrarPortadaCargada").show();
      $("#borrarPortadaCargada").prop("disabled", false); // Asegurar que el botón de borrar esté habilitado

      // Mostrar previsualización de la portada (solo el tag consolidado)
      $("#portadaPreview").attr("src", portadaUrl).show();
      // No ocultamos el video preview aquí si ya se cargó.
    }
  });

  $("#SubirVideo").on("click", function () {    //Esto solo abre el widget
    if (!videosubido) {
      Widget.open();
    }
  });

  $("#SubirPortada").on("click", function () {    // Esto abre el widget para la portada
    if (!portadasubida) {
      WidgetPortada.open();
    }
  });

  $("#guardar").on("click", function () {        //Cuando apretan guardar empezamos a verificar todo
    let titulo = $('#titulo').val();
    let descripcion = $('#descripcion').val();

    // Validación: asegurar que video y portada estén subidos
    if (!videosubido || !portadasubida || titulo === "" || descripcion === "") {
      showToast("Por favor, suba un video, una portada y llene todos los campos.", 'warning');
      return;
    }


    $.post('../../backend/php/CargarBD.php', { ID: ID, titulo: titulo, descripcion: descripcion, portadaID: portadaID }, function (data) {
      switch (data) {
        case "mal":                                             //Falto cargar o el video o los campos de titulo y descripcion
          showToast("Suba un video, una portada y llene los campos!", 'warning');
          break;
        case "largo":                                           //El largo del titulo o la descripcion esta mal
          showToast("Verifique el largo del título y la descripción!", 'warning');
          break;
        case "bien":                                            //Se pudo cargar el video con todas sus partes correctamente
          showToast("Video subido y datos guardados correctamente!", 'success');
          videosubido = false;
          ID = "";
          portadaID = "";
          portadaUrl = "";
          $("#SubirVideo").prop("disabled", false);             //Reseteamos todo y le permitimos cargar otro video si quisiera
          $("#SubirPortada").prop("disabled", false); // Habilitar botón de subir portada
          $("#portadaPreview").hide().attr("src", ""); // Ocultar y limpiar previsualización de la portada

          // Si el reproductor de video de Cloudinary existe, disponer de él
          if ($("#videoPreview").data('cld-vp')) {
            $("#videoPreview").data('cld-vp').dispose();
          }
          $("#videoPreviewContainer").hide().empty(); // Ocultar y vaciar el contenedor del reproductor
          $("#borrarVideoCargado").hide(); // Ocultar el botón de borrar video
          $("#borrarPortadaCargada").hide(); // Ocultar el botón de borrar portada
          window.location.href = '../views/SubirVideo.php';
          break;
        case "noiniciado":                                      //En este caso el usuario no estaba con su sesion iniciada , por lo que no puede subir videos
          showToast("Inicie sesión para poder subir un video", 'info');
          window.location.href = '../views/IniciarSesion.php';
          break;
        case "sancionado":
          showToast("Cuenta sancionada: No puedes subir videos.", 'error');
          break;
      }
    });
  });


  window.onbeforeunload = function () {    //Si el usuario abandona la pagina antes de terminar , el video y la portada se borraran de Cloudinary
    if (videosubido && ID) {
      // Usar navigator.sendBeacon para enviar la petición de forma fiable
      navigator.sendBeacon("../../backend/php/EliminarVideo.php", new URLSearchParams({ ID: ID }));
    }
    if (portadasubida && portadaID) {
      // Usar navigator.sendBeacon para enviar la petición de forma fiable
      navigator.sendBeacon("../../backend/php/EliminarImagen.php", new URLSearchParams({ ID: portadaID }));
    }
  };

  // Función para borrar el video cargado
  function borrarVideo() {
    if (videosubido && ID) {
      $("#spinnerVideo").show(); // Mostrar spinner
      $("#borrarVideoCargado").prop("disabled", true); // Deshabilitar botón

      $.post("../../backend/php/EliminarVideo.php", { ID: ID }, function (response) {
        $("#spinnerVideo").hide(); // Ocultar spinner
        if (response.success) {
          showToast("Video eliminado correctamente.", 'info');
          // Resetear variables y ocultar previsualización
          videosubido = false;
          ID = "";
          $("#SubirVideo").prop("disabled", false);
          if ($("#videoPreview").data('cld-vp')) {
            $("#videoPreview").data('cld-vp').dispose();
          }
          $("#videoPreviewContainer").hide().empty(); // Ocultar y vaciar el contenedor del reproductor
          $("#borrarVideoCargado").hide();
        } else {
          showToast("Error al eliminar el video: " + response.message, 'error');
          $("#borrarVideoCargado").prop("disabled", false); // Habilitar si hubo error
        }
      }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
        $("#spinnerVideo").hide(); // Ocultar spinner en caso de error AJAX
        $("#borrarVideoCargado").prop("disabled", false); // Habilitar si hubo error
        console.error("Error AJAX al eliminar video: ", textStatus, errorThrown, jqXHR.responseText);
        showToast("Error de red al intentar eliminar el video.", 'error');
      });
    }
  }

  // Función para borrar la portada cargada
  function borrarPortada() {
    if (portadasubida && portadaID) {
      $("#spinnerPortada").show(); // Mostrar spinner
      $("#borrarPortadaCargada").prop("disabled", true); // Deshabilitar botón

      $.post("../../backend/php/EliminarImagen.php", { ID: portadaID }, function (response) {
        $("#spinnerPortada").hide(); // Ocultar spinner
        if (response.success) {
          showToast("Portada eliminada correctamente.", 'info');
          // Resetear variables y ocultar previsualización
          portadasubida = false;
          portadaID = "";
          portadaUrl = "";
          $("#SubirPortada").prop("disabled", false);
          $("#portadaPreview").hide().attr("src", "");
          $("#borrarPortadaCargada").hide();
        } else {
          showToast("Error al eliminar la portada: " + response.message, 'error');
          $("#borrarPortadaCargada").prop("disabled", false); // Habilitar si hubo error
        }
      }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
        $("#spinnerPortada").hide(); // Ocultar spinner en caso de error AJAX
        $("#borrarPortadaCargada").prop("disabled", false); // Habilitar si hubo error
        console.error("Error AJAX al eliminar imagen: ", textStatus, errorThrown, jqXHR.responseText);
        showToast("Error de red al intentar eliminar la portada.", 'error');
      });
    }
  }

  // Manejar el clic del botón de borrar video
  $("#borrarVideoCargado").on("click", function () {
    borrarVideo();
  });

  // Manejar el clic del botón de borrar portada
  $("#borrarPortadaCargada").on("click", function () {
    borrarPortada();
  });
});
