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
                element: '#sus-titulo',
                popover: {
                    title: 'Historial de Suscripciones',
                    description: 'Aquí revisas todas las suscripciones de tus clientes, sin importar si vinieron por Web Checkout o por API Gateway.'
                }
            },
            {
                element: '#sus-tabs',
                popover: {
                    title: 'Modos de suscripción',
                    description: 'Cada pestaña es una variante distinta del flujo: pago con suscripción, recurrencia pura o suscripción pura, tanto en Web Checkout como en API Gateway. Cambia de pestaña para ver la tabla de cada modo.'
                }
            },
            {
                element: '#sus-tabla',
                popover: {
                    title: 'Registros de suscripción',
                    description: 'Cada fila muestra el plan contratado, el cliente y el precio. Las columnas cambian un poco según el modo seleccionado, pero la lógica es la misma.'
                }
            },
            {
                element: '#sus-th-estado',
                popover: {
                    title: 'Estado de la suscripción',
                    description: 'Pendiente indica que PlacetoPay aún no confirma el pago. Aprobada significa que la suscripción quedó activa y lista para renovarse.'
                }
            },
            {
                element: '#sus-th-accion',
                popover: {
                    title: 'Verificar o cancelar',
                    description: 'Con "Verificar" consultas el estado real de un pago pendiente. Cuando la suscripción ya está aprobada, algunos modos permiten cancelarla desde aquí.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                    }
                }
            }
        ].filter(step => document.querySelector(step.element))
    });

    function iniciarTourRegSus() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourRegSus);

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
        iniciarTourRegSus();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
