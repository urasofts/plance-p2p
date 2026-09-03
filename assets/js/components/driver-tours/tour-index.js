document.addEventListener('DOMContentLoaded', () => {
    const iniciarTutorialButton = document.getElementById(
        'navbar-iniciar-tutorial'
    );
    const tutorialQuickStartButton = document.querySelector(
        '.tutorial-trigger'
    );

    const cerrarTutorialButton = document.getElementById(
        'navbar-cerrar-tutorial'
    );

    const tutorialHelpButton = document.getElementById(
        'navbar-tutorial-help'
    );

    const tutorialHelpWrap = document.getElementById(
        'tutorial-help-wrap'
    );

    if (!iniciarTutorialButton || !cerrarTutorialButton || !window.driver?.js) {
        return;
    }

    function actualizarBotonTutorial() {
        const estadoTutorial = localStorage.getItem('tutorial') || 'inactivo';
        const tutorialIniciado =
            estadoTutorial === 'activo' || estadoTutorial === 'pendiente';

        iniciarTutorialButton.innerHTML = tutorialIniciado
            ? '<i class="bi bi-arrow-repeat"></i> Repetir tutorial'
            : '<i class="bi bi-question-circle"></i> Iniciar tutorial';

        iniciarTutorialButton.hidden = false;
        cerrarTutorialButton.hidden = !tutorialIniciado;

        if (tutorialHelpButton) {
            tutorialHelpButton.hidden = tutorialIniciado;
        }

        if (tutorialHelpWrap) {
            tutorialHelpWrap.style.display = tutorialIniciado ? 'none' : 'inline-flex';
        }

        cerrarTutorialButton.innerHTML =
            estadoTutorial === 'pendiente'
                ? '<i class="bi bi-check-circle"></i> Finalizar tutorial'
                : '<i class="bi bi-x-circle"></i> Cerrar tutorial';
    }

    const driver = window.driver.js.driver({
        allowClose: false,
        showProgress: true,
        disableActiveInteraction: false,
        advanceOnClick: true,
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
                element: '#sesiones',
                disableActiveInteraction: true,
                popover: {
                    title: 'Ejemplos de integraciones',
                    description:
                        'Podrás recorrer una compra como la que viviría un cliente: elegir un producto, iniciar el pago y observar qué ocurre detrás de cada paso hasta recibir la respuesta de Place to Pay.'
                }
            },
            {
                element: '#guia-user',
                disableActiveInteraction: true,
                popover: {
                    title: 'Guía de usuario',
                    description:
                        'Es un punto de partida para familiarizarte con el vocabulario y las decisiones habituales de un pago digital, incluso si todavía no sabes cómo se conectan los sistemas.'
                }
            },
            {
                element: '#guia-developer',
                disableActiveInteraction: true,
                popover: {
                    title: 'Guía developer',
                    description:
                        'Cuando quieras pasar de entender el flujo a construirlo, aquí verás la estructura técnica, los recursos necesarios y la forma de llevar la integración a tu propio comercio.'
                }
            },
            {
                element: '#navbar-tutorial-actions',
                disableActiveInteraction: false,
                popover: {
                    title: 'Barra tutorial',
                    description:
                        'Desde aquí puedes repetir el tutorial o cerrarlo cuando quieras. Hacer clic en "Finalizar tutorial" lo marcará como completado y no se volverá a mostrar.'
                }
            },
        ]
    });

    window.iniciarTourIndex = () => {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    };

    iniciarTutorialButton.addEventListener('click', iniciarTourIndex);

    if (tutorialQuickStartButton) {
        tutorialQuickStartButton.addEventListener('click', iniciarTourIndex);
    }

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
        window.iniciarTourIndex();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
