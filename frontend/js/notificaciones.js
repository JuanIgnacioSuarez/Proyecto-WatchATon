document.addEventListener('DOMContentLoaded', function () {
    const checkJquery = setInterval(function () {
        if (window.jQuery) {
            clearInterval(checkJquery);
            initNotifications(window.jQuery);
        }
    }, 100);
});

function initNotifications($) {
    $(document).ready(function () {
        console.log("Notificaciones: Iniciando...");
        // Re-inicializar dropdowns de bootstrap explícitamente si es necesario
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl)
        });

        // Solo cargar si el elemento existe (usuario logueado)
        const $dropdownToggle = $('#notificacionesDropdown');
        if ($dropdownToggle.length) {
            console.log("Notificaciones: Dropdown encontrado.");

            // Inicializar explícitamente Bootstrap Dropdown para este elemento
            // ya que puede no tener la clase .dropdown-toggle
            const dropdownInstance = new bootstrap.Dropdown($dropdownToggle[0]);

            // Debug click y Toggle Manual
            $dropdownToggle.on('click', function (e) {
                console.log("Click en campana detectado");
                e.preventDefault();
                e.stopPropagation();
                dropdownInstance.toggle();
            });

            loadNotifications();

            // Recargar cada 60 segundos
            setInterval(loadNotifications, 60000);
        }

        // Delegación de eventos para botones dentro del dropdown
        $(document).on('click', '.btn-mark-read', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Evitar cierre del dropdown
            const id = $(this).data('id');
            markAsRead(id, $(this));
        });

        $(document).on('click', '.btn-view-sanction', function (e) {
            // Redirigir a perfil y abrir tab de sanciones
            // Si ya estamos en perfil, solo cambiar tab
            if (window.location.pathname.includes('perfil.php')) {
                e.preventDefault();
                $('#btn-mis-sanciones').click();
            } else {
                // Dejar que el link funcione (href="perfil.php#sanciones")
                // Pero necesitamos manejar el hash en perfil.js
            }
        });
    });
}

function loadNotifications() {
    $.getJSON('../../backend/php/cargarMensajes.php', function (data) {
        if (data.error) return;

        // Actualizar badge
        if (data.unreadCount > 0) {
            $('#notificaciones-badge').text(data.unreadCount).removeClass('d-none');
        } else {
            $('#notificaciones-badge').addClass('d-none');
        }

        const $list = $('#lista-notificaciones');
        $list.empty();

        if (data.mensajes.length === 0) {
            $list.html('<div class="text-center p-4 text-white-50"><i class="bi bi-inbox fs-1 mb-2"></i><p class="mb-0">No tienes mensajes.</p></div>');
            return;
        }

        data.mensajes.forEach(m => {
            const isGlobal = m.id_destinatario == 0 || m.id_destinatario == null;
            const isSanction = m.tipo === 'sancion';
            const isRead = m.leido == 1;

            let bgClass = 'bg-transparent';
            let borderClass = 'border-secondary border-opacity-25';
            let icon = 'bi-envelope';
            let iconColor = 'text-white';

            if (isGlobal) {
                bgClass = 'bg-warning bg-opacity-10';
                borderClass = 'border-warning border-opacity-50';
                icon = 'bi-megaphone-fill';
                iconColor = 'text-warning';
            } else if (isSanction) {
                bgClass = 'bg-danger bg-opacity-10';
                borderClass = 'border-danger border-opacity-50';
                icon = 'bi-exclamation-triangle-fill';
                iconColor = 'text-danger';
            }

            // Botón de acción principal
            let actionBtn = '';
            if (isSanction) {
                // Si estamos en perfil.php, usamos #, si no, perfil.php
                const link = window.location.pathname.includes('perfil.php') ? '#' : 'perfil.php';
                actionBtn = `<a href="${link}" class="btn btn-sm btn-outline-danger w-100 mt-2 btn-view-sanction">
                                <i class="bi bi-eye me-1"></i>Ver en Mis Sanciones
                             </a>`;
            }

            // Botón marcar leido (solo si no es global y no está leido)
            let readBtn = '';
            if (!isGlobal && !isRead) {
                readBtn = `<button class="btn btn-link text-white-50 p-0 ms-2 btn-mark-read" data-id="${m.id_mensaje}" title="Marcar como leído">
                                <i class="bi bi-check-circle"></i>
                           </button>`;
            }

            const html = `
                <div class="list-group-item ${bgClass} border-bottom ${borderClass} text-white p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex w-100">
                            <div class="me-3 mt-1">
                                <i class="bi ${icon} ${iconColor} fs-4"></i>
                            </div>
                            <div class="w-100">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold ${isRead ? 'opacity-75' : ''}">${m.titulo}</h6>
                                    <small class="text-white-50 ms-2" style="font-size: 0.7rem;">${formatDate(m.fecha)}</small>
                                    ${readBtn}
                                </div>
                                <p class="mb-1 small text-white-50 text-break" style="font-size: 0.85rem;">
                                    ${m.contenido.replace(/\n/g, '<br>')}
                                </p>
                                ${actionBtn}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $list.append(html);
        });
    });
}

function markAsRead(id, $btn) {
    $.post('../../backend/php/marcarMensajeLeido.php', { id_mensaje: id }, function (response) {
        if (response.success) {
            $btn.closest('.list-group-item').find('h6').addClass('opacity-75');
            $btn.remove();

            // Actualizar badge localmente
            let count = parseInt($('#notificaciones-badge').text());
            if (count > 0) {
                count--;
                $('#notificaciones-badge').text(count);
                if (count === 0) $('#notificaciones-badge').addClass('d-none');
            }
        }
    }, 'json');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;

    // Si es hoy, mostrar hora
    if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    // Si es ayer
    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    if (date.toDateString() === yesterday.toDateString()) {
        return 'Ayer';
    }
    // Si no, fecha corta
    return date.toLocaleDateString();
}

// Función global para marcar todo (opcional, por ahora marca visualmente o recarga)
function markAllRead() {
    // Implementar backend si se desea, por ahora solo recarga para forzar sync
    loadNotifications();
}
