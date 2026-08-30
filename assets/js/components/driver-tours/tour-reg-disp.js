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
                element: '#disp-titulo',
                popover: {
                    title: 'Historial de Dispersión de Pago',
                    description: 'Aquí ves los tiquetes vendidos donde el pago se dividió automáticamente entre distintos beneficiarios.'
                }
            },
            {
                element: '#disp-info',
                popover: {
                    title: '¿Qué es una dispersión?',
                    description: 'En vez de que todo el dinero vaya a un solo destino, PlacetoPay reparte el monto total entre la aerolínea y los impuestos aeroportuarios en una misma transacción.'
                }
            },
            {
                element: '#disp-th-desglose',
                popover: {
                    title: 'Desglose del pago',
                    description: 'Estas columnas muestran cuánto correspondió a la aerolínea y cuánto a impuestos, dentro del mismo tiquete.'
                }
            },
            {
                element: '#disp-th-estado',
                popover: {
                    title: 'Estado del tiquete',
                    description: 'Pendiente significa que la dispersión aún no se confirma. Aprobada indica que ambos beneficiarios ya recibieron su parte correspondiente.'
                }
            },
            {
                element: '#disp-th-accion',
                popover: {
                    title: 'Verificar el pago',
                    description: 'Si un tiquete quedó pendiente, usa "Verificar" para consultar con PlacetoPay si la dispersión ya se completó.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                    }
                }
            }
        ].filter(step => document.querySelector(step.element))
    });

    function iniciarTourRegDisp() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourRegDisp);

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
        iniciarTourRegDisp();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
