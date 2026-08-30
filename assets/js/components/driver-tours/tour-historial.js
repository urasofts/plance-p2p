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
                element: '#historial-panel',
                popover: {
                    title: 'Tu centro de historiales',
                    description: 'Aquí encuentras el registro de todo lo que ha pasado por tu comercio: pagos, suscripciones, links y reversos. Cada tarjeta te lleva al detalle de un tipo de transacción.'
                }
            },
            {
                element: '#hist-card-pgb',
                popover: {
                    title: 'Pagos Básicos',
                    description: 'Recargas simples de juegos, incluyendo variantes de Web Checkout, API Gateway y pagos mixtos (con abono parcial).'
                }
            },
            {
                element: '#hist-card-rec',
                popover: {
                    title: 'Recurrentes',
                    description: 'Servicios que se renuevan automáticamente según una periodicidad, sin que el cliente tenga que volver a pagar manualmente cada vez.'
                }
            },
            {
                element: '#hist-card-link',
                popover: {
                    title: 'Links de Pago',
                    description: 'Links compartibles generados con la API de PlacetoPay para que un cliente pague sin pasar por el flujo normal del comercio.'
                }
            },
            {
                element: '#hist-card-reversos',
                popover: {
                    title: 'Reversos',
                    description: 'Desde aquí puedes ver tus transacciones aprobadas y solicitar el reverso del dinero cuando sea necesario.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                    }
                }
            }
        ]
    });

    function iniciarTourHistorial() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourHistorial);

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
        iniciarTourHistorial();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
