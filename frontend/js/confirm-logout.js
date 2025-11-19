function showLogoutModal() {
  document.getElementById('logout-modal').style.display = 'flex';
}

function closeLogoutModal() {
  document.getElementById('logout-modal').style.display = 'none';
}

document.getElementById('confirm-logout').addEventListener('click', function() {
  window.location.href = '../../backend/php/cerrarSesion.php';
});

document.getElementById('cancel-logout').addEventListener('click', function() {
  document.getElementById('logout-modal').style.display = 'none';
});

window.onclick = function(event) {
  const modal = document.getElementById('logout-modal');
  if (event.target == modal) {
    modal.style.display = 'none';
  }
};
