// frontend/js/comprar_premium.js
document.addEventListener("DOMContentLoaded", function () {
    // Configurar Mercado Pago
    // IMPORTANTE: PUBLIC KEY FRONTEND -> APP_USR-44cd806e-504a-4aa7-a189-3cfb8ec45eab
    const mp = new MercadoPago('APP_USR-44cd806e-504a-4aa7-a189-3cfb8ec45eab', {
        locale: 'es-AR'
    });

    let brickController = null;

    async function createPreference(plan) {
        // Mostrar loader, ocultar wallet
        const walletDiv = document.getElementById('wallet_container');
        const loader = document.getElementById('loading-payment');

        walletDiv.innerHTML = '';
        loader.style.display = 'block';

        try {
            const response = await fetch('../../backend/php/crear_preferencia_mp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `plan=${plan}`
            });

            const data = await response.json();

            loader.style.display = 'none';

            if (data.preference_id) {
                const bricksBuilder = mp.bricks();

                // Renderizar Wallet Brick
                brickController = await bricksBuilder.create("wallet", "wallet_container", {
                    initialization: {
                        preferenceId: data.preference_id,
                    },
                    customization: {
                        texts: {
                            action: 'buy',
                            valueProp: 'security_details',
                        },
                    },
                });
            } else {
                walletDiv.innerHTML = `<p class="text-danger text-center">Error: ${data.error}</p>`;
            }
        } catch (error) {
            console.error('Error:', error);
            loader.innerHTML = '<p class="text-danger">Error al conectar con el servidor.</p>';
        }
    }

    // Inicializar con el plan seleccionado por defecto
    const selectedPlan = document.querySelector('input[name="plan"]:checked').value;
    createPreference(selectedPlan);

    // Escuchar cambios
    const radios = document.querySelectorAll('input[name="plan"]');
    const debugBtn = document.getElementById('btn-debug-pay');

    function updateDebugLink(plan) {
        if (debugBtn) {
            debugBtn.href = `../../backend/php/simular_pago_debug.php?plan=${plan}`;
        }
    }

    radios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            const plan = e.target.value;
            createPreference(plan);
            updateDebugLink(plan);
        });
    });

    // Init
    updateDebugLink(selectedPlan);
});
