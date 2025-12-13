$(document).ready(function () {
    loadBeneficios();
});

let userPoints = 0;
let selectedReward = null;

function loadBeneficios() {
    $.getJSON('../../backend/php/cargarBeneficios.php', function (data) {

        // Actualizar puntos del usuario
        userPoints = parseInt(data.puntos || 0);
        $('#user-points-val').text(userPoints);

        const $internalContainer = $('#internal-rewards');
        const $externalContainer = $('#external-rewards');

        $internalContainer.empty();
        $externalContainer.empty();

        if (!data.items || data.items.length === 0) {
            $internalContainer.html('<div class="col-12 text-center text-white-50"><p>No hay recompensas disponibles.</p></div>');
            return;
        }

        data.items.forEach((item, index) => {
            const isCanjeado = data.canjeados.includes(item.ID_beneficio) || data.canjeados.includes(parseInt(item.ID_beneficio));
            const isExternal = item.enlace && (item.enlace.startsWith('http') || item.enlace.startsWith('www'));
            const delay = (index * 0.1) + 's';

            let btnHtml = '';
            if (isCanjeado) {
                btnHtml = `
                    <button class="btn btn-secondary w-100 rounded-pill" disabled>
                        <i class="bi bi-check-circle-fill me-2"></i>Canjeado
                    </button>`;
            } else {
                if (userPoints < item.Valor) {
                    btnHtml = `
                    <button class="btn btn-outline-secondary w-100 rounded-pill text-white-50 custom-disabled" disabled 
                            style="opacity: 0.6; cursor: not-allowed;" title="No tienes suficientes puntos">
                        <i class="bi bi-lock-fill me-2"></i>${item.Valor} Puntos
                    </button>`;
                } else {
                    // Botón habilitado
                    if (isExternal) {
                        btnHtml = `
                        <button class="btn btn-gradient w-100 rounded-pill btn-redeem-external"
                                data-id="${item.ID_beneficio}"
                                data-url="${item.enlace}"
                                data-valor="${item.Valor}"
                                data-nombre="${item.Descripcion}">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Canjear (${item.Valor} pts)
                        </button>`;
                    } else {
                        btnHtml = `
                         <button class="btn btn-gradient w-100 rounded-pill btn-redeem-internal"
                                data-id="${item.ID_beneficio}"
                                data-valor="${item.Valor}"
                                data-nombre="${item.Descripcion}">
                            <i class="bi bi-cart-plus-fill me-2"></i>Canjear (${item.Valor} pts)
                        </button>`;
                    }
                }
            }

            const cardHtml = `
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="glass-panel h-100 rounded-4 hover-scale fade-in-up d-flex flex-column" style="animation-delay: ${delay};">
                        <div class="p-4 text-center border-bottom border-white border-opacity-10 position-relative">
                            <span class="badge bg-dark bg-opacity-50 border border-secondary border-opacity-25 position-absolute top-0 end-0 m-3 rounded-pill text-white-50">
                                ${item.nombre_tipo || 'General'}
                            </span>
                            <i class="bi bi-${isExternal ? 'globe' : 'trophy-fill'} display-1 text-${isExternal ? 'info' : 'warning'} mb-3 d-block drop-shadow"></i>
                            <div class="badge bg-white bg-opacity-10 border border-white border-opacity-25 text-white fs-6 px-3 py-2 rounded-pill mt-2">
                                ${item.Valor} Puntos
                            </div>
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h4 class="card-title text-white fw-bold text-center mb-3">${item.Descripcion}</h4>
                            <p class="text-white-50 small text-center mb-4" title="${item.enlace}">${isExternal ? 'Sitio Externo' : 'Recompensa Interna'}</p>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-10">
                                ${btnHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            if (isExternal) {
                $externalContainer.append(cardHtml);
            } else {
                $internalContainer.append(cardHtml);
            }
        });
    }).fail(function (jqxhr, textStatus, error) {
        console.error("Error loading benefits:", textStatus, error);
        console.log("Response Text:", jqxhr.responseText); // Debugging
        $('#internal-rewards').html(`<div class="col-12 text-center text-danger"><p>Error al cargar recompensas. Intenta recargar la página.</p></div>`);
    });
}

// Handler Modal Externo
$(document).on('click', '.btn-redeem-external', function () {
    selectedReward = {
        id: $(this).data('id'),
        url: $(this).data('url'),
        valor: $(this).data('valor'),
        nombre: $(this).data('nombre')
    };
    $('#modal-external-link').text(selectedReward.url);
    $('#confirmRedemptionModal .modal-title').text('Confirmar canje de ' + selectedReward.nombre);
    $('#confirmRedemptionModal').modal('show');
});

// Confirmar Externo
$('#btn-proceed-redemption').click(function () {
    const $btn = $(this);
    if (!selectedReward) return;

    $btn.prop('disabled', true).text('Procesando...');

    // Procesar canje en backend antes de redirigir
    processRedemption(selectedReward.id, () => {
        // Éxito
        window.open(selectedReward.url, '_blank');
        $('#confirmRedemptionModal').modal('hide');
        $btn.prop('disabled', false).text('Confirmar y Canjear');
        loadBeneficios(); // Recargar para actualizar puntos y estado bloqueado
    }, (err) => {
    });
});

// Handler Interno (Confirmación con Modal)
$(document).on('click', '.btn-redeem-internal', function () {
    selectedReward = {
        id: $(this).data('id'),
        valor: $(this).data('valor'),
        nombre: $(this).data('nombre')
    };
    $('#modal-internal-cost').text(selectedReward.valor);
    $('#confirmInternalModal .modal-title').text('Confirmar canje de ' + selectedReward.nombre);
    $('#confirmInternalModal').modal('show');
});

// Confirmar Interno
$('#btn-proceed-internal').click(function () {
    const $btn = $(this);
    if (!selectedReward) return;

    $btn.prop('disabled', true).text('Procesando...');

    processRedemption(selectedReward.id, () => {
        // Exito
        $('#confirmInternalModal').modal('hide');
        $btn.prop('disabled', false).text('Sí, Canjear');
        showStatusModal('success', '¡Canje Exitoso!', 'Disfruta de tu recompensa.');
        loadBeneficios();
    }, (err) => {
        // Error
        $('#confirmInternalModal').modal('hide');
        $btn.prop('disabled', false).text('Sí, Canjear');
        showStatusModal('error', 'Error al Canjear', err);
    });
});

// Helper para Modal de Status
function showStatusModal(type, title, msg) {
    const $modal = $('#statusModal');
    const $icon = $('#status-icon');
    const $title = $('#status-title');
    const $msg = $('#status-msg');

    if (type === 'success') {
        $icon.attr('class', 'bi bi-check-circle-fill display-1 text-success');
    } else {
        $icon.attr('class', 'bi bi-x-circle-fill display-1 text-danger');
    }

    $title.text(title);
    $msg.text(msg);
    $modal.modal('show');
}

function processRedemption(id, onSuccess, onError) {
    $.post('../../backend/php/procesar_canje.php', { id_beneficio: id }, function (res) {
        if (res.success) {
            onSuccess();
        } else {
            onError(res.error || "No se pudo procesar el canje.");
        }
    }, 'json').fail(() => onError("Error de conexión"));
}
