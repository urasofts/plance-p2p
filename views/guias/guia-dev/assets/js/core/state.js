export const state = {
  // Tarjeta seleccionada para simulación
  card: "4111111111111111",

  // Tab actual del editor JSON: preview | raw | edit
  currentTab: "preview",

  // Último body generado/editado
  lastBody: {},

  // Instancia CodeMirror
  cmEditor: null,
};
