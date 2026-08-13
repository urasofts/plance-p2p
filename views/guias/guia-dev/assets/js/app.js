import { state } from "./core/state.js";
import { createNavigation } from "./core/navigation.js";
import { initWelcome } from "./components/welcome/welcome.js";
import { initTopbar } from "./components/topbar/topbar.js";
import { initSidebar, onEpChange } from "./components/sidebar/sidebar.js";
import { initRequest, initEditor } from "./components/request/request.js";
import { initResponse } from "./components/response/response.js";
import { initPopup } from "./components/popup/popup.js";

let booted = false;

function boot() {
  if (booted) return;

  const appShell = document.getElementById("appShell");
  const welcome = document.getElementById("welcomeScreen");
  if (!appShell || !welcome) return; // templates aún no

  booted = true;
  console.log("[app] boot");

  const nav = createNavigation(state);

  const requestApi = initRequest(state) || {};

  document.addEventListener(
    "app:force-update",
    () => requestApi.updateAll?.(),
    { once: false },
  );

  const sidebarApi =
    initSidebar(state, {
      updateAll: () => requestApi.updateAll?.(),
    }) || {};

  initTopbar({
    onToggleSidebar: () => sidebarApi.toggleSidebar?.(),
  });

  initResponse(state, {
    updateSendAvailability: () => requestApi.updateSendAvailability?.(),
  });
  initPopup();
  initWelcome(state, { nav });

  nav.init();

  onEpChange({
    updateAll: () => requestApi.updateAll?.(),
  });

  requestApi.updateAll?.();
  setTimeout(() => initEditor(state), 100);

  document.getElementById("backToGuideBtn")?.addEventListener("click", () => {
    nav.goToWelcome();
  });
}

window.addEventListener("templates:ready", boot);
window.addEventListener("DOMContentLoaded", boot);
setTimeout(boot, 300); // fallback extra
