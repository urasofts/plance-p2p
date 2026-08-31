document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".dashboard-card-1, .dashboard-card-2, .dashboard-card-3 , .dashboard-card-4, .dashboard-card-5, .dashboard-card-6");

    cards.forEach((card, index) => {
        card.style.opacity = 0;
        card.style.transform = "translateY(30px)";

        setTimeout(() => {
            card.style.transition = "0.6s ease";
            card.style.opacity = 1;
            card.style.transform = "translateY(0)";
        }, index * 200);
    });
});

const saludo = document.querySelector(".home-card");

if (saludo) {
    const hora = new Date().getHours();

    let mensaje = "Hola";

    if (hora < 12) mensaje = "Buenos días";
    else if (hora < 18) mensaje = "Buenas tardes";
    else mensaje = "Buenas noches";

    saludo.textContent = `${mensaje}, Usuario 👋`;
}



document.addEventListener("DOMContentLoaded", function () {

    const navbar = document.querySelector(".navbar");
    if (!navbar) return;

    window.addEventListener("scroll", function () {
        if (window.scrollY > 20) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });

});

let productoSeleccionado = null;
let precioSeleccionado = 0;

function seleccionarProducto(nombre, precio) {
  productoSeleccionado = nombre;
  precioSeleccionado = precio;

  document.getElementById("producto").innerText = "Producto: " + nombre;
  document.getElementById("precio").innerText = "Precio: $" + precio;
}

function comprar() {
  if (!productoSeleccionado) {
    alert("Selecciona un producto");
    return;
  }

  // Aquí conectas con tu backend PHP
  window.location.href = "crear_pago.php?producto=" + productoSeleccionado + "&precio=" + precioSeleccionado;

}
