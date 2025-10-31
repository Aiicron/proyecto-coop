async function cambiarIdioma(lang) {
  try {
    const resp = await fetch(`lang/${lang}.json`);
    const textos = await resp.json();

    document.querySelectorAll("[data-i18n]").forEach(el => {
      const key = el.getAttribute("data-i18n");
      if (textos[key]) el.textContent = textos[key];
    });

    localStorage.setItem("lang", lang);
  } catch (err) {
    console.log("Error cargando idioma:", err);
  }
}

document.addEventListener("DOMContentLoaded", () => {

  const select = document.getElementById("language-select");
  const lang = localStorage.getItem("lang") || "es";
  select.value = lang;
  cambiarIdioma(lang);

  select.addEventListener("change", e => {
    cambiarIdioma(e.target.value);
  });

  const form = document.getElementById("formulario");
  const mensaje = document.getElementById("mensaje");

  form.addEventListener("submit", e => {
    e.preventDefault();
    mensaje.textContent = "Solicitud enviada con éxito. Nos contactaremos con usted dentro de las próximas 48 horas.";
    mensaje.className = "mensaje success";
    form.reset();
  });

  const passwordInput = document.getElementById("contraseña");
  const toggle = document.querySelector(".toggle-password");

  toggle.addEventListener("click", () => {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      toggle.classList.remove("fa-eye");
      toggle.classList.add("fa-eye-slash");
    } else {
      passwordInput.type = "password";
      toggle.classList.remove("fa-eye-slash");
      toggle.classList.add("fa-eye");
    }
  });
});
