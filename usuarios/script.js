const loginForm = document.getElementById('loginForm');
const loginMensaje = document.getElementById('loginMensaje');

loginForm.addEventListener('submit', function (e) {
  e.preventDefault();

  const correo = document.getElementById('email').value.trim();
  const contrasena = document.getElementById('password').value.trim();

  if (!correo || !contrasena) {
    loginMensaje.textContent = "Completa todos los campos.";
    loginMensaje.className = "mensaje error";
    return;
  }

  loginMensaje.textContent = "Validando...";
  loginMensaje.className = "mensaje";

  fetch("login.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `correo=${encodeURIComponent(correo)}&contrasena=${encodeURIComponent(contrasena)}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        loginMensaje.textContent = "Inicio de sesión exitoso.";
        loginMensaje.className = "mensaje success";
        setTimeout(() => {
          window.location.href = "../frontend-coop/index.html";
        }, 1000);
      } else {
        loginMensaje.textContent = data.mensaje;
        loginMensaje.className = "mensaje error";
      }
    })
    .catch(error => {
      loginMensaje.textContent = "Error al conectar con el servidor.";
      loginMensaje.className = "mensaje error";
    });
});
