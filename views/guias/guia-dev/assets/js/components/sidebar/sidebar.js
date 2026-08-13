import {
  $,
  $$,
  getSelectedOptionValue,
  selectSingleOption,
} from "../../core/utils.js";
import { URLS } from "../../core/constants.js";

export function initSidebar(state, actions = {}) {
  const api = {
    toggleSidebar,
    toggleSection,
    onEpChange: () => onEpChange(actions),
  };

  bindSidebarEvents(state, actions, api);
  return api;
}

function bindSidebarEvents(state, actions, api) {
  // Colapsables
  $$("[data-section-toggle]").forEach((head) => {
    head.addEventListener("click", () => {
      const id = head.dataset.sectionToggle;
      if (id) api.toggleSection(id);
    });
  });

  // Opciones (servicio, tipo pago, sim)
  $$(
    'input[name="serviceOption"], input[name="paymentType"], input[name="simMode"]',
  ).forEach((input) => {
    input.addEventListener("change", () => {
      handleSelectOption(input, input.name, actions);
    });

    // al hacer click en el card no dispares doble comportamiento raro
    input.addEventListener("click", (e) => e.stopPropagation());

    // click en todo el card selecciona
    const optionCard = input.closest(".checkbox-option");
    optionCard?.addEventListener("click", () => {
      handleSelectOption(input, input.name, actions);
    });
  });

  // Tarjetas de prueba
  $$(".test-card[data-card-number]").forEach((card) => {
    card.addEventListener("click", () => {
      const num = card.dataset.cardNumber;
      pickCard(num, card, state, actions);
    });
  });

  // Inputs que regeneran request
  ["#fLogin", "#fSecret", "#fRef", "#fDesc", "#fAmount"].forEach((sel) => {
    const el = $(sel);
    el?.addEventListener("input", () => actions.updateAll?.());
  });

  $("#fCurrency")?.addEventListener("change", () => actions.updateAll?.());
}

export function toggleSidebar() {
  const labLayout = $("#labLayout");
  if (!labLayout) return;
  labLayout.classList.toggle("sidebar-hidden");
}

export function toggleSection(id) {
  const sec = document.getElementById(id);
  if (!sec) return;

  const body = sec.querySelector(".section-body");
  if (!body) return;

  if (sec.classList.contains("collapsed")) {
    sec.classList.remove("collapsed");
    body.style.maxHeight = body.scrollHeight + "px";
    setTimeout(() => {
      if (!sec.classList.contains("collapsed")) body.style.maxHeight = "none";
    }, 260);
  } else {
    body.style.maxHeight = body.scrollHeight + "px";
    requestAnimationFrame(() => {
      sec.classList.add("collapsed");
      body.style.maxHeight = "0";
    });
  }
}

function handleSelectOption(el, name, actions = {}) {
  selectSingleOption(el, name);

  if (name === "serviceOption") {
    onEpChange(actions);
  } else {
    actions.updateAll?.();
  }
}

function pickCard(num, el, state, actions = {}) {
  state.card = num;
  $$(".test-card").forEach((c) => c.classList.remove("selected"));
  el?.classList.add("selected");
  actions.updateAll?.();
}

export function onEpChange(actions = {}) {
  const ep = getSelectedOptionValue("serviceOption");

  const tipoPagoGroup = $("#tipoPagoGroup");
  const secTarjetas = $("#secTarjetas");
  const urlDisplay = $("#urlDisplay");

  if (tipoPagoGroup) tipoPagoGroup.classList.toggle("show", ep !== "link");
  if (secTarjetas)
    secTarjetas.style.display = ep === "gw_process" ? "block" : "none";
  if (urlDisplay)
    urlDisplay.textContent = URLS[ep] || "https://docs.placetopay.dev/";

  updateSimModeOptions();
  actions.updateAll?.();
}

export function updateSimModeOptions() {
  const ep = getSelectedOptionValue("serviceOption");

  const wcOptions = new Set([
    "auto",
    "e100",
    "e101",
    "e102",
    "e103",
    "e104",
    "e105",
    "e106",
    "e107",
    "e200",
    "e10001",
  ]);
  const gwOptions = new Set([
    "auto",
    "ok",
    "pending",
    "e96",
    "e68",
    "R1",
    "R3",
    "e13",
    "e61",
    "XR",
    "XE",
    "XX",
    "eXA",
    "eNF",
    "e05",
    "eXH",
  ]);
  const linkOptions = new Set([
    "e101",
    "e102",
    "e103",
    "e104",
    "eX3",
    "eBR",
    "eNF",
  ]);

  let allowed = null;
  if (ep === "wc_session") allowed = wcOptions;
  else if (ep === "gw_process") allowed = gwOptions;
  else if (ep === "link") allowed = linkOptions;

  const options = $$("#simModeOptions .checkbox-option");
  const selected = getSelectedOptionValue("simMode");
  let firstAllowed = null;

  options.forEach((opt) => {
    const input = opt.querySelector('input[name="simMode"]');
    if (!input) return;

    const visible = !allowed || allowed.has(input.value);
    opt.style.display = visible ? "" : "none";
    if (visible && !firstAllowed) firstAllowed = input;
  });

  if (allowed && selected && !allowed.has(selected) && firstAllowed) {
    selectSingleOption(firstAllowed, "simMode");
  }
}
