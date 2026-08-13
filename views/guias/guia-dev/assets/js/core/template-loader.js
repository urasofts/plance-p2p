const templateBase = `${window.MOCKUP_DEV_BASE || "assets"}/templates`;

async function loadTemplate(path) {
  const res = await fetch(path);
  if (!res.ok) throw new Error(`No se pudo cargar template: ${path}`);
  return res.text();
}

async function injectTemplates() {
  const root = document.getElementById("templateRoot");
  if (!root) {
    throw new Error("No existe #templateRoot en el HTML base");
  }

  // 1) Cargar shells principales
  const [welcomeHtml, appShellHtml] = await Promise.all([
    loadTemplate(`${templateBase}/welcome.html`),
    loadTemplate(`${templateBase}/app-shell.html`),
  ]);

  // 2) Inyectar shells
  root.innerHTML = `${welcomeHtml}\n${appShellHtml}`;

  // 3) Inyectar secciones de welcome dentro de #guideContent
  const guideContent = document.getElementById("guideContent");
  if (!guideContent) {
    throw new Error("No existe #guideContent dentro de welcome.html");
  }

  const sectionPaths = [
    `${templateBase}/sections/g-home.html`,
    `${templateBase}/sections/g-session.html`,
    `${templateBase}/sections/g-process.html`,
    `${templateBase}/sections/g-links.html`,
    `${templateBase}/sections/g-errors.html`,
  ];

  const sectionsHtml = await Promise.all(sectionPaths.map(loadTemplate));
  guideContent.innerHTML = sectionsHtml.join("\n");

  // 4) Evento global para que app.js haga boot cuando TODO estÃ© listo
  window.dispatchEvent(new Event("templates:ready"));
}

// Ejecutar loader
injectTemplates().catch((err) => {
  console.error("Template loader error:", err);

  // Fallback visual mÃ­nimo para debug rÃ¡pido
  const root = document.getElementById("templateRoot");
  if (root) {
    root.innerHTML = `
      <div style="padding:16px;color:#b00020;font-family:system-ui;">
        <h3 style="margin:0 0 8px;">Error cargando templates</h3>
        <pre style="white-space:pre-wrap;margin:0;">${err?.message || err}</pre>
      </div>
    `;
  }
});
