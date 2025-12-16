$(document).ready(function () {
    // Función para mostrar la sección y actualizar el estado activo del menú
    function showSection(sectionId, buttonId) {
        // Oculta todas las secciones de contenido con fade out rápido y elimina la animación de entrada
        $('#mis-videos-content, #mi-info-content, #mi-cuenta-content, #mis-sanciones-content, #mis-canjes-content, #mi-suscripcion-content').addClass('d-none').removeClass('fade-in-up');

        // Muestra la sección deseada con animación de entrada
        $(sectionId).removeClass('d-none').addClass('fade-in-up');

        // Actualiza las clases activas del menú de navegación
        $('.profile-nav-link').removeClass('active');
        $(buttonId).addClass('active');
    }

    // Eventos de click para los botones del menú lateral
    $('#btn-mis-videos').on('click', function (e) {
        e.preventDefault();
        showSection('#mis-videos-content', '#btn-mis-videos');
    });

    $('#btn-mi-info').on('click', function (e) {
        e.preventDefault();
        showSection('#mi-info-content', '#btn-mi-info');
    });

    $('#btn-mi-cuenta').on('click', function (e) {
        e.preventDefault();
        showSection('#mi-cuenta-content', '#btn-mi-cuenta');
    });

    $('#btn-mis-sanciones').on('click', function (e) {
        e.preventDefault();
        showSection('#mis-sanciones-content', '#btn-mis-sanciones');
        loadSanctions();
    });

    $('#btn-mis-canjes').on('click', function (e) {
        e.preventDefault();
        showSection('#mis-canjes-content', '#btn-mis-canjes');
        loadMyRedemptions();
    });

    $('#btn-mi-suscripcion').on('click', function (e) {
        e.preventDefault();
        showSection('#mi-suscripcion-content', '#btn-mi-suscripcion');
    });

    // Cargar historial de canjes
    function loadMyRedemptions() {
        $.getJSON('../../backend/php/cargarMisCanjes.php', function (data) {
            const $tbody = $('#lista-canjes-body');
            const $noDataMsg = $('#no-canjes-msg');

            $tbody.empty();
            if (!data || data.length === 0 || data.error) {
                $noDataMsg.removeClass('d-none');
                return;
            }

            $noDataMsg.addClass('d-none');

            let html = '';
            data.forEach(item => {
                const isExternal = item.enlace && (item.enlace.startsWith('http') || item.enlace.startsWith('www'));
                const icon = isExternal ? '<i class="bi bi-globe me-2 text-info"></i>' : '<i class="bi bi-trophy-fill me-2 text-warning"></i>';

                let statusBadge = '';
                if (!item.fecha_vencimiento) {
                    statusBadge = '<span class="badge bg-success ms-2" style="font-size: 0.7em;">Activo</span>';
                } else if (item.activo == 1) {
                    statusBadge = '<span class="badge bg-success ms-2" style="font-size: 0.7em;">Activo</span>';
                } else {
                    statusBadge = '<span class="badge bg-danger ms-2" style="font-size: 0.7em;">Vencido</span>';
                }

                html += `
                    <tr>
                        <td class="text-white-50">${item.Fecha}</td>
                        <td class="text-white fw-bold">
                            ${icon} ${item.Descripcion} ${statusBadge}
                        </td>
                        <td><span class="badge bg-secondary bg-opacity-25 text-white border border-secondary">${item.Tipo}</span></td>
                        <td class="text-center text-white-50 fw-bold">${item.Valor} pts</td>
                    </tr>
                `;
            });
            $tbody.html(html);

        }).fail(function () {
            $('#lista-canjes-body').html('<tr><td colspan="4" class="text-center text-danger">Error al cargar historial.</td></tr>');
        });
    }

    // Cargar sanciones
    function loadSanctions() {
        $.getJSON('../../backend/php/cargarSanciones.php', function (data) {
            if (data.error) {
                $('#lista-sanciones').html('<div class="text-center text-danger">' + data.error + '</div>');
                return;
            }

            $('#total-sanciones-count').text(data.totalActive);

            // Actualizar color del contador según gravedad
            if (data.totalActive >= 3) {
                $('#total-sanciones-count').removeClass('text-white').addClass('text-danger');
            } else if (data.totalActive > 0) {
                $('#total-sanciones-count').removeClass('text-white').addClass('text-warning');
            }

            let html = '';
            if (data.sanciones.length === 0) {
                html = `
                    <div class="text-center py-5">
                        <i class="bi bi-shield-check display-1 text-success opacity-50"></i>
                        <h4 class="mt-3 text-white">¡Estás limpio!</h4>
                        <p class="text-white-50">No tienes ninguna sanción en tu historial. ¡Sigue así!</p>
                    </div>
                `;
            } else {
                data.sanciones.forEach(s => {
                    const isActive = s.tipo == 1;
                    const statusBadge = isActive
                        ? '<span class="badge bg-danger">Activa (Strike)</span>'
                        : '<span class="badge bg-secondary">Inactiva / Advertencia</span>';

                    const originalContent = s.contenido_original
                        ? `<div class="mt-2 p-2 bg-black bg-opacity-25 rounded border border-secondary border-opacity-25">
                             <small class="text-white-50 d-block mb-1">Contenido eliminado:</small>
                             <span class="text-white fst-italic">"${s.contenido_original}"</span>
                           </div>`
                        : '';

                    html += `
                        <div class="glass-panel p-3 rounded border ${isActive ? 'border-danger border-opacity-50' : 'border-secondary border-opacity-25'}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="fw-bold text-white mb-0"><span class="text-white-50 fw-normal">Motivo:</span> ${s.motivo}</h5>
                                ${statusBadge}
                            </div>
                            <p class="text-white-50 mb-2 small"><strong class="text-white">Notas:</strong> ${s.descripcion}</p>
                            ${originalContent}
                            <div class="text-end mt-2">
                                <small class="text-white-50"><i class="bi bi-calendar3 me-1"></i>${s.fecha}</small>
                            </div>
                        </div>
                    `;
                });
            }
            $('#lista-sanciones').html(html);
        }).fail(function () {
            $('#lista-sanciones').html('<div class="text-center text-danger">Error al cargar sanciones.</div>');
        });
    }

    // Cargar videos del usuario al iniciar
    loadUserVideos();

    function loadUserVideos() {
        $('#lista-mis-videos').load('../../backend/php/cargarMisVideos.php', function (response, status, xhr) {
            if (status == "error") {
                $('#lista-mis-videos').html('<div class="col-12 text-center text-danger"><p>Error al cargar los videos.</p></div>');
            }
        });
    }

    // Función para mostrar Toast
    function showToast(message, type = 'info') {
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-circle-fill',
            info: 'bi-info-circle-fill'
        };
        const icon = icons[type] || icons.info;
        const toastHtml = `
            <div class="custom-toast ${type} fade-in-up" style="background: rgba(0,0,0,0.8); backdrop-filter: blur(10px); border-left: 4px solid; padding: 15px; border-radius: 8px; color: white; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; min-width: 300px;">
                <i class="bi ${icon} fs-4"></i>
                <span>${message}</span>
            </div>
        `;
        const $toast = $(toastHtml);

        // Ajustar color del borde según tipo
        if (type === 'success') $toast.css('border-color', '#10b981');
        else if (type === 'error') $toast.css('border-color', '#ef4444');
        else if (type === 'warning') $toast.css('border-color', '#f59e0b');
        else $toast.css('border-color', '#3b82f6');

        $('#toast-container').append($toast);
        setTimeout(() => {
            $toast.fadeOut(500, function () { $(this).remove(); });
        }, 4000);
    }

    // Variables para el flujo de eliminación
    let deleteStep = 0;
    let videoToDeleteId = null;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteVideoModal'));

    // Delegación de eventos para el botón de borrar video
    $(document).on('click', '.btn-borrar-video', function (e) {
        e.preventDefault();
        e.stopPropagation();

        videoToDeleteId = $(this).data('id');
        deleteStep = 1;
        updateModalContent(deleteStep);
        deleteModal.show();
    });

    // Función para actualizar el contenido del modal según el paso
    function updateModalContent(step) {
        const $icon = $('#deleteModalIcon');
        const $title = $('#deleteModalTitle');
        const $body = $('#deleteModalBody');
        const $btn = $('#btn-confirm-delete-step');

        $btn.prop('disabled', false).text('Continuar');
        $icon.removeClass().addClass('bi display-1 mb-3');

        switch (step) {
            case 1:
                $icon.addClass('bi-trash3 text-warning');
                $title.text('Confirmar Eliminación');
                $body.text('¿Estás seguro de que quieres eliminar este video? Esta acción no se puede deshacer.');
                break;
            case 2:
                $icon.addClass('bi-exclamation-octagon text-danger');
                $title.text('Advertencia Seria');
                $body.text('Esta acción eliminará permanentemente el video y todos sus datos asociados de nuestros servidores. No hay forma de recuperar esta información. ¿Deseas continuar?');
                break;
            case 3:
                $icon.addClass('bi-emoji-dizzy text-danger'); // Icono más "dramático"
                $title.text('¡Última Oportunidad!');
                $body.html('¿Estás <strong>100% seguro</strong>? Si confirmas, este video desaparecerá más rápido que tu sueldo a fin de mes. 💸<br><br>¿Confirmar la aniquilación total del video?');
                $btn.text('Sí, eliminarlo');
                break;
        }
    }

    // Manejar el clic en el botón de confirmar del modal
    $('#btn-confirm-delete-step').on('click', function () {
        if (deleteStep < 3) {
            deleteStep++;
            // Pequeña animación de transición
            $('.modal-body').fadeOut(200, function () {
                updateModalContent(deleteStep);
                $(this).fadeIn(200);
            });
        } else {
            // Paso final: Ejecutar eliminación
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...');

            $.post('../../backend/php/borrarVideoUsuario.php', { id_video: videoToDeleteId }, function (response) {
                deleteModal.hide();
                if (response.success) {
                    loadUserVideos();
                    showToast("Video eliminado correctamente. ¡Hasta la vista, baby!", 'success');
                } else {
                    showToast("Error al eliminar el video: " + response.message, 'error');
                }
            }, 'json').fail(function () {
                deleteModal.hide();
                showToast("Error de conexión al intentar eliminar el video.", 'error');
            });
        }
    });

    // Widget para subir nueva portada en edición
    var myWidgetEditPortada = cloudinary.createUploadWidget({
        cloudName: 'dqrxdpqef',
        uploadPreset: 'mi_preset',
        folder: 'portadas_de_videos', // Carpeta específica
        sources: ['local', 'url', 'camera'],
        resourceType: 'image',
        clientAllowedFormats: ['png', 'jpg', 'jpeg', 'webp'],
        maxFileSize: 5000000, // 5MB
        cropping: true,
        croppingAspectRatio: 1.6, // Aspect ratio 16:10 aprox
        showSkipCropButton: false,
    }, (error, result) => {
        if (!error && result && result.event === "success") {
            console.log('Nueva portada subida: ', result.info);
            $('#edit-public-id-portada').val(result.info.public_id);
            $('#edit-preview-portada').attr('src', result.info.secure_url);
            showToast('Portada actualizada. Recuerda guardar los cambios.', 'info');
        }
    });

    $('#btn-cambiar-portada-edit').on('click', function () {
        myWidgetEditPortada.open();
    });

    // Manejo del modal de edición
    $(document).on('click', '.btn-editar-video', function () {
        const idVideo = $(this).data('id');
        const titulo = $(this).data('titulo');
        const descripcion = $(this).data('descripcion');
        const publicIdPortada = $(this).data('portada');
        const publicIdVideo = $(this).data('video-public-id');

        $('#edit-video-id').val(idVideo);
        $('#edit-titulo').val(titulo);
        $('#edit-descripcion').val(descripcion);
        $('#edit-public-id-portada').val(publicIdPortada);

        // Mostrar preview de portada actual
        if (publicIdPortada) {
            $('#edit-preview-portada').attr('src', `https://res.cloudinary.com/dqrxdpqef/image/upload/c_fill,h_60,w_100/${publicIdPortada}`);
        } else {
            // Fallback al frame del video
            $('#edit-preview-portada').attr('src', `https://res.cloudinary.com/dqrxdpqef/video/upload/so_1/${publicIdVideo}.jpg`);
        }

        const editModal = new bootstrap.Modal(document.getElementById('editVideoModal'));
        editModal.show();
    });

    $('#btn-guardar-edicion').on('click', function () {
        const idVideo = $('#edit-video-id').val();
        const titulo = $('#edit-titulo').val().trim();
        const descripcion = $('#edit-descripcion').val().trim();
        const publicIdPortada = $('#edit-public-id-portada').val();

        if (titulo === '' || descripcion === '') {
            showToast('Por favor, completa todos los campos.', 'warning');
            return;
        }

        $.post('../../backend/php/editarVideo.php', {
            id_video: idVideo,
            titulo: titulo,
            descripcion: descripcion,
            public_id_portada: publicIdPortada
        }, function (response) {
            if (response.success) {
                showToast(response.message, 'success');
                $('#editVideoModal').modal('hide'); // Cerrar modal usando jQuery
                $('.modal-backdrop').remove(); // Eliminar backdrop residual si queda
                loadUserVideos(); // Recargar la lista
            } else {
                showToast(response.message, 'error');
            }
        }, 'json')
            .fail(function () {
                showToast('Error de conexión al guardar cambios.', 'error');
            });
    });

    // --- GESTIÓN DE PERFIL (FOTO Y USUARIO) ---

    // Widget de Cloudinary para Foto de Perfil
    var WidgetPerfil = cloudinary.createUploadWidget({
        cloudName: 'dqrxdpqef',
        uploadPreset: 'mi_preset',
        resourceType: 'image',
        clientAllowedFormats: ['jpg', 'jpeg', 'png', 'webp'],
        multiple: false,
        folder: 'fotos_de_perfil',
        cropping: true, // Habilitar recorte para fotos de perfil
        croppingAspectRatio: 1, // Forzar cuadrado
        showSkipCropButton: false
    }, (error, result) => {
        if (!error && result && result.event === "success") {
            const publicId = result.info.public_id;
            const secureUrl = result.info.secure_url;

            // Actualizar en backend
            $.post('../../backend/php/actualizarPerfil.php', {
                action: 'update_photo',
                public_id: publicId
            }, function (response) {
                if (response.success) {
                    showToast("Foto de perfil actualizada.", 'success');
                    // Actualizar todas las imágenes de perfil en la página
                    $('.profile-pic-display').attr('src', secureUrl);
                } else {
                    showToast("Error al guardar la foto: " + response.message, 'error');
                }
            }, 'json').fail(function () {
                showToast("Error de conexión al guardar la foto.", 'error');
            });
        }
    });

    $('#btn-cambiar-foto').on('click', function () {
        WidgetPerfil.open();
    });

    // Actualizar Información de Usuario (Nombre)
    $('#form-mi-info').on('submit', function (e) {
        e.preventDefault();
        const newUsername = $('#input-username').val().trim();
        const newBiography = $('#input-biography').val().trim();

        if (newUsername === "") {
            showToast("El nombre de usuario no puede estar vacío.", 'warning');
            return;
        }

        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

        $.post('../../backend/php/actualizarPerfil.php', {
            action: 'update_info',
            nombre_usuario: newUsername,
            biografia: newBiography
        }, function (response) {
            $btn.prop('disabled', false).text('Guardar Cambios');
            if (response.success) {
                showToast("Información actualizada correctamente.", 'success');
                $('.username-display').text(newUsername); // Actualizar nombre en la UI
            } else {
                showToast("Error al actualizar: " + response.message, 'error');
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false).text('Guardar Cambios');
            showToast("Error de conexión al intentar actualizar.", 'error');
        });
    });

    // --- CAMBIO DE CONTRASEÑA (FIREBASE) ---
    $('#form-cambiar-password').on('submit', function (e) {
        e.preventDefault();
        // console.log("Formulario de cambio de contraseña enviado.");

        const currentPassword = $('#current-password').val();
        const newPassword = $('#new-password').val();
        const confirmPassword = $('#confirm-password').val();

        // Helper para mostrar error visual
        const showError = (selector) => {
            const $el = $(selector);
            $el.addClass('shake-error');
            $el.focus();
            // Quitar la clase al terminar la animación para poder repetirla
            setTimeout(() => {
                $el.removeClass('shake-error');
            }, 500);
        };

        if (currentPassword === "") {
            showToast("Por favor, ingresa tu contraseña actual.", 'warning');
            showError('#current-password');
            return;
        }
        if (newPassword === "") {
            showToast("Por favor, ingresa la nueva contraseña.", 'warning');
            showError('#new-password');
            return;
        }
        if (confirmPassword === "") {
            showToast("Por favor, confirma la nueva contraseña.", 'warning');
            showError('#confirm-password');
            return;
        }

        if (newPassword !== confirmPassword) {
            showToast("Las nuevas contraseñas no coinciden.", 'warning');
            showError('#new-password');
            showError('#confirm-password');
            return;
        }

        if (newPassword.length < 6) {
            showToast("La nueva contraseña debe tener al menos 6 caracteres.", 'warning');
            showError('#new-password');
            return;
        }

        const $btn = $('#btn-update-password');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Procesando...');

        // Función para procesar la actualización
        const processPasswordUpdate = (user) => {
            if (!user.email) {
                showToast("Error: No se pudo obtener el email del usuario.", 'error');
                $btn.prop('disabled', false).text('Actualizar Contraseña');
                return;
            }

            // console.log("Intentando re-autenticar a:", user.email);
            const credential = firebase.auth.EmailAuthProvider.credential(user.email, currentPassword);

            user.reauthenticateWithCredential(credential).then(() => {
                // console.log("Re-autenticación exitosa. Actualizando contraseña...");
                user.updatePassword(newPassword).then(() => {
                    showToast("Contraseña actualizada correctamente.", 'success');
                    $('#form-cambiar-password')[0].reset();
                    $btn.prop('disabled', false).text('Actualizar Contraseña');
                }).catch((error) => {
                    // console.error("Error al actualizar contraseña:", error);
                    showToast("Error al actualizar: " + error.message, 'error');
                    $btn.prop('disabled', false).text('Actualizar Contraseña');
                });
            }).catch((error) => {
                // console.error("Error en re-autenticación:", error);
                if (error.code === 'auth/wrong-password' ||
                    (error.code === 'auth/internal-error' && (error.message.includes('INVALID_LOGIN_CREDENTIALS') || error.message.includes('INVALID_PASSWORD')))) {
                    showToast("La contraseña actual es incorrecta.", 'error');
                    showError('#current-password');
                } else {
                    // Si es un error interno feo, mostramos algo más genérico si no podemos identificarlo
                    if (error.code === 'auth/internal-error') {
                        showToast("Error de autenticación. Verifica tu contraseña actual.", 'error');
                        showError('#current-password');
                    } else {
                        showToast("Error: " + error.message, 'error');
                    }
                }
                $btn.prop('disabled', false).text('Actualizar Contraseña');
            });
        };

        // Verificar estado de autenticación
        const user = firebase.auth().currentUser;
        if (user) {
            processPasswordUpdate(user);
        } else {
            // Si currentUser es null, esperamos un momento por si se está inicializando
            const unsubscribe = firebase.auth().onAuthStateChanged((user) => {
                unsubscribe(); // Desuscribirse inmediatamente
                if (user) {
                    processPasswordUpdate(user);
                } else {
                    showToast("No se pudo verificar la sesión. Recarga la página.", 'error');
                    $btn.prop('disabled', false).text('Actualizar Contraseña');
                }
            });
        }
    });

    // --- ELIMINACIÓN DE CUENTA (4 PASOS) ---
    let deleteAccountStep = 0;
    const deleteAccountModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));

    $('#btn-eliminar-cuenta').on('click', function () {
        deleteAccountStep = 1;
        updateDeleteAccountModal(deleteAccountStep);
        deleteAccountModal.show();
    });

    function updateDeleteAccountModal(step) {
        const $icon = $('#deleteAccountIcon');
        const $title = $('#deleteAccountTitle');
        const $body = $('#deleteAccountBody');
        const $btn = $('#btn-confirm-delete-account');

        $btn.prop('disabled', false).text('Continuar');
        $icon.removeClass().addClass('bi display-1 mb-3');

        switch (step) {
            case 1:
                $icon.addClass('bi-trash3 text-warning');
                $title.text('¿Te vas? 🥺');
                $body.text('¿Estás seguro de que quieres eliminar tu cuenta? Perderás acceso a todos tus videos y beneficios.');
                break;
            case 2:
                $icon.addClass('bi-heartbreak text-danger');
                $title.text('Nos rompes el corazón 💔');
                $body.text('¡Espera! Piénsalo bien. ¿De verdad quieres dejarnos? Prometemos mejorar... ¡incluso nos bañaremos más seguido!');
                break;
            case 3:
                $icon.addClass('bi-radioactive text-danger');
                $title.text('¡Punto de No Retorno! ☢️');
                $body.html('Si continúas, tus datos se borrarán para siempre. Ni los hackers rusos podrán recuperarlos. ¿Estás 100% seguro?');
                break;
            case 4:
                $icon.addClass('bi-skull-crossbones text-dark'); // O un color muy oscuro
                $title.text('Autodestrucción Inminente ☠️');
                $body.html(`
                    <p>Última oportunidad. Para detonar tu cuenta, primero verifica tu identidad.</p>
                    <div class="input-group mt-3">
                        <input type="password" id="delete-password-confirm" class="form-control form-control-glass" placeholder="Tu contraseña actual">
                        <button class="btn btn-outline-light" type="button" id="btn-verify-delete">Verificar</button>
                    </div>
                    <div id="delete-verification-msg" class="form-text mt-2"></div>
                `);
                $btn.text('¡ADIÓS MUNDO CRUEL!');
                $btn.prop('disabled', true); // Desactivar botón principal al inicio del paso 4
                break;
        }
    }

    // Evento para el botón de verificar (delegado porque se crea dinámicamente)
    $(document).on('click', '#btn-verify-delete', function () {
        const password = $('#delete-password-confirm').val();
        const $verifyBtn = $(this);
        const $mainBtn = $('#btn-confirm-delete-account');
        const $msg = $('#delete-verification-msg');
        const $input = $('#delete-password-confirm');

        if (!password) {
            showToast("Ingresa tu contraseña.", 'warning');
            return;
        }

        $verifyBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        $input.prop('disabled', true);

        const user = firebase.auth().currentUser;

        if (!user) {
            showToast("¡Presiona de nuevo verificar!", 'warning');
            $verifyBtn.prop('disabled', false).text('Verificar');
            $input.prop('disabled', false);
            return;
        }

        const credential = firebase.auth.EmailAuthProvider.credential(user.email, password);

        user.reauthenticateWithCredential(credential).then(() => {
            // Éxito
            $verifyBtn.removeClass('btn-outline-light').addClass('btn-success').html('<i class="bi bi-check-lg"></i>');
            $msg.html('<span class="text-success"><i class="bi bi-check-circle me-1"></i>Identidad confirmada. Puedes proceder.</span>');
            $mainBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-danger shake-error'); // Activar y llamar la atención
            showToast("Identidad verificada. Cuidado con el botón rojo.", 'success');
        }).catch((error) => {
            // Error
            console.error("Error verify:", error);
            $verifyBtn.prop('disabled', false).text('Verificar');
            $input.prop('disabled', false).focus();
            $msg.html('<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Contraseña incorrecta.</span>');
            showToast("Contraseña incorrecta.", 'error');
            // Asegurar que el botón principal siga desactivado
            $mainBtn.prop('disabled', true);
        });
    });

    $('#btn-confirm-delete-account').on('click', function () {
        if (deleteAccountStep < 4) {
            deleteAccountStep++;
            $('.modal-body').fadeOut(200, function () {
                updateDeleteAccountModal(deleteAccountStep);
                $(this).fadeIn(200);
            });
        } else {
            // Paso final: Ejecutar eliminación (Ya verificado)
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Borrando existencia...');

            // 1. Borrar de la Base de Datos y Cloudinary (Backend)
            $.post('../../backend/php/eliminarCuenta.php', {}, function (response) {
                if (response.success) {
                    // 2. Borrar de Firebase (Ya estamos re-autenticados del paso de verificación)
                    const user = firebase.auth().currentUser;
                    if (user) {
                        user.delete().then(function () {
                            window.location.href = '../views/index.php';
                        }).catch(function (error) {
                            console.error("Error al borrar en Firebase:", error);
                            window.location.href = '../views/index.php';
                        });
                    } else {
                        window.location.href = '../views/index.php';
                    }
                } else {
                    showToast("Error al eliminar datos: " + response.message, 'error');
                    $btn.prop('disabled', false).text('¡ADIÓS MUNDO CRUEL!');
                }
            }, 'json').fail(function () {
                showToast("Error de conexión con el servidor.", 'error');
                $btn.prop('disabled', false).text('¡ADIÓS MUNDO CRUEL!');
            });
        }
    });

});
