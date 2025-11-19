$(document).ready(function(){  //Utilizamos JQUERY para no tener que recargar la pagina y verificar que el usuario ingresa los datos de forma correcta.
    $('#iniciar').click(function(){
      let email=$('#email').val();
      let contra=$('#contra').val();
      let captcha= grecaptcha.getResponse();   //Esto se utiliza para el reCaptcha

       if(captcha === "") {
            alert("Por favor, completa el reCAPTCHA.");
            return;  // No nos interesa seguir si el captcha no es completado
        }


      $('#loader').show();
      $.post('../../backend/php/verificar.php',{email:email,contra:contra,'g-recaptcha-response':captcha},function(data){  //Enviamos a un php que verifica los datos 
        let response = JSON.parse(data);
        switch(response.message){                 //Usamos un switch para evaluar las distintas respuestas

          case "faltandatos":
            alert("Ingrese datos en todos los campos");
                $('#loader').hide();
            break;

          case "malcaptcha":
            alert("Ingrese el captcha correctamente");
                 grecaptcha.reset();   //Reseteamos el captcha
                $('#loader').hide();
            break;

          case "nocuenta":
            alert("Dominio de correo invalido :(");
                $('#loader').hide();
            break;

          case "bien":
            firebase.auth().signInWithEmailAndPassword(email,contra)   //Llamamos a esta funcion de firebase para intentar el inicio de sesion
              .then((userCredential)=>{
                const user = userCredential.user;
                   if (user.emailVerified) {
                    $('#loader').hide();
                  alert("Inicio de sesion exitoso , redirijiendo...");
                    document.cookie = "iniciado=" + email + "; path=/; max-age=" + (60 * 60 * 24 * 365 * 10) + ";";  //Creo una cookie con el email
                    window.location.href = '../views/index.php';
                }
                else{
                  alert("Por favor verifica tu correo antes de iniciar sesion!");
                  $('#loader').hide();
                  firebase.auth().signOut();
                }
              })
              .catch((error)=>{  //Aqui el inicio falla
                alert("Cuenta inexistente");
                $('#loader').hide();
              });
              break;
          }     
      });
    });
  });
