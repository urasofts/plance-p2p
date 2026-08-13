export const URLS = {
  wc_session: "https://checkout-test.placetopay.com/api/session",
  gw_process: "https://api-test.placetopay.com/rest/gateway/process",
  link: "https://sites-test.placetopay.com/api/payment-link",
};

export const OPTION_INFO = {
  wc_session: {
    title: "Web Checkout — Crear sesión",
    text: "Genera una sesión de Web Checkout y devuelve una URL de proceso. Útil para iniciar el flujo de pago en el navegador del cliente.",
  },
  gw_process: {
    title: "API Gateway — Procesar pago",
    text: "Procesa un pago directamente mediante Gateway. Ideal para transacciones con tarjeta y pagos integrados desde backends.",
  },
  link: {
    title: "Link de Pagos — Generar link",
    text: "Crea un enlace de pago que puedes enviar al cliente. Perfecto para cobros rápidos sin formulario integrado.",
  },

  basico: {
    title: "Pago básico",
    text: "Realiza un pago inmediato sin suscripción ni tokenización. Es el modo estándar para cobros puntuales.",
  },
  suscripcion: {
    title: "Pago + Suscripción",
    text: "Cobras el pago y además generas una suscripción. Útil cuando quieres crear un cliente recurrente mientras aceptas el primer cobro.",
  },
  token: {
    title: "Suscripción + Token",
    text: "Tokeniza el método de pago sin realizar un cargo inicial. Sirve para almacenar la tarjeta y usarla en suscripciones futuras.",
  },
  partial: {
    title: "Pagos Parciales",
    text: "Una sesión de pago parcial permite que el usuario haga varias transacciones distintas hasta completar el total solicitado.",
  },
  preauth: {
    title: "Pago con Preautorización",
    text: "Una sesión de preautorización permite reservar fondos antes de realizar el cargo final.",
  },
  dispersion: {
    title: "Pago con Dispersión",
    text: "Una sesión de dispersión divide el pago total en diferentes destinos.",
  },
  recurrencia: {
    title: "Recurrencia",
    text: "Configura pagos periódicos automáticos. Ideal para planes mensuales o cobros recurrentes.",
  },

  auto: {
    title: "Automático",
    text: "El resultado del mock se decide según la tarjeta seleccionada. Usar para pruebas con comportamiento dinámico.",
  },
  ok: {
    title: "Aprobada (00)",
    text: "Fuerza una respuesta exitosa. Sirve para probar el flujo de pago cuando todo sale bien.",
  },
  pending: {
    title: "Pendiente (?-) ",
    text: "Fuerza un estado pendiente. Ideal para probar flujos asíncronos o pagos que requieren confirmación posterior.",
  },

  e100: {
    title: "100 · Auth mal formado",
    text: "Simula un error de autenticación mal formada. Útil para validar tu header auth.",
  },
  e101: {
    title: "101 · Login incorrecto",
    text: "Simula un login inválido. Verifica que el campo login sea el correcto.",
  },
  e102: {
    title: "102 · TranKey incorrecto",
    text: "Simula un tranKey inválido. Útil para comprobar el hash de autenticación.",
  },
  e103: {
    title: "103 · Seed expirado",
    text: "Simula un seed expirado. Buena prueba para revisar el control de tiempo en auth.",
  },
  e104: {
    title: "104 · Sitio inactivo",
    text: "Simula un comercio inactivo. Indica que el sitio no está habilitado en el sandbox.",
  },

  eBR: {
    title: "BR · Solicitud incorrecta",
    text: "Simula un payload mal formado o con datos inválidos.",
  },
  eX3: {
    title: "X3 · Error de validación",
    text: "Simula errores de validación de campos específicos.",
  },
  eXA: {
    title: "XA · Rechazo financiero",
    text: "Simula un rechazo de pago por fondos insuficientes u otra causa financiera.",
  },
  eNF: {
    title: "NF · No encontrado",
    text: "Simula un recurso no encontrado en la API.",
  },
  e05: {
    title: "05 · Error externo",
    text: "Simula un fallo en un servicio externo o pasarela adicional.",
  },
  eXH: {
    title: "XH · Error interno",
    text: "Simula un error interno del sistema. Útil para manejar fallos inesperados.",
  },
};

