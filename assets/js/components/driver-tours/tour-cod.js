document.addEventListener('DOMContentLoaded', () => {
    const iniciarTutorialButton = document.getElementById('navbar-iniciar-tutorial');
    const cerrarTutorialButton = document.getElementById('navbar-cerrar-tutorial');

    if (!iniciarTutorialButton || !cerrarTutorialButton || !window.driver?.js) {
        return;
    }

    function actualizarBotonTutorial() {
        const estadoTutorial = localStorage.getItem('tutorial') || 'inactivo';
        const tutorialIniciado = estadoTutorial === 'activo' || estadoTutorial === 'pendiente';

        iniciarTutorialButton.innerHTML = tutorialIniciado
            ? '<i class="bi bi-arrow-repeat"></i> Repetir tutorial'
            : '<i class="bi bi-question-circle"></i> Iniciar tutorial';

        cerrarTutorialButton.hidden = !tutorialIniciado;
        cerrarTutorialButton.innerHTML = estadoTutorial === 'pendiente'
            ? '<i class="bi bi-check-circle"></i> Finalizar tutorial'
            : '<i class="bi bi-x-circle"></i> Cerrar tutorial';
    }

    const driver = window.driver.js.driver({
        allowClose: false,
        showProgress: true,
        disableActiveInteraction: true,
        stagePadding: 10,
        nextBtnText: 'Siguiente',
        prevBtnText: 'Atrás',
        doneBtnText: 'Finalizar',

        onDestroyStarted: () => {
            if (localStorage.getItem('tutorial') === 'activo') {
                localStorage.setItem('tutorial', 'pendiente');
            }
            driver.destroy();
        },

        onDestroyed: () => {
            document.body.classList.remove('tutorial-active');
            actualizarBotonTutorial();
        },

        steps: [
            {
                element: '#bloque-productos',
                popover: {
                    title: 'Selecciona un producto',
                    description: 'En un flujo de compra normal, el usuario escogería uno de los productos disponibles. Este catálogo ilustra cómo podría presentarse la oferta de tu comercio en una integración con Place to Pay.'
                }
            },
            {
                element: '#btnBuy',
                popover: {
                    title: 'Inicia el pago',
                    description: 'Después de seleccionar un producto e ingresar los datos solicitados, el usuario haría clic en "Comprar ahora". En este punto comienza la integración con el Web Checkout de Place to Pay.'
                }
            },
            {
                popover: {
                    title: 'Solicitud desde el backend',
                    description: 'El backend recibe los datos de la compra, crea la orden y envía a Place to Pay una solicitud autenticada con la referencia, descripción, moneda, total, URL de retorno y demás datos necesarios para iniciar la sesión de pago.'
                }
            },
            {
                popover: {
                    title: 'Redirección al Web Checkout',
                    description: 'Después de procesar la solicitud, Place to Pay devuelve una processUrl. El backend redirige al usuario a esa URL para que complete el proceso de Web Checkout de forma segura.'
                }
            }
        ]
    });

    function iniciarTourCod() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourCod);

    cerrarTutorialButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (localStorage.getItem('tutorial') === 'pendiente') {
            localStorage.setItem('tutorial', 'inactivo');
            actualizarBotonTutorial();
            return;
        }

        localStorage.setItem('tutorial', 'pendiente');
        driver.destroy();
    });

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });

    actualizarBotonTutorial();

    if (localStorage.getItem('tutorial') === 'pendiente') {
        iniciarTourCod();
    }
});
