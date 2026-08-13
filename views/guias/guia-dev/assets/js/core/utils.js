// Selectores
export const $ = (selector, scope = document) => scope.querySelector(selector);
export const $$ = (selector, scope = document) =>
  Array.from(scope.querySelectorAll(selector));

// JSON safe parse
export function safeJsonParse(text, fallback = null) {
  try {
    return JSON.parse(text);
  } catch {
    return fallback;
  }
}

// Valida JSON y ubica el error (línea/columna) cuando el motor lo reporta
// (V8 incluye "position N" en el mensaje; otros motores no, y se degrada
// mostrando solo el mensaje sin resaltar línea).
export function parseJsonDetailed(text) {
  try {
    return { valid: true, value: JSON.parse(text) };
  } catch (err) {
    const posMatch = /position (\d+)/.exec(err.message);
    let line = null;
    let column = null;
    if (posMatch) {
      const before = text.slice(0, Number(posMatch[1]));
      const lines = before.split("\n");
      line = lines.length;
      column = lines[lines.length - 1].length + 1;
    }
    return { valid: false, message: err.message, line, column };
  }
}

// Opción seleccionada por name (radio/checkbox tipo single-select)
export function getSelectedOptionValue(name) {
  const selected = document.querySelector(`input[name="${name}"]:checked`);
  return selected ? selected.value : null;
}

// Fuerza comportamiento single-select en un grupo checkbox/radio visual
export function selectSingleOption(el, name) {
  const group = document.querySelectorAll(`input[name="${name}"]`);
  group.forEach((input) => {
    const isCurrent = input === el;
    input.checked = isCurrent;
    const wrapper = input.closest(".checkbox-option");
    if (wrapper) wrapper.classList.toggle("checked", isCurrent);
  });
}

// Copiar al portapapeles
export async function copyToClipboard(text) {
  await navigator.clipboard.writeText(text);
}

// Fecha ISO helper
export function isoNow() {
  return new Date().toISOString();
}

// Espera simple (si luego quieres reemplazar setTimeout encadenados)
export function wait(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

// Colorizador JSON (mantiene tu formato visual actual)
export function colorize(obj, d = 0) {
  const s = "  ".repeat(d);
  const s2 = "  ".repeat(d + 1);

  if (Array.isArray(obj)) {
    if (!obj.length) return '<span class="jbr">[]</span>';
    return `<span class="jbr">[</span>\n${obj
      .map((v) => s2 + colorize(v, d + 1))
      .join('<span class="jbr">,</span>\n')}\n${s}<span class="jbr">]</span>`;
  }

  if (obj !== null && typeof obj === "object") {
    if (!Object.keys(obj).length) return '<span class="jbr">{}</span>';
    const rows = Object.entries(obj)
      .map(([k, v]) => {
        const isAuto = ["tranKey", "nonce", "seed"].includes(k);
        const val = isAuto
          ? `<span class="jau">"${typeof v === "string" ? v : ""}"</span>`
          : colorize(v, d + 1);
        return `${s2}<span class="jk">"${k}"</span><span class="jbr">: </span>${val}`;
      })
      .join('<span class="jbr">,</span>\n');

    return `<span class="jbr">{</span>\n${rows}\n${s}<span class="jbr">}</span>`;
  }

  if (typeof obj === "string") return `<span class="js">"${obj}"</span>`;
  if (typeof obj === "number") return `<span class="jn">${obj}</span>`;
  if (typeof obj === "boolean") return `<span class="jb">${obj}</span>`;
  if (obj === null) return `<span class="jb">null</span>`;
  return String(obj);
}
