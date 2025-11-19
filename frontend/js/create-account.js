
$(document).ready(function(){
      $('#crear').click(function(){
        let email=$('#email').val();
        let contra=$('#contra').val();

          if(email === "" || contra === "" || contra.length < 6){
         alert("Por favor, ingrese un email válido y una contraseña de al menos 6 caracteres.")
          }
          else{
            firebase.auth().createUserWithEmailAndPassword(email,contra)
            .then((userCredential)=>{
               const user = userCredential.user;
              alert("Usuario creado con exito , se enviara un correo para poder verificar el mismo");
               user.sendEmailVerification()                                                             //Utilizamos firebase para enviar un correo de confirmacion
                .then((userCredential) => {
                  alert("Se ha enviado un correo de verificación a: " + email);             
                 $.post('../../backend/php/UsuarioNuevoaBD.php',{email:email},function(data){
                  window.location.href= '../views/IniciarSesion.php';
                 });
                })
                .catch((error) => {
                  console.error("Error al enviar el correo de verificación:", error);
                  alert("No se pudo enviar el correo de verificación.");
                });
          
            })
            .catch((error)=>{
              alert(" No se pudo crear la cuenta :c, intentelo mas tarde");
            });
          }
      });
  });
