//Ejecutando funciones
document.getElementById("btn__iniciar-sesion").addEventListener("click", iniciarSesion);
document.getElementById("btn__registrarse").addEventListener("click", register);
window.addEventListener("resize", anchoPage);

//Declarando variables
var formulario_login = document.querySelector(".formulario__login");
var formulario_register = document.querySelector(".formulario__register");
var contenedor_login_register = document.querySelector(".contenedor__login-register");
var caja_trasera_login = document.querySelector(".caja__trasera-login");
var caja_trasera_register = document.querySelector(".caja__trasera-register");

    //FUNCIONES

function anchoPage(){

    if (window.innerWidth > 850){
        caja_trasera_register.style.display = "block";
        caja_trasera_login.style.display = "block";
    }else{
        caja_trasera_register.style.display = "block";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.display = "none";
        formulario_login.style.display = "block";
        contenedor_login_register.style.left = "0px";
        formulario_register.style.display = "none";   
    }
}

anchoPage();


    function iniciarSesion(){
        if (window.innerWidth > 850){
            formulario_login.style.display = "block";
            contenedor_login_register.style.left = "10px";
            formulario_register.style.display = "none";
            caja_trasera_register.style.opacity = "1";
            caja_trasera_login.style.opacity = "0";
        }else{
            formulario_login.style.display = "block";
            contenedor_login_register.style.left = "0px";
            formulario_register.style.display = "none";
            caja_trasera_register.style.display = "block";
            caja_trasera_login.style.display = "none";
        }
    }

    function register(){
        if (window.innerWidth > 850){
            formulario_register.style.display = "block";
            contenedor_login_register.style.left = "410px";
            formulario_login.style.display = "none";
            caja_trasera_register.style.opacity = "0";
            caja_trasera_login.style.opacity = "1";
        }else{
            formulario_register.style.display = "block";
            contenedor_login_register.style.left = "0px";
            formulario_login.style.display = "none";
            caja_trasera_register.style.display = "none";
            caja_trasera_login.style.display = "block";
            caja_trasera_login.style.opacity = "1";
        }

        




}
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
