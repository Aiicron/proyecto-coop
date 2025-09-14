document.addEventListener("DOMContentLoaded", () => {
    const hamburger = document.getElementById("hamburger");
    const sidebar = document.getElementById("sidebar");

    hamburger.addEventListener("click", (e) => {
        e.stopPropagation();
        sidebar.classList.toggle("active");
    });

    document.addEventListener("click", (e) => {
        if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
            sidebar.classList.remove("active");
        }
    });
});
