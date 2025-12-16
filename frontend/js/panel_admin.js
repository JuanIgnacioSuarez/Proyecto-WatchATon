$(document).ready(function () {
    // --- Navegación ---
    $('.nav-link-admin').click(function (e) {
        const section = $(this).data('section');
        if (!section) return;

        e.preventDefault();
        $('.nav-link-admin').removeClass('active');
        $(this).addClass('active');

        // Update Navbar Sub-title
        const sectionName = $(this).text().trim();
        $('#nav-sub-title').text(' > ' + sectionName);

        $('.section-content').addClass('d-none');
        $(`#section-${section}`).removeClass('d-none');

        // Cargar datos según sección
        if (section === 'dashboard') loadDashboardStats();
        if (section === 'anunciantes') loadAnunciantes();
        if (section === 'anuncios') loadAnuncios();
        if (section === 'admins') loadAdmins();
        if (section === 'beneficios') {
            loadBeneficios();
            loadBeneficioTypes();
        }
        if (section === 'mensajes') loadMensajesGlobales();
    });

    // --- Inicialización ---
    initDashboard();

    // Helper de validación
    function validateField($input, message) {
        if (!$input.val() || $input.val().trim() === '') {
            $input.addClass('is-invalid shake-error');
            setTimeout(() => $input.removeClass('shake-error'), 500);
            if (message) showToast(message, 'warning');
            return false;
        }
        $input.removeClass('is-invalid');
        return true;
    }

    function showToast(message, type = 'success') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
        const $toast = $(toastHtml);
        $('#toast-container').append($toast);
        const toast = new bootstrap.Toast($toast[0]);
        toast.show();
        $toast.on('hidden.bs.toast', function () { $(this).remove(); });
    }

    // ==========================================
    // ANUNCIANTES
    // ==========================================
    function loadAnunciantes() {
        $.getJSON('../../backend/php/admin/gestionar_anunciantes.php?accion=list', function (data) {
            const $tbody = $('#tabla-anunciantes');
            $tbody.empty();
            if (Array.isArray(data)) {
                data.forEach(a => {
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
            }
        });
    }

    $('#form-add-anunciante').submit(function (e) {
        e.preventDefault();
        const $nombre = $('#nombre-anunciante');
        if (!validateField($nombre, 'El nombre es obligatorio')) return;

        $.post('../../backend/php/admin/gestionar_anunciantes.php', {
            accion: 'add',
            nombre: $nombre.val()
        }, function (res) {
            if (res.success) {
                $('#addAnuncianteModal').modal('hide');
                $('#form-add-anunciante')[0].reset();
                showToast('Anunciante agregado', 'success');
                loadAnunciantes();
                loadAnunciantesList(); // Actualizar filtros también
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json');
    });

    let anuncianteIdToDelete = null;
    $(document).on('click', '.btn-delete-anunciante', function () {
        anuncianteIdToDelete = $(this).data('id');
        $('#deleteAnuncianteModal').modal('show');
    });

    $('#btn-confirm-delete-anunciante').click(function () {
        if (!anuncianteIdToDelete) return;
        const $btn = $(this); $btn.prop('disabled', true);

        $.post('../../backend/php/admin/gestionar_anunciantes.php', {
            accion: 'delete',
            id: anuncianteIdToDelete
        }, function (res) {
            $btn.prop('disabled', false);
            $('#deleteAnuncianteModal').modal('hide');
            if (res.success) {
                loadAnunciantes();
                showToast('Anunciante eliminado', 'success');
            } else {
                showToast(res.error || 'Error al eliminar', 'danger');
            }
        }, 'json').fail(() => {
            $btn.prop('disabled', false);
            showToast('Error de conexión', 'danger');
        });
    });

    // ==========================================
    // ANUNCIOS
    // ==========================================
    let adVideoId = '';
    let adVideoUrl = '';
    let adSaved = false;

    function loadAnuncios(advertiserId = '') {
        let url = '../../backend/php/admin/gestionar_anuncios.php?accion=list';
        if (advertiserId) url += `&id_anunciante=${advertiserId}`;

        $.getJSON(url, function (data) {
            const $grid = $('#grid-anuncios');
            $grid.empty();

            if (!Array.isArray(data) || data.length === 0) {
                $grid.html('<div class="col-12 text-center text-white-50"><p>No se encontraron anuncios.</p></div>');
                return;
            }

            data.forEach(a => {
                let thumbnailHtml = '';
                if (a.Url && (a.Url.includes('cloudinary') || a.Url.endsWith('.mp4'))) {
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

            // Reset options
            $selectModal.html('<option value="">Seleccionar...</option>');
            $selectFilter.find('option:not(:first)').remove(); // Keep "Todos"

            if (Array.isArray(data)) {
                data.forEach(a => {
                    const id = a.id || a.ID;
                    $selectModal.append(`<option value="${id}">${a.nombre}</option>`);
                    $selectFilter.append(`<option value="${id}">${a.nombre}</option>`);
                });
            }
        });
    }

    // Filtro Anuncios
    $('#filter-anunciante').change(function () { loadAnuncios($(this).val()); });

    // Carga inicial listas
    loadAnunciantesList();

    // Widget Cloudinary
    let myWidget = cloudinary.createUploadWidget({
        cloudName: 'dqrxdpqef',
        uploadPreset: 'mi_preset',
        folder: 'anuncios',
        sources: ['local', 'url'],
        resourceType: 'video',
        clientAllowedFormats: ['mp4'],
        maxFileSize: 50000000
    }, (error, result) => {
        if (!error && result && result.event === "success") {
            adVideoId = result.info.public_id;
            adVideoUrl = result.info.secure_url;
            myWidget.close();

            $('#ad-video-preview').attr('src', adVideoUrl);
            $('#ad-video-preview-container').removeClass('d-none');
            $('#btn-upload-ad-video').prop('disabled', true)
                .removeClass('btn-outline-light').addClass('btn-success border-success')
                .html('<i class="bi bi-check-lg me-2"></i>Video Cargado');
            showToast('Video cargado correctamente', 'success');
        }
    });

    $('#btn-upload-ad-video').click(function () {
        if (!$(this).prop('disabled')) myWidget.open();
    });

    $('#form-add-anuncio').submit(function (e) {
        e.preventDefault();
        const $selectAnunciante = $('#select-anunciante');
        const $nombreAnuncio = $('#nombre-anuncio');
        const $btnUpload = $('#btn-upload-ad-video');

        // Limpiar errores visuales previos
        $('.form-control, .form-select').removeClass('is-invalid');

        let isValid = true;
        if (!validateField($selectAnunciante, 'Selecciona un anunciante')) isValid = false;
        if (!validateField($nombreAnuncio, 'Ingresa el nombre del anuncio')) isValid = false;

        if (!adVideoId || !adVideoUrl) {
            $btnUpload.addClass('shake-error');
            setTimeout(() => $btnUpload.removeClass('shake-error'), 500);
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
                adSaved = true;
                $('#addAnuncioModal').modal('hide');
                showToast('Anuncio subido', 'success');
                loadAnuncios();
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json');
    });

    $('#addAnuncioModal').on('hidden.bs.modal', function () {
        if (adVideoId && !adSaved) {
            $.post('../../backend/php/EliminarVideo.php', { ID: adVideoId });
        }
        adVideoId = ''; adVideoUrl = ''; adSaved = false;
        $('#form-add-anuncio')[0].reset();
        $('#ad-video-preview-container').addClass('d-none');
        $('#btn-upload-ad-video').prop('disabled', false)
            .removeClass('btn-success border-success').addClass('btn-outline-light')
            .html('<i class="bi bi-cloud-upload me-2"></i>Seleccionar Video (MP4)');
        $('.form-control, .form-select').removeClass('is-invalid');
    });

    let anuncioIdToDelete = null;
    $(document).on('click', '.btn-delete-anuncio', function () {
        anuncioIdToDelete = $(this).data('id');
        $('#deleteAnuncioModal').modal('show');
    });

    $('#btn-confirm-delete-anuncio').click(function () {
        if (!anuncioIdToDelete) return;
        const $btn = $(this); $btn.prop('disabled', true);

        $.post('../../backend/php/admin/gestionar_anuncios.php', {
            accion: 'delete',
            id: anuncioIdToDelete
        }, function (res) {
            $btn.prop('disabled', false);
            $('#deleteAnuncioModal').modal('hide');
            if (res.success) {
                loadAnuncios();
                showToast('Anuncio eliminado', 'success');
            } else {
                showToast('Error al eliminar', 'danger');
            }
        }, 'json').fail(() => {
            $btn.prop('disabled', false);
            showToast('Error de conexión', 'danger');
        });
    });

    // ==========================================
    // ADMINS
    // ==========================================
    function loadAdmins() {
        $.getJSON('../../backend/php/admin/gestionar_admins.php?accion=list', function (data) {
            const $list = $('#lista-admins');
            $list.empty();
            if (Array.isArray(data)) {
                data.forEach(user => {
                    $list.append(`
                         <li class="list-group-item bg-transparent text-white border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-badge-fill me-2 text-warning"></i>
                                <span>${user.nombre_usuario}</span>
                                <small class="text-white-50 ms-2">(${user.Correo})</small>
                            </div>
                        </li>
                    `);
                });
            }
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

        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).text('Creando...');

        const emailVal = $email.val();
        const passVal = $password.val();
        const usernameVal = $username.val();

        firebase.auth().createUserWithEmailAndPassword(emailVal, passVal)
            .then((res) => {
                showToast("Usuario creado en Firebase. Verificación enviada...", "info");
                res.user.sendEmailVerification().then(() => {
                    $.post('../../backend/php/admin/gestionar_admins.php', {
                        accion: 'create',
                        username: usernameVal,
                        email: emailVal
                    }, function (backendRes) {
                        $btn.prop('disabled', false).text('Crear Admin');
                        if (backendRes.success) {
                            $('#addAdminModal').modal('hide');
                            $('#form-add-admin')[0].reset();
                            showToast('Admin registrado correctamente', 'success');
                            loadAdmins();
                        } else {
                            showToast('Error backend: ' + backendRes.error, 'danger');
                        }
                    }, 'json');
                }).catch(err => {
                    $btn.prop('disabled', false).text('Crear Admin');
                    showToast('Fallo envío verificación', 'warning');
                });
            })
            .catch((err) => {
                $btn.prop('disabled', false).text('Crear Admin');
                let msg = err.message;
                if (err.code === 'auth/email-already-in-use') msg = "Correo ya registrado.";
                if (err.code === 'auth/weak-password') msg = "Contraseña débil.";
                showToast(msg, 'danger');
            });
    });

    function renderCompletion(data) {
        const ctx = document.getElementById('chart-completion').getContext('2d');
        let completed = 0, skipped = 0;
        data.forEach(d => {
            if (d.estado === 'completado') completed = parseInt(d.total);
            if (d.estado === 'saltado') skipped = parseInt(d.total);
        });
        const total = completed + skipped;
        const rate = total > 0 ? Math.round((completed / total) * 100) : 0;
        $('#completion-text').text(`Tasa Completado: ${rate}%`);

        if (chartCompletion) chartCompletion.destroy();
        chartCompletion = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completado', 'Saltado'],
                datasets: [{
                    data: [completed, skipped],
                    backgroundColor: ['#198754', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '70%',
                plugins: { legend: { position: 'bottom', labels: { color: '#fff' } } }
            }
        });
    }

    function renderTopAds(data) {
        const ctx = document.getElementById('chart-top-ads').getContext('2d');
        if (chartTopAds) chartTopAds.destroy();
        chartTopAds = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.nombre),
                datasets: [{
                    data: data.map(d => d.vistas),
                    backgroundColor: 'rgba(13, 202, 240, 0.7)',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#adb5bd' } },
                    y: { ticks: { color: '#fff' } }
                }
            }
        });
    }

    let chartTimeline = null;
    let chartDevices = null;
    let chartBrowsers = null;
    let chartCompletion = null;
    let chartTopAds = null;
    let chartTopAdvertisers = null;
    let chartTopRedemptions = null;
    let chartPremiumStats = null;

    function initDashboard() {
        const today = new Date();
        const past = new Date();
        past.setDate(today.getDate() - 30);

        $('#dash-date-end').val(today.toISOString().split('T')[0]);
        $('#dash-date-start').val(past.toISOString().split('T')[0]);

        loadDashboardAdvertisers();
        loadDashboardStats();
    }

    function loadDashboardAdvertisers() {
        $.getJSON('../../backend/php/admin/gestionar_anunciantes.php?accion=list', function (data) {
            const $select = $('#dash-filter-anunciante');
            $select.find('option:not(:first)').remove();
            if (Array.isArray(data)) {
                data.forEach(a => {
                    $select.append(`<option value="${a.id}">${a.nombre}</option>`);
                });
            }
        });
    }

    function loadDashboardStats() {
        const start = $('#dash-date-start').val();
        const end = $('#dash-date-end').val();
        const advertiserCtx = $('#dash-filter-anunciante').val();

        $('#btn-refresh-stats i').addClass('spin-anim');

        $.post('../../backend/php/admin/obtener_estadisticas_avanzadas.php', {
            fecha_inicio: start,
            fecha_fin: end,
            filtro_id: advertiserCtx // Renamed to avoid AdBlocker trigger
        }, function (res) {
            $('#btn-refresh-stats i').removeClass('spin-anim');
            if (res.success) {
                $('#stat-views-period').text(res.total_period);
                renderTimeline(res.timeline);
                renderDevices(res.devices);
                renderBrowsers(res.browsers);
                renderCompletion(res.retention);
                renderTopAds(res.top_ads);
                renderTopRedemptions(res.top_redemptions);
                renderTopAdvertisers(res.top_advertisers);
                renderTimeStats(res.time_advertiser, res.time_ad);
                if (res.premium_stats) renderPremiumStats(res.premium_stats);
            }
        }, 'json').fail((jqXHR, textStatus, errorThrown) => {
            $('#btn-refresh-stats i').removeClass('spin-anim');
            console.error("Error loading stats:", textStatus, errorThrown);
            // Detect AdBlocker (status 0 or error)
            if (jqXHR.status === 0 || textStatus === 'error') {
                showToast('Error de conexión. Si usas AdBlock, desactívalo.', 'danger');
            } else {
                showToast('Error al cargar estadísticas.', 'danger');
            }
        });

        $.getJSON('../../backend/php/admin/get_stats.php', function (data) {
            $('#stat-users').text(data.users);
            $('#stat-videos').text(data.videos);
            $('#stat-ads').text(data.ads);
        });
    }

    function renderTimeline(data) {
        const ctx = document.getElementById('chart-views-time').getContext('2d');
        if (chartTimeline) chartTimeline.destroy();
        chartTimeline = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => d.fecha),
                datasets: [{
                    label: 'Visualizaciones',
                    data: data.map(d => d.total),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true, tension: 0.4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#adb5bd' } },
                    y: { ticks: { color: '#adb5bd' }, beginAtZero: true }
                }
            }
        });
    }

    function renderDevices(data) {
        const ctx = document.getElementById('chart-devices').getContext('2d');
        if (chartDevices) chartDevices.destroy();
        chartDevices = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.length ? data.map(d => d.dispositivo) : ['Sin datos'],
                datasets: [{
                    data: data.length ? data.map(d => d.total) : [1],
                    backgroundColor: ['#0d6efd', '#20c997', '#ffc107'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { color: '#fff' } } }
            }
        });
    }

    function renderBrowsers(data) {
        const ctx = document.getElementById('chart-browsers').getContext('2d');
        if (chartBrowsers) chartBrowsers.destroy();
        chartBrowsers = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.length ? data.map(d => d.navegador) : ['Sin datos'],
                datasets: [{
                    data: data.length ? data.map(d => d.total) : [1],
                    backgroundColor: ['#fd7e14', '#6610f2', '#6f42c1', '#d63384', '#0dcaf0'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { color: '#fff' } } }
            }
        });
    }

    function renderTopAds(data) {
        const ctx = document.getElementById('chart-top-ads').getContext('2d');
        if (chartTopAds) chartTopAds.destroy();
        chartTopAds = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.nombre),
                datasets: [{
                    data: data.map(d => d.vistas),
                    backgroundColor: 'rgba(13, 202, 240, 0.7)',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#adb5bd' } },
                    y: { ticks: { color: '#fff' } }
                }
            }
        });
    }

    function renderTopRedemptions(data) {
        const ctx = document.getElementById('chart-top-redemptions').getContext('2d');
        if (chartTopRedemptions) chartTopRedemptions.destroy();
        chartTopRedemptions = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.Descripcion),
                datasets: [{
                    data: data.map(d => d.total),
                    backgroundColor: 'rgba(253, 126, 20, 0.7)', // Orange
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#adb5bd' }, beginAtZero: true },
                    y: { ticks: { color: '#fff' } }
                }
            }
        });
    }

    function renderTopAdvertisers(data) {
        const ctx = document.getElementById('chart-top-advertisers').getContext('2d');
        if (chartTopAdvertisers) chartTopAdvertisers.destroy();
        chartTopAdvertisers = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.nombre),
                datasets: [{
                    data: data.map(d => d.vistas),
                    backgroundColor: 'rgba(255, 193, 7, 0.7)', // Warning color
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#adb5bd' } },
                    y: { ticks: { color: '#fff' } }
                }
            }
        });
    }

    function renderTimeStats(advertiserData, adData) {
        // Por Anunciante
        const $tblAdv = $('#table-time-advertiser');
        $tblAdv.empty();
        if (Array.isArray(advertiserData) && advertiserData.length > 0) {
            advertiserData.forEach(d => {
                $tblAdv.append(`<tr><td class="text-white">${d.nombre}</td><td class="text-end text-white-50 font-monospace">${formatSeconds(d.segundos)}</td></tr>`);
            });
        } else {
            $tblAdv.append('<tr><td colspan="2" class="text-center text-white-50">Sin datos</td></tr>');
        }

        // Por Anuncio
        const $tblAd = $('#table-time-ad');
        $tblAd.empty();
        if (Array.isArray(adData) && adData.length > 0) {
            adData.forEach(d => {
                $tblAd.append(`<tr><td class="text-white">${d.nombre}</td><td class="text-end text-white-50 font-monospace">${formatSeconds(d.segundos)}</td></tr>`);
            });
        } else {
            $tblAd.append('<tr><td colspan="2" class="text-center text-white-50">Sin datos</td></tr>');
        }
    }

    function formatSeconds(seconds) {
        if (!seconds) return '0s';
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);

        let str = '';
        if (h > 0) str += `${h}h `;
        if (m > 0 || h > 0) str += `${m}m `;
        str += `${s}s`;
        return str;
    }

    function renderPremiumStats(data) {
        const ctx = document.getElementById('chart-premium-stats').getContext('2d');
        if (chartPremiumStats) chartPremiumStats.destroy();

        const pagados = parseInt(data.pagados || 0);
        const puntos = parseInt(data.puntos || 0);
        const total = pagados + puntos;

        // Si no hay datos, mostrar 'Sin datos' visualmente o chart vacio
        const labels = total > 0 ? ['Pagado', 'Puntos'] : ['Sin datos'];
        const dataset = total > 0 ? [pagados, puntos] : [1];
        const colors = total > 0 ? ['#0d6efd', '#ffc107'] : ['#495057'];

        chartPremiumStats = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataset,
                    backgroundColor: colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#fff' } },
                    tooltip: { enabled: total > 0 }
                }
            }
        });
    }

    // ==========================================
    // BENEFICIOS
    // ==========================================
    function loadBeneficios() {
        $.getJSON('../../backend/php/admin/gestionar_beneficios.php?accion=list', function (data) {
            const $tbodyInternal = $('#tabla-beneficios-internos');
            const $tbodyExternal = $('#tabla-beneficios-externos');
            $tbodyInternal.empty();
            $tbodyExternal.empty();

            if (Array.isArray(data)) {
                data.forEach(b => {
                    const isExternal = b.enlace && (b.enlace.startsWith('http') || b.enlace.startsWith('www'));
                    const duracionTxt = b.dias_duracion ? `<br><span class="badge bg-secondary bg-opacity-50 small font-monospace">${b.dias_duracion} días</span>` : '';

                    const actionsHtml = `
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-light me-1 btn-edit-beneficio" 
                                data-id="${b.ID_beneficio}" 
                                data-tipo="${b.id_tipo}" 
                                data-desc="${b.Descripcion}" 
                                data-valor="${b.Valor}" 
                                data-enlace="${b.enlace || ''}"
                                data-duracion="${b.dias_duracion || ''}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-beneficio" data-id="${b.ID_beneficio}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>`;

                    const rowHtml = `
                        <tr>
                            <td class="text-white-50">${b.ID_beneficio}</td>
                            <td class="text-info">${b.nombre_tipo}</td>
                            <td class="text-white">
                                ${b.Descripcion}
                                ${duracionTxt}
                            </td>
                            <td class="text-white font-monospace">${b.Valor} pts</td>
                            ${isExternal ? `<td class="text-white-50 small text-truncate" style="max-width: 150px;">${b.enlace || '-'}</td>` : ''}
                            ${actionsHtml}
                        </tr>
                    `;

                    if (isExternal) {
                        $tbodyExternal.append(rowHtml);
                    } else {
                        $tbodyInternal.append(rowHtml);
                    }
                });
            }
        });
    }

    function loadBeneficioTypes() {
        $.getJSON('../../backend/php/admin/gestionar_beneficios.php?accion=get_types', function (data) {
            const $sAdd = $('#select-tipo-beneficio');
            const $sEdit = $('#edit-select-tipo-beneficio');
            $sAdd.find('option:not(:first)').remove();
            $sEdit.find('option:not(:first)').remove();
            if (Array.isArray(data)) {
                data.forEach(t => {
                    const opt = `<option value="${t.id_tipo}">${t.descripcion}</option>`;
                    $sAdd.append(opt);
                    $sEdit.append(opt);
                });
            }
        });
    }

    $('#form-add-beneficio').submit(function (e) {
        e.preventDefault();
        const $tipo = $('#select-tipo-beneficio');
        const $desc = $('#descripcion-beneficio');
        const $valor = $('#valor-beneficio');
        const $enlace = $('#enlace-beneficio');
        const $duracion = $('#duracion-beneficio');

        let isValid = true;
        if (!validateField($tipo, "Selecciona un tipo")) isValid = false;
        if (!validateField($desc, "La descripción es obligatoria")) isValid = false;
        if (!validateField($valor, "El valor es obligatorio")) isValid = false;
        if (!validateField($enlace, "El enlace es obligatorio")) isValid = false;

        if (!isValid) return;

        $.post('../../backend/php/admin/gestionar_beneficios.php', {
            accion: 'add',
            id_tipo: $tipo.val(),
            descripcion: $desc.val(),
            valor: $valor.val(),
            enlace: $enlace.val(),
            dias_duracion: $duracion.val()
        }, function (res) {
            if (res.success) {
                $('#addBeneficioModal').modal('hide');
                $('#form-add-beneficio')[0].reset();
                showToast("Beneficio agregado", 'success');
                loadBeneficios();
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json');
    });

    $(document).on('click', '.btn-edit-beneficio', function () {
        const id = $(this).data('id');
        const tipo = $(this).data('tipo');
        const desc = $(this).data('desc');
        const valor = $(this).data('valor');
        const enlace = $(this).data('enlace');
        const duracion = $(this).data('duracion');

        $('#edit-id-beneficio').val(id);
        $('#edit-select-tipo-beneficio').val(tipo);
        $('#edit-descripcion-beneficio').val(desc);
        $('#edit-valor-beneficio').val(valor);
        $('#edit-enlace-beneficio').val(enlace);
        $('#edit-duracion-beneficio').val(duracion);

        $('#editBeneficioModal').modal('show');
    });

    $('#form-edit-beneficio').submit(function (e) {
        e.preventDefault();
        const $id = $('#edit-id-beneficio');
        const $tipo = $('#edit-select-tipo-beneficio');
        const $desc = $('#edit-descripcion-beneficio');
        const $valor = $('#edit-valor-beneficio');
        const $enlace = $('#edit-enlace-beneficio');
        const $duracion = $('#edit-duracion-beneficio');

        let isValid = true;
        if (!validateField($tipo, "Selecciona un tipo")) isValid = false;
        if (!validateField($desc, "La descripción es obligatoria")) isValid = false;
        if (!validateField($valor, "El valor es obligatorio")) isValid = false;
        if (!validateField($enlace, "El enlace es obligatorio")) isValid = false;

        if (!isValid) return;

        $.post('../../backend/php/admin/gestionar_beneficios.php', {
            accion: 'edit',
            id: $id.val(),
            id_tipo: $tipo.val(),
            descripcion: $desc.val(),
            valor: $valor.val(),
            enlace: $enlace.val(),
            dias_duracion: $duracion.val()
        }, function (res) {
            if (res.success) {
                $('#editBeneficioModal').modal('hide');
                showToast("Beneficio actualizado", 'success');
                loadBeneficios();
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json');
    });

    let beneficioIdToDelete = null;
    $(document).on('click', '.btn-delete-beneficio', function () {
        beneficioIdToDelete = $(this).data('id');
        $('#deleteBeneficioModal').modal('show');
    });

    $('#btn-confirm-delete-beneficio').click(function () {
        if (!beneficioIdToDelete) return;
        const $btn = $(this); $btn.prop('disabled', true);

        $.post('../../backend/php/admin/gestionar_beneficios.php', {
            accion: 'delete',
            id: beneficioIdToDelete
        }, function (res) {
            $btn.prop('disabled', false);
            $('#deleteBeneficioModal').modal('hide');
            if (res.success) {
                loadBeneficios();
                showToast("Beneficio eliminado", 'success');
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json');
    });

    // Listeners Dashboard
    $('#btn-refresh-stats').click(loadDashboardStats);
    $('#dash-filter-anunciante, #dash-date-start, #dash-date-end').change(loadDashboardStats);


    // ==========================================
    // MENSAJES GLOBALES
    // ==========================================
    function loadMensajesGlobales() {
        $.getJSON('../../backend/php/admin/gestionar_mensajes.php?accion=list_global', function (data) {
            const $tbody = $('#tabla-mensajes');
            $tbody.empty();

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(m => {
                    $tbody.append(`
                        <tr>
                            <td class="text-white-50 small">${m.fecha}</td>
                            <td class="text-white fw-bold">${m.titulo}</td>
                            <td class="text-white-50 small text-truncate" style="max-width: 200px;" title="${m.contenido}">${m.contenido}</td>
                            <td class="text-info small">${m.remitente_nombre || 'Admin'}</td>
                            <td class="text-end">
                                <button onclick="openEditMensaje(${m.id_mensaje}, '${m.titulo.replace(/'/g, "\\'")}', '${m.contenido.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline-warning rounded-pill me-1"><i class="bi bi-pencil-square"></i></button>
                                <button onclick="confirmDeleteMensaje(${m.id_mensaje})" class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                $tbody.append('<tr><td colspan="5" class="text-center text-white-50">No hay mensajes enviados.</td></tr>');
            }
        });
    }

    $('#form-send-message').submit(function (e) {
        e.preventDefault();
        const $titulo = $('#msg-titulo');
        const $contenido = $('#msg-contenido');

        let isValid = true;
        if (!validateField($titulo, "El título es obligatorio")) isValid = false;
        if (!validateField($contenido, "El contenido es obligatorio")) isValid = false;

        if (!isValid) return;

        const $btn = $(this).find('button');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');

        $.post('../../backend/php/admin/gestionar_mensajes.php', {
            accion: 'send_global',
            titulo: $titulo.val(),
            contenido: $contenido.val()
        }, function (res) {
            $btn.prop('disabled', false).html('<i class="bi bi-paperplane me-2"></i>Enviar a Todos');
            if (res.success) {
                showToast('Mensaje global enviado', 'success');
                $('#form-send-message')[0].reset();
                loadMensajesGlobales();
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json').fail(() => {
            $btn.prop('disabled', false).html('<i class="bi bi-paperplane me-2"></i>Enviar a Todos');
            showToast('Error de conexión', 'danger');
        });
    });

    // Funciones para Editar Mensaje
    window.openEditMensaje = function (id, titulo, contenido) {
        $('#edit-id-mensaje').val(id);
        $('#edit-titulo-mensaje').val(titulo);
        $('#edit-contenido-mensaje').val(contenido);
        const modal = new bootstrap.Modal(document.getElementById('editMensajeModal'));
        modal.show();
    };

    $('#form-edit-mensaje').submit(function (e) {
        e.preventDefault();
        const id = $('#edit-id-mensaje').val();
        const titulo = $('#edit-titulo-mensaje').val();
        const contenido = $('#edit-contenido-mensaje').val();

        if (!titulo || !contenido) return;

        const $btn = $(this).find('button');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...');

        $.post('../../backend/php/admin/gestionar_mensajes.php', {
            accion: 'edit_global',
            id_mensaje: id,
            titulo: titulo,
            contenido: contenido
        }, function (res) {
            $btn.prop('disabled', false).text('Actualizar');
            if (res.success) {
                showToast('Mensaje actualizado correctamente', 'success');
                $('#editMensajeModal').modal('hide'); // jQuery helper por si bootstrap instance no es accesible
                // Cerrar modal bootstrap nativo si es necesario
                const modalEl = document.getElementById('editMensajeModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                loadMensajesGlobales();
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json').fail(() => {
            $btn.prop('disabled', false).text('Actualizar');
            showToast('Error de conexión', 'danger');
        });
    });

    // Funciones para Eliminar Mensaje
    let mensajeIdToDelete = null;

    window.confirmDeleteMensaje = function (id) {
        mensajeIdToDelete = id;
        const modal = new bootstrap.Modal(document.getElementById('deleteMensajeModal'));
        modal.show();
    };

    $('#btn-confirm-delete-mensaje').click(function () {
        if (!mensajeIdToDelete) return;

        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Eliminando...');

        $.post('../../backend/php/admin/gestionar_mensajes.php', {
            accion: 'delete_global',
            id_mensaje: mensajeIdToDelete
        }, function (res) {
            $btn.prop('disabled', false).text('Eliminar');
            if (res.success) {
                showToast('Mensaje eliminado', 'success');
                const modalEl = document.getElementById('deleteMensajeModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                loadMensajesGlobales();
            } else {
                showToast(res.error, 'danger');
            }
        }, 'json').fail(() => {
            $btn.prop('disabled', false).text('Eliminar');
            showToast('Error de conexión', 'danger');
        });
    });

}); // Fin document ready
