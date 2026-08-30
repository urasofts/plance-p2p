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
                element: '#securityWarning',
                popover: {
                    title: 'Aviso para comercios que integran API Gateway',
                    description: 'A diferencia de Web Checkout, aquí los datos de tarjeta pasan por tu propia página. Eso implica certificación PCI-DSS en producción — este aviso resume esa responsabilidad.'
                }
            },
            {
                element: '#productsPanel',
                popover: {
                    title: 'Catálogo de plataformas',
                    description: 'El catálogo de streamings que vería tu cliente. Al elegir un plan, el resumen de la derecha se actualiza para reflejar el cobro que se va a tokenizar.'
                }
            },
            {
                element: '#simModeWrap',
                popover: {
                    title: 'Modo de simulación',
                    description: 'Aquí controlas cómo termina la demo: eliges manualmente el estado final del pago o dejas que se resuelva automáticamente, como pasaría con una transacción real.'
                }
            },
            {
                element: '#cardNumber',
                popover: {
                    title: 'Datos de tarjeta en tu propia página',
                    description: 'Como esta tienda usa API Gateway, no hay redirección: los datos de la tarjeta viajan directo en el request. El cuerpo usa "subscription" en vez de "payment", así que este primer envío solo tokeniza, no cobra.'
                }
            },
            {
                element: '#btnPagar',
                popover: {
                    title: 'Suscribirse ahora',
                    description: 'Al presionar aquí se envía el request a PlacetoPay Gateway, que responde de inmediato con el resultado de la tokenización — sin processUrl ni redirección.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourStreamingGateway() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourStreamingGateway);

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
        iniciarTourStreamingGateway();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
