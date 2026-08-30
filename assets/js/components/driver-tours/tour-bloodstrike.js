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
                    description: 'Identifica dentro del juego a quién se le acreditan las Esmeraldas compradas, igual que en cualquier tienda de recargas.'
                }
            },
            {
                element: '#productsPanel',
                popover: {
                    title: 'Catálogo de Esmeraldas',
                    description: 'La moneda del juego se vende en distintos paquetes. Aquí tu cliente elige cuánto quiere comprar, igual que en una tienda de recargas real.'
                }
            },
            {
                element: '#pagoMixtoBadge',
                popover: {
                    title: 'Qué es el Pago Mixto',
                    description: 'Esta tienda permite completar una compra con varios pagos parciales: el cliente puede abonar hoy una parte y el resto después, incluso con un medio de pago distinto, hasta cubrir el total.'
                }
            },
            {
                element: '#montoPagarGroup',
                popover: {
                    title: 'Cuánto pagar en este intento',
                    description: 'Aquí el cliente decide cuánto abonar ahora. Lo que quede pendiente se guarda como saldo y se puede completar más adelante volviendo a esta misma tienda.'
                }
            },
            {
                element: '#btnPagar',
                popover: {
                    title: 'Procesar el pago',
                    description: 'Al presionar aquí, los datos viajan directo al API Gateway de PlacetoPay, que responde de inmediato con el estado del abono — sin redirección, por eso requiere certificación PCI-DSS en producción.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourBloodstrike() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourBloodstrike);

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
        iniciarTourBloodstrike();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
