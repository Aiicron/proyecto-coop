document.addEventListener("DOMContentLoaded", () => {
    const perfilContainer = document.querySelector(".perfil-container");
    const perfilIcon = document.querySelector(".perfil-icon");

    perfilIcon.addEventListener("click", () => {
        perfilContainer.classList.toggle("active");
    });

    document.addEventListener("click", (e) => {
        if (!perfilContainer.contains(e.target)) {
            perfilContainer.classList.remove("active");
        }
    });
});
