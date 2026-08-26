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
                element: '#tipo-flujo',
                popover: {
                    title: 'Elige cómo pagar en el ejemplo',
                    description: 'Aquí puedes cambiar entre Web Checkout y API Gateway. Piénsalo como dos caminos para que un cliente pague en tu comercio: uno más guiado y otro más integrado con tu sistema.'
                }
            },
            {
                element: '#tipo-suscripcion-mixta',
                popover: {
                    title: 'Pago inicial más suscripción',
                    description: 'Esta etiqueta indica un flujo donde el cliente hace un primer pago para activar el servicio y luego queda con la suscripción asociada.'
                }
            },
            {
                element: '#tipo-suscripcion-recurrencia',
                popover: {
                    title: 'Cobros recurrentes',
                    description: 'En este tipo de ejemplo, después del alta inicial, los cobros se realizan automáticamente según la frecuencia definida por el comercio.'
                }
            },
            {
                element: '#tarjeta',
                popover: {
                    title: 'Selecciona un plan para continuar',
                    description: 'Estas tarjetas simulan lo que vería un cliente final. Al hacer clic en Productos se abre el flujo de compra para iniciar la suscripción en PlacetoPay.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourSuscripciones() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourSuscripciones);

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
        iniciarTourSuscripciones();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
