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
                element: '#detrev-info-trx',
                popover: {
                    title: 'Información de la transacción',
                    description: 'Aquí ves los datos completos del pago que puede reversarse: tipo, producto o servicio, fecha y el monto total que se cobró al cliente.'
                }
            },
            {
                element: '#detrev-info-pagador',
                popover: {
                    title: 'Información del pagador',
                    description: 'Identifica quién hizo el pago y el Request ID que PlacetoPay asignó a esa transacción, útil para dar seguimiento con soporte si es necesario.'
                }
            },
            {
                element: '#detrev-opciones',
                popover: {
                    title: 'Acciones disponibles',
                    description: 'Desde este menú puedes imprimir el comprobante, generar la carta formal de reverso o iniciar el reverso de la transacción hacia el cliente.'
                }
            },
            {
                element: '#detrev-aviso',
                popover: {
                    title: 'Importante antes de reversar',
                    description: 'El reverso solo está disponible antes de la hora de corte del día. Una vez confirmado, el dinero se devuelve automáticamente al cliente y no se puede deshacer.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                    }
                }
            }
        ]
    });

    function iniciarTourDetalleReverso() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourDetalleReverso);

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
        iniciarTourDetalleReverso();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
