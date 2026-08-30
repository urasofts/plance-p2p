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
                element: '#jugadorIdInput',
                popover: {
                    title: 'ID del jugador',
                    description: 'Identifica dentro del juego a quién se le acredita el Cash comprado, tal como pediría cualquier tienda de recargas.'
                }
            },
            {
                element: '#productsPanel',
                popover: {
                    title: 'Catálogo de Cash',
                    description: 'La moneda del juego se vende en paquetes con distintos descuentos. Este catálogo simula lo que vería tu cliente al elegir cuánto comprar.'
                }
            },
            {
                element: '#simModeWrap',
                popover: {
                    title: 'Modo de simulación',
                    description: 'Aquí controlas cómo termina la demo: eliges manualmente el estado final del pago o dejas que se resuelva automáticamente, como pasaría con una transacción real.'
                }
            },
            {
                element: '#paymentTabs',
                popover: {
                    title: 'Tarjeta o cuenta bancaria',
                    description: 'Esta tienda de API Gateway ofrece dos instrumentos de pago distintos: tarjeta y transferencia por cuenta, cada uno con su propio formulario.'
                }
            },
            {
                element: '#btnPagar',
                popover: {
                    title: 'Procesar el pago',
                    description: 'Al presionar aquí, los datos viajan directo al API Gateway de PlacetoPay, que responde de inmediato con el estado del pago — APPROVED, PENDING o REJECTED — sin redirección.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourPubg() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourPubg);

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
        iniciarTourPubg();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
