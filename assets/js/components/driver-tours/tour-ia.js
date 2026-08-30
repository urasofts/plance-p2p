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
                element: '#usuarioIdInput',
                popover: {
                    title: 'Correo del cliente',
                    description: 'PlacetoPay usa este correo para identificar al comprador y asociarlo a los cobros recurrentes que se programan tras el primer pago.'
                }
            },
            {
                element: '#periodSelector',
                popover: {
                    title: 'Mensual o anual',
                    description: 'Esta elección cambia la periodicidad del cobro recurrente que se envía a PlacetoPay: "M" con hasta 12 cobros mensuales, o "Y" con un único cobro anual.'
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
                element: '#recurringMsg',
                popover: {
                    title: 'Cobro automático programado',
                    description: 'Este aviso resume cómo queda configurada la recurrencia: PlacetoPay cobra el primer periodo ahora y programa los siguientes de forma automática, sin volver a redirigir al cliente.'
                }
            },
            {
                element: '#btnBuy',
                popover: {
                    title: 'Crear la sesión de pago',
                    description: 'Al presionar aquí, tu backend arma el request con el bloque "recurring" dentro de "payment" y redirige al cliente a pagar el primer periodo en PlacetoPay Web Checkout.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourIa() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourIa);

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
        iniciarTourIa();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
