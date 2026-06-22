/**
 * Global CSRF Error Handler for Production
 * Handles 419 CSRF token mismatch errors across the application
 */

(function() {
    'use strict';

    // Global CSRF token refresh function
    window.refreshGlobalCsrfToken = function() {
        return fetch('/csrf-token', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrf_token) {
                // Update meta tag
                let metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) metaToken.setAttribute('content', data.csrf_token);
                
                // Update all form tokens
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = data.csrf_token;
                });
                
                // Update Laravel global object if it exists
                if (window.Laravel) window.Laravel.csrfToken = data.csrf_token;
                
                console.log('CSRF token refreshed successfully');
                return data.csrf_token;
            }
            throw new Error('No CSRF token received');
        })
        .catch(error => {
            console.error('Failed to refresh CSRF token:', error);
            throw error;
        });
    };

    // Auto-refresh CSRF token every 10 minutes for production stability
    setInterval(window.refreshGlobalCsrfToken, 10 * 60 * 1000);

    // Global AJAX error handler for CSRF errors
    if (window.jQuery) {
        // jQuery global AJAX error handler
        $(document).ajaxError(function(event, xhr, settings) {
            if (xhr.status === 419) {
                console.warn('CSRF token mismatch detected, refreshing token...');
                window.refreshGlobalCsrfToken().then(newToken => {
                    // Update the failed request with new token and retry
                    if (settings.headers) {
                        settings.headers['X-CSRF-TOKEN'] = newToken;
                    } else {
                        settings.headers = { 'X-CSRF-TOKEN': newToken };
                    }
                    
                    // Show user-friendly message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Security Token Refreshed',
                            text: 'Please try your action again.',
                            icon: 'info',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Security token refreshed. Please try your action again.');
                    }
                }).catch(() => {
                    // Show error message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Session Expired',
                            text: 'Please refresh the page and try again.',
                            icon: 'error',
                            confirmButtonText: 'Refresh Page'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert('Session expired. Please refresh the page.');
                        window.location.reload();
                    }
                });
            }
        });
    }

    // Global fetch wrapper for automatic CSRF error handling
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args).then(response => {
            if (response.status === 419) {
                console.warn('CSRF token mismatch detected in fetch request, refreshing token...');
                return window.refreshGlobalCsrfToken().then(newToken => {
                    // Update the request headers with new token
                    const [url, options = {}] = args;
                    if (!options.headers) options.headers = {};
                    
                    if (typeof options.headers.set === 'function') {
                        options.headers.set('X-CSRF-TOKEN', newToken);
                    } else {
                        options.headers['X-CSRF-TOKEN'] = newToken;
                    }
                    
                    // Show user message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Security Token Refreshed',
                            text: 'Please try your action again.',
                            icon: 'info',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    }
                    
                    // Don't automatically retry - let user retry manually for safety
                    return new Response(JSON.stringify({
                        error: 'CSRF token refreshed. Please retry your action.',
                        csrf_token_refreshed: true
                    }), {
                        status: 419,
                        headers: { 'Content-Type': 'application/json' }
                    });
                });
            }
            return response;
        });
    };

    console.log('Global CSRF error handler initialized');
})();
