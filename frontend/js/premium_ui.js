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
    } else {
        console.warn("Premium UI: Logo element (.brand-text) not found in header.");
    }
}
