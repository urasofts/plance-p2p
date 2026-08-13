import { $, getSelectedOptionValue } from "./utils.js";

export function createNavigation(state) {
  function goToWelcome() {
    const welcome = $("#welcomeScreen");
    const app = $("#appShell");
    if (!welcome || !app) return;

    welcome.classList.remove("hidden");
    app.classList.add("hidden");
  }

  function goToApp() {
    const welcome = $("#welcomeScreen");
    const app = $("#appShell");
    if (!welcome || !app) return;

    welcome.classList.add("hidden");
    app.classList.remove("hidden");

    // refresco suave del editor si está en tab editar
    setTimeout(() => {
      if (state?.cmEditor && getSelectedOptionValue) {
        state.cmEditor.refresh?.();
      }
    }, 80);
  }

  function init() {
    // pantalla inicial
    goToWelcome();
  }

  return {
    init,
    goToWelcome,
    goToApp,
  };
}
