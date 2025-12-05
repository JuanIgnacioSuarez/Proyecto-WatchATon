$(document).ready(function () {
    // Navegación
    $('.nav-link-admin').click(function (e) {
        const section = $(this).data('section');

        if (!section) return;

        e.preventDefault();
        $('.nav-link-admin').removeClass('active');
        $(this).addClass('active');

        $('.section-content').addClass('d-none');
        $(`#section-${section}`).removeClass('d-none');

        // Cargar datos según la sección
        if (section === 'dashboard') loadDashboardStats();
        if (section === 'anunciantes') loadAnunciantes();
        if (section === 'anuncios') loadAnuncios();
        if (section === 'admins') loadAdmins();
    });

    // Carga inicial
    loadDashboardStats();

    // Helper para validación con animación
    function validateField($input, message) {
        if (!$input.val() || $input.val().trim() === '') {
            $input.addClass('is-invalid shake-error');

            // Remover la clase de animación después de que termine (500ms) para permitir re-ejecución
            setTimeout(() => {
                $input.removeClass('shake-error');
            }, 500);

            // Solo mostrar toast si se proporciona mensaje
            if (message) showToast(message, 'warning');

            return false;
        }
        $input.removeClass('is-invalid');
        return true;
    }

    // --- Anunciantes ---
    function loadAnunciantes() {
        $.getJSON('../../backend/php/admin/gestionar_anunciantes.php?accion=list', function (data) {
            const $tbody = $('#tabla-anunciantes');
            $tbody.empty();
            if (!Array.isArray(data)) {
                console.error('Error loading anunciantes:', data);
                return;
            }
            data.forEach(a => {
                // Manejar sensibilidad a mayúsculas/minúsculas (id vs ID)
                const id = a.id || a.ID;
                $tbody.append(`
                    <tr>
                        <td class="text-white-50">${id}</td>
                        <td class="text-white fw-bold">${a.nombre}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-danger btn-delete-anunciante" data-id="${id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error("Error fetching anunciantes:", textStatus, errorThrown);
        });
    }

    $('#form-add-anunciante').submit(function (e) {
        e.preventDefault();

        const $nombre = $('#nombre-anunciante');

        // Validar
        if (!validateField($nombre, 'El nombre es obligatorio')) return;

        const nombre = $nombre.val();

        $.post('../../backend/php/admin/gestionar_anunciantes.php', { accion: 'add', nombre: nombre }, function (res) {
            if (res.success) {
                $('#addAnuncianteModal').modal('hide');
                $('#form-add-anunciante')[0].reset();
                showToast('Anunciante agregado', 'success');
                loadAnunciantes();
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json');
    });

    // Lógica para eliminar anunciante con modal
    let anuncianteIdToDelete = null;

    $(document).on('click', '.btn-delete-anunciante', function () {
        anuncianteIdToDelete = $(this).data('id');
        $('#deleteAnuncianteModal').modal('show');
    });

    $('#btn-confirm-delete-anunciante').click(function () {
        if (!anuncianteIdToDelete) return;

        const $btn = $(this);
        $btn.prop('disabled', true);

        $.post('../../backend/php/admin/gestionar_anunciantes.php', { accion: 'delete', id: anuncianteIdToDelete }, function (res) {
            $btn.prop('disabled', false);
            $('#deleteAnuncianteModal').modal('hide');

            if (res.success) {
                loadAnunciantes();
                showToast('Anunciante eliminado', 'success');
            } else {
                showToast(res.error || 'Error al eliminar', 'danger');
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false);
            showToast('Error de conexión', 'danger');
        });
    });

    // --- Anuncios ---
    let adVideoId = '';
    let adVideoUrl = '';
    let adSaved = false;

    function loadAnuncios(advertiserId = '') {
        let url = '../../backend/php/admin/gestionar_anuncios.php?accion=list';
        if (advertiserId) {
            url += `&id_anunciante=${advertiserId}`;
        }

        $.getJSON(url, function (data) {
            const $grid = $('#grid-anuncios');
            $grid.empty();

            if (!Array.isArray(data) || data.length === 0) {
                $grid.html('<div class="col-12 text-center text-white-50"><p>No se encontraron anuncios.</p></div>');
                return;
            }

            data.forEach(a => {
                // Generar URL de miniatura (Frame 0) o usar video tag si es necesario
                let thumbnailHtml = '';

                if (a.Url && (a.Url.includes('cloudinary') || a.Url.endsWith('.mp4'))) {
                    // Si es cloudinary, intentamos usar jpg. Si no, video tag
                    if (a.public_id) {
                        const thumb = `https://res.cloudinary.com/dqrxdpqef/video/upload/so_0/${a.public_id}.jpg`;
                        thumbnailHtml = `<img src="${thumb}" class="w-100 h-100 object-fit-cover" alt="Anuncio">`;
                    } else {
                        thumbnailHtml = `<video src="${a.Url}" class="w-100 h-100 object-fit-cover"></video>`;
                    }
                } else {
                    thumbnailHtml = `<div class="d-flex align-items-center justify-content-center h-100 bg-secondary bg-opacity-25"><i class="bi bi-play-circle fs-1 text-white-50"></i></div>`;
                }

                $grid.append(`
                    <div class="col-md-4 col-lg-3">
                        <div class="glass-panel p-3 rounded-4 h-100 position-relative">
                            <div class="ratio ratio-16x9 mb-3 rounded overflow-hidden bg-black">
                                ${thumbnailHtml}
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <i class="bi bi-play-circle-fill fs-1 text-white opacity-75"></i>
                                </div>
                            </div>
                            <h6 class="text-white fw-bold mb-1 text-truncate" title="${a.nombre || 'Sin Título'}">${a.nombre || 'Sin Título'}</h6>
                            <small class="text-white-50 d-block text-truncate mb-1" title="${a.nombre_anunciante || 'Sin Anunciante'}"><i class="bi bi-briefcase-fill me-1"></i>${a.nombre_anunciante || 'Sin Anunciante'}</small>
                            <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 btn-delete-anuncio" data-id="${a.ID_anuncio}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `);
            });
        });
    }

    function loadAnunciantesList() {
        $.getJSON('../../backend/php/admin/gestionar_anunciantes.php?accion=list', function (data) {
            const $selectModal = $('#select-anunciante');
            const $selectFilter = $('#filter-anunciante');

            $selectModal.html('<option value="">Seleccionar...</option>');
            // Filter: mantener la opcion "Todos"
            $selectFilter.find('option:not(:first)').remove();

            if (Array.isArray(data)) {
                data.forEach(a => {
                    const id = a.id || a.ID;
                    $selectModal.append(`<option value="${id}">${a.nombre}</option>`);
                    $selectFilter.append(`<option value="${id}">${a.nombre}</option>`);
                });
            }
        });
    }

    // Evento change filtro
    $('#filter-anunciante').change(function () {
        loadAnuncios($(this).val());
    });

    // Cargar listas iniciales
    loadAnunciantesList();

    // Widget de Cloudinary para Anuncios (Solo Video)
    let myWidget = cloudinary.createUploadWidget({
        cloudName: 'dqrxdpqef',
        uploadPreset: 'mi_preset', // Asegúrate de usar un preset válido
        folder: 'anuncios',
        sources: ['local', 'url'],
        resourceType: 'video',
        clientAllowedFormats: ['mp4'],
        maxFileSize: 50000000 // Límite opcional
    }, (error, result) => {
        if (!error && result && result.event === "success") {
            console.log('Video Uploaded: ', result.info);
            adVideoId = result.info.public_id;
            adVideoUrl = result.info.secure_url;

            // Cerrar el widget
            myWidget.close();

            // Mostrar vista previa
            $('#ad-video-preview').attr('src', adVideoUrl);
            $('#ad-video-preview-container').removeClass('d-none');

            // Deshabilitar botón y mostrar estado de éxito
            $('#btn-upload-ad-video').prop('disabled', true)
                .removeClass('btn-outline-light')
                .addClass('btn-success border-success')
                .html('<i class="bi bi-check-lg me-2"></i>Video Cargado');

            showToast('Video cargado correctamente', 'success');
        }
    });

    $('#btn-upload-ad-video').click(function () {
        if (!$(this).prop('disabled')) {
            myWidget.open();
        }
    });

    $('#form-add-anuncio').submit(function (e) {
        e.preventDefault();

        // Limpiar validaciones previas (visuales)
        $('.form-control, .form-select').removeClass('is-invalid');

        const $selectAnunciante = $('#select-anunciante');
        const $nombreAnuncio = $('#nombre-anuncio');
        const $btnUpload = $('#btn-upload-ad-video');

        let isValid = true;

        if (!validateField($selectAnunciante, 'Selecciona un anunciante')) isValid = false;
        if (!validateField($nombreAnuncio, 'Ingresa el nombre del anuncio')) isValid = false;

        if (!adVideoId || !adVideoUrl) {
            // Animación para el botón de subida
            $btnUpload.addClass('shake-error');

            setTimeout(() => {
                $btnUpload.removeClass('shake-error');
            }, 500);

            showToast('Debes subir un video primero', 'warning');
            isValid = false;
        }

        if (!isValid) return;

        $.post('../../backend/php/admin/gestionar_anuncios.php', {
            accion: 'add',
            id_anunciante: $selectAnunciante.val(),
            public_id: adVideoId,
            url: adVideoUrl,
            nombre: $nombreAnuncio.val()
        }, function (res) {
            if (res.success) {
                adSaved = true; // Marcar como guardado
                $('#addAnuncioModal').modal('hide');
                showToast('Anuncio subido', 'success');
                loadAnuncios();
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json');
    });

    // Resetear formulario y borrar video temporal al cerrar modal
    $('#addAnuncioModal').on('hidden.bs.modal', function () {
        // Si hay un video subido pero NO se guardó en la BD (usuario canceló o cerró), eliminarlo de Cloudinary
        if (adVideoId && !adSaved) {
            console.log('Eliminando video temporal de Cloudinary:', adVideoId);
            $.post('../../backend/php/EliminarVideo.php', { ID: adVideoId }, function (res) {
                if (res.success) {
                    console.log('Video temporal eliminado');
                } else {
                    console.error('Error eliminando video temporal:', res.message);
                }
            }, 'json');
        }

        // Resetear variables
        adVideoId = '';
        adVideoUrl = '';
        adSaved = false;

        // Resetear formulario y UI
        $('#form-add-anuncio')[0].reset();
        $('#ad-video-preview-container').addClass('d-none');
        $('#ad-video-preview').attr('src', '');

        // Rehabilitar botón de carga
        $('#btn-upload-ad-video').prop('disabled', false)
            .removeClass('btn-success border-success')
            .addClass('btn-outline-light')
            .html('<i class="bi bi-cloud-upload me-2"></i>Seleccionar Video (MP4)');

        $('.form-control, .form-select').removeClass('is-invalid');
    });

    // Lógica para eliminar anuncio con modal
    let anuncioIdToDelete = null;

    $(document).on('click', '.btn-delete-anuncio', function () {
        anuncioIdToDelete = $(this).data('id');
        $('#deleteAnuncioModal').modal('show');
    });

    $('#btn-confirm-delete-anuncio').click(function () {
        if (!anuncioIdToDelete) return;

        const $btn = $(this);
        $btn.prop('disabled', true);

        $.post('../../backend/php/admin/gestionar_anuncios.php', { accion: 'delete', id: anuncioIdToDelete }, function (res) {
            $btn.prop('disabled', false);
            $('#deleteAnuncioModal').modal('hide');

            if (res.success) {
                loadAnuncios();
                showToast('Anuncio eliminado', 'success');
            } else {
                showToast('Error al eliminar', 'danger');
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false);
            showToast('Error de conexión', 'danger');
        });
    });

    // --- Admins ---
    function loadAdmins() {
        $.getJSON('../../backend/php/admin/gestionar_admins.php?accion=list', function (data) {
            const $list = $('#lista-admins');
            $list.empty();
            if (!Array.isArray(data)) return;
            data.forEach(u => {
                $list.append(`
                    <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold">${u.nombre_usuario}</h6>
                            <small class="text-white-50">${u.Correo}</small>
                        </div>
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">Admin</span>
                    </li>
                `);
            });
        });
    }

    $('#form-add-admin').submit(function (e) {
        e.preventDefault();

        const $username = $('#admin-username');
        const $email = $('#admin-email');
        const $password = $('#admin-password');

        let isValid = true;

        if (!validateField($username, 'El nombre de usuario es obligatorio')) isValid = false;
        if (!validateField($email, 'El correo es obligatorio')) isValid = false;
        if (!validateField($password, 'La contraseña es obligatoria (min 6 caracteres)')) isValid = false;

        if ($password.val().length < 6) {
            $password.addClass('is-invalid shake-error');
            setTimeout(() => $password.removeClass('shake-error'), 500);
            showToast('La contraseña debe tener al menos 6 caracteres', 'warning');
            isValid = false;
        }

        if (!isValid) return;

        // Deshabilitar botón para evitar doble click
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).text('Creando...');

        const emailVal = $email.val();
        const passVal = $password.val();
        const usernameVal = $username.val();

        // 1. Crear usuario en Firebase
        firebase.auth().createUserWithEmailAndPassword(emailVal, passVal)
            .then((userCredential) => {
                const user = userCredential.user;
                showToast("Usuario creado en Firebase. Enviando verificación...", "info");

                // 2. Enviar correo de verificación
                user.sendEmailVerification()
                    .then(() => {
                        showToast(`Verificación enviada a ${emailVal}`, "success");

                        // 3. Guardar en MySQL (Backend)
                        const data = {
                            accion: 'create',
                            username: usernameVal,
                            email: emailVal
                            // No enviamos password al backend porque sólo guardamos referencia
                        };

                        $.post('../../backend/php/admin/gestionar_admins.php', data, function (res) {
                            $btn.prop('disabled', false).text('Crear Admin');

                            if (res.success) {
                                $('#addAdminModal').modal('hide');
                                $('#form-add-admin')[0].reset();
                                showToast('Admin registrado correctamente en BD', 'success');
                                loadAdmins();
                            } else {
                                showToast('Error al guardar en BD: ' + res.error, 'danger');
                            }
                        }, 'json').fail(function () {
                            $btn.prop('disabled', false).text('Crear Admin');
                            showToast('Error de conexión con el servidor', 'danger');
                        });
                    })
                    .catch((error) => {
                        $btn.prop('disabled', false).text('Crear Admin');
                        console.error("Error al enviar verificación:", error);
                        showToast("Usuario creado pero falló el envío de verificación.", "warning");
                    });
            })
            .catch((error) => {
                $btn.prop('disabled', false).text('Crear Admin');
                console.error("Error Firebase:", error);

                let msg = "Error al crear cuenta.";
                if (error.code === 'auth/email-already-in-use') {
                    msg = "El correo ya está registrado en Firebase.";
                } else if (error.code === 'auth/weak-password') {
                    msg = "La contraseña es muy débil.";
                } else if (error.code === 'auth/invalid-email') {
                    msg = "El correo no es válido.";
                }

                showToast(msg, 'danger');
            });
    });

    // --- Estadísticas del Dashboard ---
    function loadDashboardStats() {
        $.getJSON('../../backend/php/admin/get_stats.php', function (data) {
            $('#stat-users').text(data.users);
            $('#stat-videos').text(data.videos);
            $('#stat-ads').text(data.ads);
        }).fail(function () {
            console.error("Error loading stats");
        });
    }

    function showToast(message, type = 'success') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        const $toast = $(toastHtml);
        $('#toast-container').append($toast);
        const toast = new bootstrap.Toast($toast[0]);
        toast.show();
        $toast.on('hidden.bs.toast', function () {
            $(this).remove();
        });
    }
});
