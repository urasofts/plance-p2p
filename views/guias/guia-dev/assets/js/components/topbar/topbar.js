import { $ } from "../../core/utils.js";

export function initTopbar(actions = {}) {
  const sidebarToggle = $("#sidebarToggle");

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", (event) => {
      event.preventDefault();
      if (typeof actions.onToggleSidebar === "function") {
        actions.onToggleSidebar();
      }
    });
  }

  return {
    toggleSidebar: () => {
      if (typeof actions.onToggleSidebar === "function") {
        actions.onToggleSidebar();
      }
    },
  };
}
