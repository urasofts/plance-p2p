<section id="g-errors" class="guide-section">
  <h1>Simular errores</h1>
  <p>
    Esta vista te ayuda a probar la robustez de tu integración frente a
    escenarios no exitosos: errores de autenticación, validación, rechazo
    financiero y fallos operativos.
  </p>

  <p>
    Simular errores desde etapas tempranas reduce incidentes en producción y te
    permite diseñar mejores mensajes al usuario, estrategias de reintento y
    rutas de contingencia.
  </p>

  <h3>¿Por qué es importante?</h3>
  <ul>
    <li>
      Evita que tu sistema dependa solo del flujo convencional y que ocurran
      errores inesperados en los cuales el cliente pueda perder información o
      dinero.
    </li>
    <li>Mejora la experiencia del usuario ante fallos reales.</li>
    <li>
      Facilita soporte técnico al tener respuestas esperadas documentadas.
    </li>
  </ul>

  <h3>Qué podrás validar en esta vista</h3>
  <ul>
    <li>Códigos de error de autenticación y estructura de credenciales.</li>
    <li>Errores de validación de datos y consistencia del payload (del sistema placetopay).</li>
    <li>Comportamiento de tu aplicación ante respuestas no aprobadas.</li>
  </ul>

  <h3>Flujo resumido</h3>
  <ol>
    <li>Seleccionas un modo de simulación de error.</li>
    <li>Envías el request con el escenario elegido.</li>
    <li>Inspeccionas código, mensaje y detalle de respuesta.</li>
    <li>Ajustas validaciones, mensajes y manejo de excepciones.</li>
  </ol>

  <p>
    Selecciona una de las siguientes opciones de servicio para ver su catálogo
    de errores más comunes.
  </p>

  <div class="guide-home-cards">
    <article class="home-card" data-target="g-errors-wc">
      <div class="home-card-icon"><i class="bi bi-window-stack"></i></div>
      <h3>Web Checkout — Crear sesión</h3>
      <p>Errores que se pueden presentar en Web Checkout al crear sesión.</p>
    </article>

    <article class="home-card" data-target="g-errors-gw">
      <div class="home-card-icon">
        <i class="bi bi-credit-card-2-front"></i>
      </div>
      <h3>API Gateway — Procesar pago</h3>
      <p>Errores que se pueden presentar en API Gateway al procesar pago.</p>
    </article>

    <article class="home-card" data-target="g-errors-link">
      <div class="home-card-icon"><i class="bi bi-link-45deg"></i></div>
      <h3>Link de Pagos — Generar link</h3>
      <p>Errores que se pueden presentar al generar links de pago.</p>
    </article>
  </div>
</section>

<section id="g-errors-wc" class="guide-section">
  <h1>Errores · Web Checkout — Crear sesión</h1>
  <p>
    Selecciona un escenario para abrir el entorno con el estado/error
    preconfigurado.
  </p>

  <div class="guide-home-cards session-cards">
    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e100"
    >
      <h3>Error 100 · UsernameToken no proporcionado</h3>
      <p>Error de autenticación: falta token en la solicitud.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e100"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e101"
    >
      <h3>Error 101 · Identificador de sitio no existe</h3>
      <p>El login/site configurado no corresponde a un sitio válido.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e101"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e102"
    >
      <h3>Error 102 · El hash de TranKey no coincide</h3>
      <p>Credenciales inválidas o firma generada incorrectamente.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e102"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e103"
    >
      <h3>Error 103 · Fecha de la semilla mayor de 5 minutos</h3>
      <p>Desfase de tiempo entre cliente y servidor.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e103"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e104"
    >
      <h3>Error 104 · Sitio inactivo</h3>
      <p>La cuenta/sitio no está habilitada para operar.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e104"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e105"
    >
      <h3>Error 105 · Sitio expirado</h3>
      <p>La vigencia del sitio/cuenta ha expirado.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e105"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e106"
    >
      <h3>Error 106 · Credenciales expiradas</h3>
      <p>Las credenciales configuradas ya no están vigentes.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e106"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e107"
    >
      <h3>Error 107 · UsernameToken mal definido</h3>
      <p>Formato o estructura inválida en el token de autenticación.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e107"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e200"
    >
      <h3>Error 200 · Saltar encabezado SOAP</h3>
      <p>Solicitud enviada sin encabezado SOAP requerido.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e200"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="wc_session"
      data-sim="e10001"
    >
      <h3>Error 10001 · Contacte a Soporte</h3>
      <p>Error no controlado que requiere revisión de soporte.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="wc_session"
        data-sim="e10001"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>
  </div>
