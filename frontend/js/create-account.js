
$(document).ready(function () {
  // Función auxiliar para toasts personalizados (reutilizada de login.js)
  function showToast(message, type = 'info') {
    const icons = {
      success: 'bi-check-circle-fill',
      error: 'bi-exclamation-triangle-fill',
      warning: 'bi-exclamation-circle-fill',
      info: 'bi-info-circle-fill'
    };

    const icon = icons[type] || icons.info;

    const toastHtml = `
            <div class="custom-toast ${type}">
                <i class="bi ${icon}"></i>
                <span>${message}</span>
            </div>
        `;

    const $toast = $(toastHtml);
    $('#toast-container').append($toast);

    // Eliminar después de 4 segundos
    setTimeout(() => {
      $toast.css('animation', 'fadeOutRight 0.4s ease-in forwards');
      setTimeout(() => $toast.remove(), 400);
    }, 4000);
  }

  $('#crear').click(function () {
    let email = $('#email').val();
    let contra = $('#contra').val();

    if (email === "" || contra === "" || contra.length < 6) {
      showToast("Por favor, ingrese un email válido y una contraseña de al menos 6 caracteres.", "warning");
    }
    else {
      firebase.auth().createUserWithEmailAndPassword(email, contra)
        .then((userCredential) => {
          const user = userCredential.user;
          showToast("Usuario creado con éxito. Enviando correo de verificación...", "success");
          user.sendEmailVerification()
            .then((userCredential) => {
              showToast("Se ha enviado un correo de verificación a: " + email, "info");
              $.post('../../backend/php/UsuarioNuevoaBD.php', { email: email }, function (data) {
                setTimeout(() => {
                  window.location.href = '../views/IniciarSesion.php';
                }, 2000);
              });
            })
            .catch((error) => {
              console.error("Error al enviar el correo de verificación:", error);
              showToast("No se pudo enviar el correo de verificación.", "error");
            });

        })
        .catch((error) => {
          showToast("No se pudo crear la cuenta. Inténtelo más tarde o verifique si el correo ya existe.", "error");
        });
    }
  });
});
