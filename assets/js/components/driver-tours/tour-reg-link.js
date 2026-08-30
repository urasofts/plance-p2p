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
                element: '#link-titulo',
                popover: {
                    title: 'Historial de Links de Pago',
                    description: 'Aquí ves los links de pago que has generado con la API de PlacetoPay para que un cliente pague sin pasar por el flujo normal del comercio.'
                }
            },
            {
                element: '#link-tabla',
                popover: {
                    title: 'Links generados',
                    description: 'Cada fila es un link: el producto, el precio, la referencia y cuántas veces se ha usado para pagar.'
                }
            },
            {
                element: '#link-th-estado',
                popover: {
                    title: 'Estado del link',
                    description: 'Activo significa que el link sigue disponible para pagar. Expirado indica que se venció el tiempo límite, y Usado que ya se completó el pago.'
                }
            },
            {
                element: '#link-th-expira',
                popover: {
                    title: 'Fecha de expiración',
                    description: 'Cada link tiene un tiempo límite de uso. Pasada esta fecha, el link deja de estar disponible aunque nadie lo haya usado.'
                }
            },
            {
                element: '#link-th-accion',
                popover: {
                    title: 'Abrir o copiar el link',
                    description: 'Mientras el link esté activo puedes abrirlo directamente o copiarlo para compartirlo con tu cliente por el canal que prefieras.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                    }
                }
            }
        ].filter(step => document.querySelector(step.element))
    });

    function iniciarTourRegLink() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourRegLink);

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
        iniciarTourRegLink();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
