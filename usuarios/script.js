const loginForm = document.getElementById('loginForm');
const loginMensaje = document.getElementById('loginMensaje');

loginForm.addEventListener('submit', function(e) {
  e.preventDefault();

  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value.trim();

  if (!email || !password) {
    loginMensaje.textContent = "Completa todos los campos.";
    loginMensaje.className = "mensaje error";
    return;
  }

  loginMensaje.textContent = "Validando...";
  loginMensaje.className = "mensaje";

  setTimeout(() => {
    //se conecta con API real
    loginMensaje.textContent = "Inicio de sesión exitoso.";
    loginMensaje.className = "mensaje success";
    loginForm.reset();
  }, 1000);
});
