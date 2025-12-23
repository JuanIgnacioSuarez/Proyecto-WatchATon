$(document).ready(function () {
    if (typeof idUsuarioPublico !== 'undefined') {
        $.post('../../backend/php/cargarVideosPublicos.php', { id_usuario: idUsuarioPublico }, function (response) {
            $('#lista-videos-publicos').html(response);
        }).fail(function () {
            $('#lista-videos-publicos').html('<div class="col-12 text-center text-danger">Error al cargar los videos.</div>');
        });
    } else {
        console.error("idUsuarioPublico is not defined.");
    }
});
