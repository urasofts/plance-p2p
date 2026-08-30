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
                element: '#prea-titulo',
                popover: {
                    title: 'Historial de Preautorizaciones',
                    description: 'Aquí ves las reservas de habitación donde se apartó un monto en la tarjeta del cliente sin cobrarlo todavía.'
                }
            },
            {
                element: '#prea-info',
                popover: {
                    title: '¿Qué es una preautorización?',
                    description: 'El dinero queda reservado en la tarjeta, pero el cargo real solo ocurre cuando el cliente hace check-out en el hotel. Es distinto a un pago normal, que se cobra de inmediato.'
                }
            },
            {
                element: '#prea-tabla',
                popover: {
                    title: 'Reservas registradas',
                    description: 'Cada fila es una habitación reservada: el monto apartado, la moneda y la fecha de la reserva.'
                }
            },
            {
                element: '#prea-th-estado',
                popover: {
                    title: 'Estado de la reserva',
                    description: 'Pendiente significa que PlacetoPay aún no confirma la preautorización. Aprobada indica que el monto ya quedó reservado correctamente en la tarjeta.'
                }
            },
            {
                element: '#prea-th-accion',
                popover: {
                    title: 'Verificar el estado',
                    description: 'Si una reserva quedó pendiente, usa "Verificar" para consultar con PlacetoPay si la preautorización ya se confirmó.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                    }
                }
            }
        ].filter(step => document.querySelector(step.element))
    });

    function iniciarTourRegPrea() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourRegPrea);

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
        iniciarTourRegPrea();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
