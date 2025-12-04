$(document).ready(function () {
  // Función auxiliar para toasts personalizados
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

  $('#iniciar').click(function () {
    let email = $('#email').val();
    let contra = $('#contra').val();
    let captcha = grecaptcha.getResponse();

    if (captcha === "") {
      showToast("Por favor, completa el reCAPTCHA.", "warning");
      return;
    }

    $('#loader').show();
    $.post('../../backend/php/verificar.php', { email: email, contra: contra, 'g-recaptcha-response': captcha }, function (data) {
      let response = JSON.parse(data);
      switch (response.message) {

        case "faltandatos":
          showToast("Ingrese datos en todos los campos", "warning");
          $('#loader').hide();
          break;

        case "malcaptcha":
          showToast("Ingrese el captcha correctamente", "error");
          grecaptcha.reset();
          $('#loader').hide();
          break;

        case "nocuenta":
          showToast("Dominio de correo inválido", "error");
          $('#loader').hide();
          break;

        case "bien":
          firebase.auth().signInWithEmailAndPassword(email, contra)
            .then((userCredential) => {
              const user = userCredential.user;
              if (user.emailVerified) {
                $('#loader').hide();
                showToast("¡Inicio de sesión exitoso! Redirigiendo...", "success");
                document.cookie = "iniciado=" + email + "; path=/; max-age=" + (60 * 60 * 24 * 365 * 10) + ";";
                setTimeout(() => {
                  window.location.href = '../views/index.php';
                }, 1500); // Pequeño retraso para ver el toast
              }
              else {
                showToast("Por favor verifica tu correo antes de iniciar sesión", "warning");
                $('#loader').hide();
                firebase.auth().signOut();
              }
            })
            .catch((error) => {
              showToast("Correo o contraseña incorrectos", "error");
              $('#loader').hide();
            });
          break;
      }
    });
  });
});
