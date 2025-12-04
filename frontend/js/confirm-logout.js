// Variable para la instancia del modal
let logoutModal;

document.addEventListener('DOMContentLoaded', function () {
  // Inicializar el modal de Bootstrap
  const modalElement = document.getElementById('logout-modal');
  if (modalElement) {
    logoutModal = new bootstrap.Modal(modalElement);
  }

  // Listener para el botón de confirmar logout
  const confirmBtn = document.getElementById('confirm-logout');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      window.location.href = '../../backend/php/cerrarSesion.php';
    });
  }
});

function showLogoutModal() {
  if (logoutModal) {
    logoutModal.show();
  }
}

function closeLogoutModal() {
  if (logoutModal) {
    logoutModal.hide();
  }
}
