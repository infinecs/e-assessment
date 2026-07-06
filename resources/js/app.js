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

    // ── Sidebar Tooltips (collapsed state only) ─────────────────────────────
    let activeTooltip = null;

    function removeTooltip() {
        if (activeTooltip) {
            activeTooltip.remove();
            activeTooltip = null;
        }
    }

    const sideMenu = document.getElementById('side-menu');
    if (sideMenu) {
        sideMenu.querySelectorAll('a[data-tooltip]').forEach(link => {
            link.addEventListener('mouseenter', function() {
                if (body.getAttribute('data-sidebar-size') !== 'sm') return;

                const label = this.getAttribute('data-tooltip');
                const rect  = this.getBoundingClientRect();

                activeTooltip = document.createElement('div');
                activeTooltip.textContent = label;
                Object.assign(activeTooltip.style, {
                    position:      'fixed',
                    left:          (rect.right + 10) + 'px',
                    top:           (rect.top + rect.height / 2) + 'px',
                    transform:     'translateY(-50%)',
                    background:    '#4f46e5',
                    color:         '#fff',
                    padding:       '4px 12px',
                    borderRadius:  '6px',
                    fontSize:      '12px',
                    fontWeight:    '500',
                    whiteSpace:    'nowrap',
                    zIndex:        '99999',
                    pointerEvents: 'none',
                    boxShadow:     '0 2px 8px rgba(0,0,0,0.18)',
                });
                document.body.appendChild(activeTooltip);
            });

            link.addEventListener('mouseleave', removeTooltip);
            link.addEventListener('click', removeTooltip);
        });
    }

});
