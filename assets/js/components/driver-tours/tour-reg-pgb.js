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
                element: '#pgb-titulo',
                popover: {
                    title: 'Historial de Pagos Básicos',
                    description: 'Aquí ves las recargas simples que han hecho tus clientes, sin suscripciones ni recurrencias de por medio.'
                }
            },
            {
                element: '#pgb-tabs',
                popover: {
                    title: 'Modos de pago',
                    description: 'Web Checkout y API Gateway son pagos completos de una sola vez. Pago Mixto y API Gateway Mixto son pedidos que el cliente puede pagar en varios abonos hasta completar el total.'
                }
            },
            {
                element: '#pgb-tabla',
                popover: {
                    title: 'Registros de pago',
                    description: 'Cada fila es una orden con su producto, cliente y precio. En los modos mixtos también verás el monto pagado y el saldo restante.'
                }
            },
            {
                element: '#pgb-th-estado',
                popover: {
                    title: 'Estado de la orden',
                    description: 'Pendiente indica que PlacetoPay aún no confirma el pago. Aprobada significa que el dinero ya se acreditó a tu comercio.'
                }
            },
            {
                element: '#pgb-th-accion',
                popover: {
                    title: 'Verificar o continuar pago',
                    description: 'Con "Verificar" consultas un pago pendiente. En los modos mixtos, si queda saldo por pagar, el cliente puede usar "Continuar pago" para completar el abono.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                    }
                }
            }
        ].filter(step => document.querySelector(step.element))
    });

    function iniciarTourRegPgb() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourRegPgb);

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
        iniciarTourRegPgb();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
