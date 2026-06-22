import './bootstrap';

// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const body = document.querySelector('body');
    const toggleBtn = document.getElementById('sidebar-toggle');
    
    // Initialize sidebar state from localStorage
    const savedSidebarState = localStorage.getItem('sidebar-state');
    if (savedSidebarState) {
        body.setAttribute('data-sidebar-size', savedSidebarState);
    }
    
    // Add toggle button click event
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const currentState = body.getAttribute('data-sidebar-size');
            const newState = currentState === 'lg' ? 'sm' : 'lg';
            
            body.setAttribute('data-sidebar-size', newState);
            localStorage.setItem('sidebar-state', newState);
            
            // Reinitialize feather icons if they exist
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    }
});

