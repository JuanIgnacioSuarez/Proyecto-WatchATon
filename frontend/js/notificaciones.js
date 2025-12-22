document.addEventListener('DOMContentLoaded', function () {
    console.log("Notificaciones: DOMContentLoaded");
    let retries = 0;
    // Esperar a que Bootstrap esté cargado
    const checkBootstrap = setInterval(function () {
        retries++;
        if (retries % 10 === 0) console.log("Notificaciones: Esperando Bootstrap...", window.bootstrap, window.jQuery);

        if (window.bootstrap && window.jQuery) {
            clearInterval(checkBootstrap);
            console.log("Notificaciones: Bootstrap y jQuery detectados. Iniciando.");
            initNotifications(window.jQuery);
        }
        // Timeout de seguridad
        if (retries > 100) {
            clearInterval(checkBootstrap);
            console.error("Notificaciones: Timeout esperando Bootstrap.");
        }
    }, 100);
});

function initNotifications($) {
    $(document).ready(function () {
        console.log("Notificaciones: Iniciando initNotifications...");
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
                console.log("Notificaciones: Click en campana detectado");
                // e.preventDefault(); 
                // e.stopPropagation();
                // dropdownInstance.toggle(); 
                // Probamos DEJAR que bootstrap lo maneje primero, si tiene data-bs-toggle
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

        // Listener para eliminar notificación
        $(document).on('click', '.btn-delete-notification', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const id = $(this).data('id');
            deleteNotification(id, $(this));
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
            const isSanction = m.tipo == 1;
            const isApproved = m.tipo == 2;  // Reclamo Aprobado
            const isRejected = m.tipo == 3;  // Reclamo Rechazado
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
            } else if (isApproved) {
                bgClass = 'bg-success bg-opacity-10';
                borderClass = 'border-success border-opacity-50';
                icon = 'bi-check-circle-fill';
                iconColor = 'text-success';
            } else if (isRejected) {
                bgClass = 'bg-secondary bg-opacity-10';
                borderClass = 'border-secondary border-opacity-50';
                icon = 'bi-x-circle-fill';
                iconColor = 'text-secondary';
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
                                </div>
                                <p class="mb-1 small text-white-50 text-break" style="font-size: 0.85rem;">
                                    ${m.contenido.replace(/\n/g, '<br>')}
                                </p>
                                ${actionBtn}
                            </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end justify-content-between ms-2">
                             ${!isGlobal ? `
                                <button class="btn btn-link text-white-50 p-0 mb-2 btn-delete-notification" data-id="${m.id_mensaje}" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                             ` : ''}
                             ${readBtn}
                        </div>
                    </div>
                </div>
            `;
            $list.append(html);
        });
    });
}

// Handler para eliminar notificación
// Variables para manejar el borrado modal
let notifIdToDelete = null;
let $btnNotifToDelete = null;

// Handler para eliminar notificación (Abre Modal)
function deleteNotification(id, $btn) {
    notifIdToDelete = id;
    $btnNotifToDelete = $btn;

    // Abrir modal usando Bootstrap API
    const modalEl = document.getElementById('delete-notification-modal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    } else {
        // Fallback
        if (confirm('¿Seguro que quieres borrar?')) confirmDeleteNotification();
    }
}

// Función real de borrado
function confirmDeleteNotification() {
    if (!notifIdToDelete) return;

    // Cerrar modal
    closeDeleteModal();

    const id = notifIdToDelete;
    const $btn = $btnNotifToDelete;

    $.post('../../backend/php/eliminarMensaje.php', { id_mensaje: id }, function (response) {
        if (response.success) {
            const $item = $btn.closest('.list-group-item');
            $item.fadeOut(300, function () {
                $(this).remove();
                if ($('#lista-notificaciones').children().length === 0) {
                    $('#lista-notificaciones').html('<div class="text-center p-4 text-white-50"><i class="bi bi-inbox fs-1 mb-2"></i><p class="mb-0">No tienes mensajes.</p></div>');
                }
            });

            const isRead = $item.find('h6').hasClass('opacity-75');
            if (!isRead) {
                let count = parseInt($('#notificaciones-badge').text());
                if (count > 0) {
                    count--;
                    $('#notificaciones-badge').text(count);
                    if (count === 0) $('#notificaciones-badge').addClass('d-none');
                }
            }
        } else {
            console.error(response.error);
        }
    }, 'json');
}

function closeDeleteModal() {
    const modalEl = document.getElementById('delete-notification-modal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}

$(document).on('click', '#confirm-delete-notif', function () {
    confirmDeleteNotification();
});

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
