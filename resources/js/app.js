import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {

    // ── Feather Icons ──────────────────────────────────────────────────────
    if (typeof feather !== 'undefined') feather.replace();

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

});
