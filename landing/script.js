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
    console.error("Error cargando idioma:", err);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const lang = localStorage.getItem("lang") || "es";
  document.getElementById("language-select").value = lang;
  cambiarIdioma(lang);

  document.getElementById("language-select").addEventListener("change", (e) => {
    cambiarIdioma(e.target.value);
  });
});
