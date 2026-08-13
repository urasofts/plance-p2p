import { $, $$ } from "../../core/utils.js";

export function initWelcome(state, { nav } = {}) {
  const root = $("#welcomeScreen");
  if (!root) return;

  // Botón principal
  $("#goExplorerBtn")?.addEventListener("click", () => nav?.goToApp?.());

  // Delegación: captura clicks en links, cards y botones internos
  root.addEventListener("click", (e) => {
    const guideLink = e.target.closest(".guide-link[data-target]");
    if (guideLink) {
      activateGuideSection(guideLink.dataset.target);
      return;
    }

    const homeCard = e.target.closest(".home-card[data-target]");
    if (homeCard) {
      activateGuideSection(homeCard.dataset.target);
      return;
    }

    const goBtn = e.target.closest(".card-go-btn");
    if (goBtn) {
      e.preventDefault();
      e.stopPropagation();

      const flow = goBtn.dataset.flow || null;
      const payment = goBtn.dataset.payment || null;
      const service = goBtn.dataset.service || null;
      const sim = goBtn.dataset.sim || null;

      applyPreset(state, { flow, payment, service, sim });
      nav?.goToApp?.();
      return;
    }

    // Si hacen click en toda la card flow-card (no solo botón), también navega
    const flowCard = e.target.closest(".flow-card");
    if (flowCard) {
      const flow = flowCard.dataset.flow || null;
      const payment = flowCard.dataset.payment || null;
      const service = flowCard.dataset.service || null;
      const sim = flowCard.dataset.sim || null;

      applyPreset(state, { flow, payment, service, sim });
      nav?.goToApp?.();
    }
  });

  activateGuideSection("g-home");
}

function activateGuideSection(targetId) {
  if (!targetId) return;

  $$("#welcomeScreen .guide-section").forEach((sec) => {
    sec.classList.toggle("active", sec.id === targetId);
  });

  $$("#welcomeScreen .guide-link[data-target]").forEach((btn) => {
    btn.classList.toggle("active", btn.dataset.target === targetId);
  });
}

function setSingleCheckbox(name, value) {
  const inputs = document.querySelectorAll(`input[name="${name}"]`);
  inputs.forEach((i) => {
    const checked = i.value === value;
    i.checked = checked;
    i.closest(".checkbox-option")?.classList.toggle("checked", checked);
  });
}

function applyPreset(state, { flow, payment, service, sim }) {
  let resolvedService = service;
  if (!resolvedService) {
    if (flow === "session") resolvedService = "wc_session";
    else if (flow === "process") resolvedService = "gw_process";
    else if (flow === "links") resolvedService = "link";
  }

  if (resolvedService) setSingleCheckbox("serviceOption", resolvedService);
  if (payment) setSingleCheckbox("paymentType", payment);
  if (sim) setSingleCheckbox("simMode", sim);

  state.card = "4111111111111111";

  const selectedService = resolvedService
    ? document.querySelector(
        `input[name="serviceOption"][value="${resolvedService}"]`,
      )
    : null;

  if (selectedService) {
    selectedService.dispatchEvent(new Event("change", { bubbles: true }));
  } else {
    document.dispatchEvent(new CustomEvent("app:force-update"));
  }
}
