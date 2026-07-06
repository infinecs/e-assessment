import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {

    // ── Sidebar Toggle ──────────────────────────────────────────────────────
    const body = document.querySelector('body');
    const toggleBtn = document.getElementById('sidebar-toggle');

    const savedSidebarState = localStorage.getItem('sidebar-state');
    if (savedSidebarState) {
        body.setAttribute('data-sidebar-size', savedSidebarState);
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const currentState = body.getAttribute('data-sidebar-size');
            const newState = currentState === 'lg' ? 'sm' : 'lg';
            body.setAttribute('data-sidebar-size', newState);
            localStorage.setItem('sidebar-state', newState);
            if (typeof feather !== 'undefined') feather.replace();
        });
    }

    // ── MetisMenu (sidebar accordion) ──────────────────────────────────────
    if (typeof MetisMenu !== 'undefined') {
        new MetisMenu('#side-menu');
    }

    // ── Dark Mode Toggle ────────────────────────────────────────────────────
    const savedMode = localStorage.getItem('color-mode') || 'light';
    body.setAttribute('data-mode', savedMode);
    applyDarkModeIcon(savedMode);

    const darkToggle = document.getElementById('dark-mode-toggle');
    if (darkToggle) {
        darkToggle.addEventListener('click', function() {
            const current = body.getAttribute('data-mode');
            const next = current === 'dark' ? 'light' : 'dark';
            body.setAttribute('data-mode', next);
            localStorage.setItem('color-mode', next);
            applyDarkModeIcon(next);
        });
    }

    function applyDarkModeIcon(mode) {
        const moon = document.getElementById('icon-moon');
        const sun  = document.getElementById('icon-sun');
        if (!moon || !sun) return;
        if (mode === 'dark') {
            moon.classList.add('hidden');
            sun.classList.remove('hidden');
        } else {
            moon.classList.remove('hidden');
            sun.classList.add('hidden');
        }
    }

});
