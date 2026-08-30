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
                element: '#preauthInfo',
                popover: {
                    title: 'Qué es la preautorización',
                    description: 'A diferencia de un cobro normal, aquí PlacetoPay reserva el monto en la tarjeta del cliente sin cobrarlo. El cargo real solo ocurre al check-out del hotel, y si se cancela antes, el monto se libera automáticamente.'
                }
            },
            {
                element: '#productsPanel',
                popover: {
                    title: 'Catálogo de habitaciones',
                    description: 'Este catálogo simula el inventario de un hotel: cada habitación con su precio por noche, tal como lo vería un huésped al reservar.'
                }
            },
            {
                element: '#checkIn',
                popover: {
                    title: 'Fechas de la reserva',
                    description: 'El total a preautorizar se calcula multiplicando el número de noches por el precio de la habitación elegida.'
                }
            },
            {
                element: '#btnReservar',
                popover: {
                    title: 'Reservar ahora',
                    description: 'Al presionar aquí, tu backend arma el request con "type: checkin" en vez de un pago normal, y redirige al cliente a apartar el monto como garantía en PlacetoPay Web Checkout.'
                }
            },
            {
                element: '#preauthNotice',
                popover: {
                    title: 'La tarjeta no se cobra todavía',
                    description: 'Este recordatorio deja claro al huésped que el monto queda reservado, no cobrado, hasta el check-out.',
                    onNextClick: () => {
                        localStorage.setItem('tutorial', 'pendiente');
                        driver.destroy();
                        setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 0);
                    }
                }
            }
        ]
    });

    function iniciarTourHotel() {
        localStorage.setItem('tutorial', 'activo');
        document.body.classList.add('tutorial-active');
        actualizarBotonTutorial();
        driver.drive();
    }

    iniciarTutorialButton.addEventListener('click', iniciarTourHotel);

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
        iniciarTourHotel();
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'tutorial') {
            actualizarBotonTutorial();
        }
    });
});