</section>

<section id="g-errors-gw" class="guide-section">
  <h1>Errores · API Gateway — Procesar pago</h1>
  <p>
    Selecciona un escenario para abrir el entorno con el estado/error
    preconfigurado.
  </p>

  <div class="guide-home-cards session-cards">
    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="ok"
    >
      <h3>Aprobada (00)</h3>
      <p>Transacción aprobada correctamente.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="ok"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="pending"
    >
      <h3>Pendiente (?-)</h3>
      <p>La transacción queda en estado pendiente.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="pending"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="e96"
    >
      <h3>Error 96 · Malfuncionamiento del sistema</h3>
      <p>Falla general del sistema/procesador.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="e96"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="e68"
    >
      <h3>Error 68 · Respuesta tardía</h3>
      <p>Timeout o demora excesiva en la respuesta.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="e68"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="R1"
    >
      <h3>Error R1 · Autorización revocada</h3>
      <p>La autorización fue revocada por el emisor.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="R1"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="R3"
    >
      <h3>Error R3 · Todas las autorizaciones revocadas</h3>
      <p>Se revocaron todas las autorizaciones asociadas.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="R3"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="e13"
    >
      <h3>Error 13 · Monto inválido</h3>
      <p>Valor inválido o fuera de formato.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="e13"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="e61"
    >
      <h3>Error 61 · Monto máximo excedido</h3>
      <p>La transacción supera límites permitidos.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="e61"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="XR"
    >
      <h3>Error XR · Respuesta inválida</h3>
      <p>Respuesta no interpretable o inconsistente.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="XR"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="XE"
    >
      <h3>Error XE · Tipo de tarjeta inválido</h3>
      <p>Franquicia/tipo de tarjeta no aceptado.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="XE"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="XX"
    >
      <h3>Error XX · Configuraciones inválidas</h3>
      <p>Parámetros/configuración no válidos para la operación.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="XX"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="eXA"
    >
      <h3>Error XA · Rechazo financiero</h3>
      <p>La entidad financiera rechaza la operación.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="eXA"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="eNF"
    >
      <h3>Error NF · No encontrado</h3>
      <p>No se encontró el recurso o referencia indicada.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="eNF"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="e05"
    >
      <h3>Error 05 · Error externo</h3>
      <p>Fallo externo del procesador/tercero.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="e05"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="gw_process"
      data-sim="eXH"
    >
      <h3>Error XH · Error interno</h3>
      <p>Error interno del sistema durante el procesamiento.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="gw_process"
        data-sim="eXH"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>
  </div>
</section>

<section id="g-errors-link" class="guide-section">
  <h1>Errores · Link de Pagos — Generar link</h1>
  <p>
    Selecciona un escenario para abrir el entorno con el estado/error
    preconfigurado.
  </p>

  <div class="guide-home-cards session-cards">
    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="link"
      data-sim="e101"
    >
      <h3>Error 101 · Identificador de sitio no existe</h3>
      <p>El login/site configurado no corresponde a un sitio válido.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="link"
        data-sim="e101"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="link"
      data-sim="e102"
    >
      <h3>Error 102 · El hash de TranKey no coincide</h3>
      <p>Credenciales inválidas o firma generada incorrectamente.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="link"
        data-sim="e102"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="link"
      data-sim="e103"
    >
      <h3>Error 103 · Fecha de la semilla mayor de 5 minutos</h3>
      <p>Desfase de tiempo entre cliente y servidor.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="link"
        data-sim="e103"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="link"
      data-sim="e104"
    >
      <h3>Error 104 · Sitio inactivo</h3>
      <p>La cuenta/sitio no está habilitada para operar.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="link"
        data-sim="e104"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="link"
      data-sim="eBR"
    >
      <h3>Error BR · Solicitud incorrecta</h3>
      <p>La solicitud no cumple requisitos del endpoint.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="link"
        data-sim="eBR"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="link"
      data-sim="eX3"
    >
      <h3>Error X3 · Error de validación</h3>
      <p>El payload tiene campos inválidos o faltantes.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="link"
        data-sim="eX3"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>

    <article
      class="home-card flow-card"
      data-flow="errors"
      data-service="link"
      data-sim="eNF"
    >
      <h3>Error NF · No encontrado</h3>
      <p>Recurso o referencia inexistente.</p>
      <button
        class="card-go-btn"
        data-flow="errors"
        data-service="link"
        data-sim="eNF"
      >
        <i class="bi bi-rocket-takeoff"></i> Probar en Explorer
      </button>
    </article>
  </div>
</section>
