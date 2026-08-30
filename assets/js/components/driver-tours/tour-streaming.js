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
                    description: 'Este correo identifica al comprador durante la sesión de pago. Como es una suscripción, PlacetoPay lo usa para tokenizar la tarjeta a nombre de este cliente.'
                }
            },
            {
                element: '#productsPanel',
                popover: {
                    title: 'Catálogo de plataformas',
                    description: 'Aquí simulamos el catálogo que vería tu cliente: distintas plataformas de streaming con sus planes y precios, igual que en una tienda real.'
                }
            },
            {
                element: '#cardRequiredNotice',
                popover: {
                    title: 'Por qué solo se acepta tarjeta',
                    description: 'Al ser una suscripción, el pago se crea con "subscribe: true" para poder guardar el medio de pago y renovarlo automáticamente. PSE no permite guardarse, por eso este flujo solo ofrece tarjeta.'
                }
            },
            {
                element: '#btnBuy',
                popover: {
                    title: 'Crear la sesión de pago',
                    description: 'Al presionar este botón, tu backend arma el request hacia PlacetoPay Web Checkout y redirige al cliente a pagar el primer periodo mientras se guarda su tarjeta para el siguiente.'
                }
            },
            {
                element: '#checkoutPanel',
                popover: {
                    title: 'Resumen de la compra',
                    description: 'Este panel resume lo que el cliente va a pagar en tiempo real, según el plan que seleccione en el catálogo.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourStreaming() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourStreaming);

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
        iniciarTourStreaming();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
