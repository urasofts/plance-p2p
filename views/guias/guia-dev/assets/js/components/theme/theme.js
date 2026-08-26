import { $$ } from "../../core/utils.js";

const STORAGE_KEY = "labTheme"; // "light" | "dark"

function getStoredTheme() {
  try {
    return localStorage.getItem(STORAGE_KEY);
  } catch {
    return null;
  }
}

function getPreferredTheme() {
  const stored = getStoredTheme();
  if (stored === "light" || stored === "dark") return stored;
  const prefersLight = window.matchMedia?.(
    "(prefers-color-scheme: light)",
  ).matches;
  return prefersLight ? "light" : "dark";
}

function applyTheme(theme) {
  document.documentElement.dataset.theme = theme;

  $$("[data-theme-icon]").forEach((icon) => {
    icon.className =
      theme === "light" ? "bi bi-moon-stars-fill" : "bi bi-sun-fill";
  });

  $$("[data-theme-toggle]").forEach((btn) => {
    btn.title =
      theme === "light" ? "Cambiar a modo oscuro" : "Cambiar a modo claro";
  });
}

function toggleTheme() {
  const current = document.documentElement.dataset.theme === "light"
    ? "light"
    : "dark";
  const next = current === "light" ? "dark" : "light";
  applyTheme(next);
  try {
    localStorage.setItem(STORAGE_KEY, next);
  } catch {
    // localStorage no disponible (modo privado, etc.) — el tema no persiste
  }
}

export function initTheme() {
  applyTheme(getPreferredTheme());

  $$("[data-theme-toggle]").forEach((btn) => {
    btn.addEventListener("click", toggleTheme);
  });
}
