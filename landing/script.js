const form = document.getElementById('formulario');
const mensaje = document.getElementById('mensaje');

form.addEventListener('submit', function (e) {
  e.preventDefault();

  const nombre = document.getElementById('nombre').value.trim();
  const email = document.getElementById('email').value.trim();
  const telefono = document.getElementById('telefono').value.trim();
  const cedula = document.getElementById('cedula').value.trim();

  if (!nombre || !email || !telefono || !cedula) {
    mensaje.textContent = "Todos los campos son obligatorios.";
    mensaje.className = "mensaje error";
    return;
  }

  mensaje.textContent = "Enviando...";
  mensaje.className = "mensaje";

  setTimeout(() => {
    mensaje.textContent = "Solicitud enviada con exito! Dentro de las próximas 24 hs nos pondremos en contacto al correo ingresado.";
    mensaje.className = "mensaje success";
    form.reset();
  }, 1000);
});
