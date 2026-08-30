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
                element: '#productsPanel',
                popover: {
                    title: 'Gemas y pases de batalla',
                    description: 'Este catálogo combina la moneda del juego con pases de batalla. Tu cliente puede comprar uno o combinarlos en un mismo carrito.'
                }
            },
            {
                element: '#multiCheck',
                popover: {
                    title: 'Selección múltiple',
                    description: 'Al activar esta casilla, el cliente puede agregar varios productos al carrito y pagarlos juntos en una sola transacción.'
                }
            },
            {
                element: '#pagoMixtoBadge',
                popover: {
                    title: 'Qué es el Pago Mixto aquí',
                    description: 'Esta tienda crea la sesión con "allowPartial: true": en la propia pasarela de PlacetoPay, el comprador decide si paga el total de una vez o solo una parte, dejando el resto pendiente.'
                }
            },
            {
                element: '#jugadorId',
                popover: {
                    title: 'ID del jugador',
                    description: 'Identifica dentro del juego a quién se le acreditan las Gemas o el pase comprado.'
                }
            },
            {
                element: '#btnPagar',
                popover: {
                    title: 'Crear la sesión de pago',
                    description: 'Al presionar aquí, tu backend arma el request hacia PlacetoPay Web Checkout con el carrito completo y redirige al cliente a pagar — con la opción de pago parcial ya habilitada.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourRainbowsix() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourRainbowsix);

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
        iniciarTourRainbowsix();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
