/**
 * premium_ui.js
 * Maneja la lógica visual para usuarios premium
 * V5.0 - Premium Logo Badge (Gold & Sparkles)
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log("Premium UI: Checking status...");
    checkPremium();
});

function checkPremium() {
    const isPremium = getCookie('Premium') === 'true';
    console.log("Is Premium?", isPremium);

    if (isPremium) {
        injectPremiumStyles();
        addPremiumBadgeToLogo();
        injectSkipAdsToggle(); // Nueva función para el botón
    }
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function injectPremiumStyles() {
    // Estilos para el texto dorado y la animación de brillo
    const style = document.createElement('style');
    style.innerHTML = `
        .premium-badge {
            font-size: 0.8em;
            margin-left: 8px;
            background: linear-gradient(45deg, #FFD700, #FDB931, #FFD700);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
            animation: shine 3s infinite linear;
            text-shadow: 0px 0px 5px rgba(255, 215, 0, 0.5);
            vertical-align: middle;
        }

        .premium-badge::after {
            content: '✨';
            font-size: 0.8em;
            position: absolute;
            top: -5px;
            right: -10px;
            animation: twinkle 1.5s infinite alternate;
            color: #FFD700;
            text-shadow: none;
        }

        @keyframes shine {
            0% { filter: brightness(100%); }
            50% { filter: brightness(150%); }
            100% { filter: brightness(100%); }
        }

        @keyframes twinkle {
            0% { opacity: 0.5; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1.2) rotate(20deg); }
        }
    `;
    document.head.appendChild(style);
}

function addPremiumBadgeToLogo() {
    // Buscar el logo en el header (h1.brand-text o similar)
    // En cabecera.php es: <h1 class="h4 m-0 brand-text">WatchATon</h1>
    const logoText = document.querySelector('header .brand-text');

    if (logoText) {
        console.log("Logo found, appending Premium badge...");

        // Evitar duplicados
        if (logoText.querySelector('.premium-badge')) return;

        const badge = document.createElement('span');
        badge.className = 'premium-badge';
        badge.innerText = 'PREMIUM';

        logoText.appendChild(badge);
        logoText.appendChild(badge);
    } else {
        console.warn("Premium UI: Elemento logo (.brand-text) no encontrado en header.");
    }
}

/**
 * Inyecta el botón de "Omitir Anuncios" en la barra de navegación
 * Este botón permite al usuario Premium activar/desactivar la publicidad
 */
function injectSkipAdsToggle() {
    // Buscar el contenedor de navegación donde están los otros botones
    const navContainer = document.querySelector('header .nav');

    if (navContainer) {
        // Verificar si ya existe para evitar duplicados
        if (document.getElementById('btn-skip-ads')) return;

        // Leer estado actual de la cookie
        const skipAdsEnabled = getCookie('SkipAds') === 'true';

        // Crear el botón
        const toggleBtn = document.createElement('a');
        toggleBtn.id = 'btn-skip-ads';
        toggleBtn.href = '#';
        toggleBtn.className = 'nav-link-custom d-flex align-items-center me-3 clickable';
        toggleBtn.style.cursor = 'pointer';
        toggleBtn.style.transition = 'all 0.3s ease';

        // Configurar apariencia inicial
        updateToggleAppearance(toggleBtn, skipAdsEnabled);

        // Evento Click
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleSkipAds(toggleBtn);
        });

        // Insertar antes del botón de logout o al final
        const logoutBtn = document.getElementById('logout-button');
        if (logoutBtn) {
            navContainer.insertBefore(toggleBtn, logoutBtn);
        } else {
            navContainer.appendChild(toggleBtn);
        }
    }
}

/**
 * Alterna el estado de Omitir Anuncios
 */
function toggleSkipAds(btnElement) {
    const currentState = getCookie('SkipAds') === 'true';
    const newState = !currentState;

    // Guardar cookie (expira en 30 días)
    setCookie('SkipAds', newState.toString(), 30);

    // Actualizar UI
    updateToggleAppearance(btnElement, newState);

    // Feedback al usuario personalizado
    if (newState) {
        // Activó "Sin Anuncios"
        showToastGlobal("¡No sumarás puntos!", "warning");
    } else {
        // Desactivó "Sin Anuncios" (Volvió a ver anuncios)
        showToastGlobal("¿Listo para empezar a sumar de nuevo?", "success");
    }
}

/**
 * Actualiza el texto y estilo del botón según el estado
 */
function updateToggleAppearance(btn, isEnabled) {
    if (isEnabled) {
        btn.innerHTML = '<i class="bi bi-slash-circle-fill me-2 text-success"></i>Sin Anuncios';
        btn.style.opacity = '1';
        btn.title = "Click para volver a ver anuncios";
    } else {
        btn.innerHTML = '<i class="bi bi-play-circle me-2 text-secondary"></i>Ver Anuncios';
        btn.style.opacity = '0.7';
        btn.title = "Click para omitir anuncios en videos";
    }
}

// --- Sistema de Notificaciones Global ---

/**
 * Muestra una notificación Toast en cualquier parte del sitio.
 * Crea el contenedor y los estilos si no existen.
 */
function showToastGlobal(message, type = 'info') {
    ensureToastStyles();
    const container = ensureToastContainer();

    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-exclamation-triangle-fill',
        warning: 'bi-exclamation-circle-fill',
        info: 'bi-info-circle-fill'
    };

    const icon = icons[type] || icons.info;

    // Crear elemento Toast
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;
    toast.innerHTML = `
        <i class="bi ${icon}"></i>
        <span>${message}</span>
    `;

    // Agregar al contenedor
    container.appendChild(toast);

    // Animar entrada (CSS animation se encarga, pero aseguramos reflow si fuera necesario)

    // Remover después de un tiempo
    setTimeout(() => {
        toast.style.animation = 'fadeOutRight 0.4s ease-in forwards';
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 400);
    }, 4000);
}

function ensureToastContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

function ensureToastStyles() {
    if (document.getElementById('toast-global-styles')) return;

    const style = document.createElement('style');
    style.id = 'toast-global-styles';
    style.innerHTML = `
        #toast-container {
            position: fixed;
            top: 80px; /* Debajo de navbar */
            right: 20px;
            z-index: 10060; /* Encima de modales bootstrap (normalmente 1055, subimos un poco mas) */
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .custom-toast {
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            animation: slideInRight 0.4s ease-out;
            pointer-events: auto;
        }

        .custom-toast.success { border-left: 4px solid #198754; }
        .custom-toast.error { border-left: 4px solid #dc3545; }
        .custom-toast.warning { border-left: 4px solid #ffc107; }
        .custom-toast.info { border-left: 4px solid #0dcaf0; }

        .custom-toast i { font-size: 1.2rem; }
        .custom-toast.success i { color: #198754; }
        .custom-toast.error i { color: #dc3545; }
        .custom-toast.warning i { color: #ffc107; }
        .custom-toast.info i { color: #0dcaf0; }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}

/**
 * Helper para setear cookies
 */
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

// Modificar checkPremium para llamar a la nueva función
const originalCheckPremium = checkPremium; // Guardar referencia si fuera necesario
