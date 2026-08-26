// Pestañas (JSON/PHP) + botón "Copiar" para los bloques .code-block
// de la sección "Integración PlacetoPay" en las tiendas.
document.querySelectorAll('.code-block').forEach(function(block) {
  const tabs   = block.querySelectorAll('.code-tab');
  const panels = block.querySelectorAll('.code-panel');

  tabs.forEach(function(tab) {
    tab.addEventListener('click', function() {
      const key = tab.dataset.key;
      tabs.forEach(function(t) { t.classList.toggle('active', t === tab); });
      panels.forEach(function(p) { p.classList.toggle('active', p.dataset.key === key); });
    });
  });

  const copyBtn = block.querySelector('.code-copy');
  if (copyBtn) {
    copyBtn.addEventListener('click', function() {
      const active = block.querySelector('.code-panel.active');
      if (!active) return;
      navigator.clipboard.writeText(active.innerText).then(function() {
        const original = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="bi bi-check2"></i> Copiado';
        setTimeout(function() { copyBtn.innerHTML = original; }, 1400);
      });
    });
  }
});
