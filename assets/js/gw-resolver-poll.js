// Poll de resolución diferida para pantallas de resultado "pendiente" de
// API Gateway. Consulta php/gw_resolver.php cada pocos segundos; cuando la
// transacción ya se resolvió (aprobada/rechazada), actualiza la tarjeta de
// resultado en vivo, sin recargar la página.
(function () {
    const card = document.getElementById('resultCard');
    if (!card || card.dataset.pending !== '1') return;

    const tipo = card.dataset.tipo;
    const id   = card.dataset.id;
    if (!tipo || !id) return;

    const copy = {
        orden: {
            aprobada:  { icon: '✅', title: '¡Pago aprobado!', message: 'Tu compra fue procesada exitosamente. ¡Disfruta tu Cash!', color: '#3ecf8e', bg: 'rgba(62,207,142,0.15)', rgb: '62, 207, 142' },
            rechazada: { icon: '❌', title: 'Pago rechazado', message: 'Tu pago no pudo ser procesado. Verifica los datos e intenta de nuevo.', color: '#e05252', bg: 'rgba(224,82,82,0.15)', rgb: '224, 82, 82' },
        },
        suscripcion: {
            aprobada:  { icon: '🔐', title: '¡Suscripción registrada!', message: 'Tu tarjeta fue tokenizada y la suscripción quedó activa correctamente.', color: '#3ecf8e', bg: 'rgba(62,207,142,0.15)', rgb: '62, 207, 142' },
            rechazada: { icon: '❌', title: 'Proceso rechazado', message: 'No se pudo procesar. Verifica los datos e intenta de nuevo.', color: '#e05252', bg: 'rgba(224,82,82,0.15)', rgb: '224, 82, 82' },
        },
        recurrencia: {
            aprobada:  { icon: '✅', title: '¡Recurrencia activada!', message: 'Se cobró el primer periodo y PlacetoPay programó los cobros siguientes automáticamente.', color: '#3ecf8e', bg: 'rgba(62,207,142,0.15)', rgb: '62, 207, 142' },
            rechazada: { icon: '❌', title: 'Recurrencia rechazada', message: 'No se pudo procesar el primer cobro. Verifica los datos e intenta de nuevo.', color: '#e05252', bg: 'rgba(224,82,82,0.15)', rgb: '224, 82, 82' },
        },
    };

    function aplicarResolucion(estado) {
        const c = (copy[tipo] || {})[estado];
        if (!c) return;

        document.documentElement.style.setProperty('--ret-color', c.color);
        document.documentElement.style.setProperty('--ret-bg-icon', c.bg);
        document.documentElement.style.setProperty('--ret-color-rgb', c.rgb);

        const iconEl = card.querySelector('.result-icon');
        if (iconEl) iconEl.textContent = c.icon;

        const titleEl = card.querySelector('.result-title');
        if (titleEl) titleEl.textContent = c.title;

        const msgEl = card.querySelector('.result-message');
        if (msgEl) msgEl.textContent = c.message;

        const badgeEl = card.querySelector('.estado-badge');
        if (badgeEl) badgeEl.textContent = estado.toUpperCase();
    }

    function poll() {
        fetch('../php/gw_resolver.php?tipo=' + encodeURIComponent(tipo) + '&id=' + encodeURIComponent(id))
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (!data || data.estado === 'pendiente') return;
                aplicarResolucion(data.estado);
                clearInterval(intervalo);
            })
            .catch(function () { /* red caída: seguimos intentando en el próximo tick */ });
    }

    const intervalo = setInterval(poll, 4000);
    poll();
})();
