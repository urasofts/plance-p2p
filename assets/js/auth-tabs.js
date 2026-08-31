// Cambia entre los paneles de "Iniciar sesión" y "Registrarse" en index.php con una animación de deslizamiento.
document.addEventListener("DOMContentLoaded", function () {

    const tabs = document.querySelectorAll(".auth-tab");
    const forms = document.querySelectorAll(".auth-form");

    function activate(targetId) {
        tabs.forEach(tab => tab.classList.toggle("is-active", tab.dataset.target === targetId));

        forms.forEach(form => {
            if (form.getAttribute("id") === targetId) {
                form.classList.remove("is-leaving");
                form.classList.add("is-active");
            } else if (form.classList.contains("is-active")) {
                form.classList.add("is-leaving");
                form.classList.remove("is-active");
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener("click", () => activate(tab.dataset.target));
    });
});
