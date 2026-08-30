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
        advanceOnClick: true,
        stagePadding: 10,
        smoothScroll: true,
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
                element: '#periodSelector',
                popover: {
                    title: 'Mensual o anual',
                    description: 'Esta elección define la periodicidad del cobro recurrente ("M" o "Y") que se envía a PlacetoPay dentro del bloque "recurring".'
                }
            },
            {
                element: '#productsPanel',
                popover: {
                    title: 'Catálogo de IA\'s',
                    description: 'El catálogo de planes de inteligencia artificial que vería tu cliente, con precio distinto según elija mensual o anual.'
                }
            },
            {
                element: '#simModeWrap',
                popover: {
                    title: 'Modo de simulación',
                    description: 'Aquí controlas cómo termina la demo: eliges manualmente el estado final de la recurrencia o dejas que se resuelva automáticamente, como pasaría con un pago real.'
                }
            },
            {
                element: '#cardNumber',
                popover: {
                    title: 'Datos de tarjeta en tu propia página',
                    description: 'Como esta tienda usa API Gateway, no hay redirección: los datos de tarjeta viajan directo en el request, junto con el mismo bloque "recurring" que se usa en Web Checkout.'
                }
            },
            {
                element: '#btnPagar',
                popover: {
                    title: 'Activar recurrencia',
                    description: 'Al presionar aquí se cobra el primer periodo de inmediato y PlacetoPay programa y ejecuta los cobros siguientes automáticamente, sin que tu backend tenga que intervenir.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourIaGateway() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourIaGateway);

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

    actualizarBotonTutorial();

    if (localStorage.getItem('tutorial') === 'pendiente') {
        iniciarTourIaGateway();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
