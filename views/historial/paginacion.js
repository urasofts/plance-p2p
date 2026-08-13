(function () {
    var PAGE_SIZE = 10;

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('table.table').forEach(initPaginacion);
    });

    function initPaginacion(table) {
        var tbody = table.querySelector('tbody');
        if (!tbody) return;

        var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
        if (rows.length <= PAGE_SIZE) return;

        var totalPages = Math.ceil(rows.length / PAGE_SIZE);
        var current = 1;

        var nav = document.createElement('div');
        nav.className = 'paginacion';
        var wrapper = table.closest('.table-responsive') || table;
        wrapper.parentNode.insertBefore(nav, wrapper.nextSibling);

        function render() {
            var start = (current - 1) * PAGE_SIZE;
            var end = start + PAGE_SIZE;
            rows.forEach(function (row, i) {
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });
            buildNav();
        }

        function buildNav() {
            nav.innerHTML = '';
            nav.appendChild(makeBtn('\u2039', current > 1, function () { current--; render(); }));

            pageList(current, totalPages).forEach(function (p) {
                if (p === '...') {
                    var span = document.createElement('span');
                    span.className = 'pag-ellipsis';
                    span.textContent = '\u2026';
                    nav.appendChild(span);
                } else {
                    var btn = makeBtn(p, true, function () { current = p; render(); }, p === current);
                    nav.appendChild(btn);
                }
            });

            nav.appendChild(makeBtn('\u203a', current < totalPages, function () { current++; render(); }));

            var info = document.createElement('span');
            info.className = 'pag-info';
            info.textContent = 'Pag. ' + current + ' de ' + totalPages + ' \u00b7 ' + rows.length + ' registros';
            nav.appendChild(info);
        }

        function makeBtn(label, enabled, onClick, isActive) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pag-btn' + (isActive ? ' active' : '');
            btn.textContent = label;
            if (!enabled) btn.disabled = true;
            else if (!isActive) btn.addEventListener('click', onClick);
            return btn;
        }

        function pageList(cur, total) {
            var pages = [];
            if (total <= 7) {
                for (var i = 1; i <= total; i++) pages.push(i);
                return pages;
            }
            pages.push(1);
            var left = Math.max(2, cur - 1);
            var right = Math.min(total - 1, cur + 1);
            if (left > 2) pages.push('...');
            for (var j = left; j <= right; j++) pages.push(j);
            if (right < total - 1) pages.push('...');
            pages.push(total);
            return pages;
        }

        render();
    }
})();