/* Catálogo de respuestas simuladas (PlacetoPay AutoPay codes) */
export const SIM = {
  ok: {
    http: "200 OK",
    status: "APPROVED",
    reason: "00",
    message: "Aprobada. La operación se procesó correctamente.",
    kind: "ok",
  },
  pending: {
    http: "200 Pending",
    status: "PENDING",
    reason: "?-",
    message: "Pendiente. La operación está en curso (flujo asíncrono).",
    kind: "pend",
  },

  e100: {
    http: "401 Unauthorized",
    status: "FAILED",
    reason: "100",
    message: "Authentication no proporcionado o encabezado mal formado.",
    kind: "err",
  },
  e101: {
    http: "401 Unauthorized",
    status: "FAILED",
    reason: "101",
    message: "El login proporcionado no existe o es incorrecto.",
    kind: "err",
  },
  e102: {
    http: "401 Unauthorized",
    status: "FAILED",
    reason: "102",
    message: "El tranKey es incorrecto o el hash no coincide.",
    kind: "err",
  },
  e103: {
    http: "401 Unauthorized",
    status: "FAILED",
    reason: "103",
    message: "El seed ha expirado (diferencia mayor a 5 minutos).",
    kind: "err",
  },
  e104: {
    http: "401 Unauthorized",
    status: "FAILED",
    reason: "104",
    message: "Sitio inactivo.",
    kind: "err",
  },
  e105: {
    http: "401 Unauthorized",
    status: "FAILED",
    reason: "105",
    message: "Sitio expirado.",
    kind: "err",
  },
  e106: {
    http: "401 Unauthorized",
    status: "FAILED",
    reason: "106",
    message: "Credenciales expiradas.",
    kind: "err",
  },
  e107: {
    http: "401 Unauthorized",
    status: "FAILED",
    reason: "107",
    message:
      "Mala definición del UsernameToken (No cumple con el encabezado WSSE).",
    kind: "err",
  },

  e200: {
    http: "200 OK",
    status: "OK",
    reason: "200",
    message: "Saltar el encabezado de autenticación SOAP.",
    kind: "ok",
  },
  e10001: {
    http: "500 Server Error",
    status: "FAILED",
    reason: "10001",
    message: "Contacte a Soporte.",
    kind: "err",
  },
  e96: {
    http: "500 Server Error",
    status: "FAILED",
    reason: "96",
    message: "Malfuncionamiento del sistema.",
    kind: "err",
  },
  e68: {
    http: "504 Gateway Timeout",
    status: "FAILED",
    reason: "68",
    message: "Respuesta tardía.",
    kind: "err",
  },

  R1: {
    http: "200 Declined",
    status: "REJECTED",
    reason: "R1",
    message: "Autorización revocada.",
    kind: "err",
  },
  R3: {
    http: "200 Declined",
    status: "REJECTED",
    reason: "R3",
    message: "Todas las autorizaciones revocadas.",
    kind: "err",
  },

  e13: {
    http: "400 Bad Request",
    status: "FAILED",
    reason: "13",
    message: "Monto inválido.",
    kind: "err",
  },
  e61: {
    http: "400 Bad Request",
    status: "FAILED",
    reason: "61",
    message: "Monto Máximo excedido.",
    kind: "err",
  },
  XR: {
    http: "400 Bad Request",
    status: "FAILED",
    reason: "XR",
    message: "Respuesta inválida.",
    kind: "err",
  },
  XE: {
    http: "400 Bad Request",
    status: "FAILED",
    reason: "XE",
    message: "Tipo de Tarjeta inválido.",
    kind: "err",
  },
  XX: {
    http: "400 Bad Request",
    status: "FAILED",
    reason: "XX",
    message: "Configuraciones inválidas.",
    kind: "err",
  },

  eBR: {
    http: "400 Bad Request",
    status: "FAILED",
    reason: "BR",
    message: "Solicitud incorrecta (Bad Request). Datos inválidos o faltantes.",
    kind: "err",
  },
  eX3: {
    http: "400 Bad Request",
    status: "REJECTED",
    reason: "X3",
    message: "Error de validación. Formato incorrecto en campos específicos.",
    kind: "err",
  },
  eXA: {
    http: "200 Declined",
    status: "REJECTED",
    reason: "XA",
    message: "Rechazo financiero. Fondos insuficientes o monto no autorizado.",
    kind: "err",
  },
  eNF: {
    http: "404 Not Found",
    status: "FAILED",
    reason: "NF",
    message: "No encontrado. El recurso (ID o referencia) no existe.",
    kind: "err",
  },
  e05: {
    http: "502 Bad Gateway",
    status: "FAILED",
    reason: "05",
    message: "Error externo. Fallo en la comunicación con la red financiera.",
    kind: "err",
  },
  eXH: {
    http: "500 Server Error",
    status: "FAILED",
    reason: "XH",
    message: "Error interno. Fallo inesperado en el sistema de AutoPay.",
    kind: "err",
  },
};
