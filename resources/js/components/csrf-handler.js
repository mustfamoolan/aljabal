/**
 * CSRF Token Handler
 * Automatically refreshes CSRF token and handles 419 errors
 */
(function() {
    'use strict';

    // Get CSRF token from meta tag
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    // Update CSRF token in all forms
    function updateCsrfTokenInForms(token) {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const tokenInput = form.querySelector('input[name="_token"]');
            if (tokenInput) {
                tokenInput.value = token;
            }
        });

        // Update meta tag
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            meta.setAttribute('content', token);
        }
    }

    // Refresh CSRF token
    function refreshCsrfToken() {
        return fetch('/refresh-csrf-token', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Failed to refresh CSRF token');
        })
        .then(data => {
            if (data.token) {
                updateCsrfTokenInForms(data.token);
                console.log('CSRF token refreshed successfully');
                return data.token;
            }
            throw new Error('No token in response');
        })
        .catch(error => {
            console.error('Error refreshing CSRF token:', error);
            // Reload page as fallback
            window.location.reload();
        });
    }

    // Intercept fetch requests to handle 419 errors
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                if (response.status === 419) {
                    // CSRF token mismatch - refresh and retry
                    return refreshCsrfToken().then(token => {
                        // Update headers with new token
                        if (!args[1]) {
                            args[1] = {};
                        }
                        if (!args[1].headers) {
                            args[1].headers = {};
                        }
                        
                        if (args[1].headers instanceof Headers) {
                            args[1].headers.set('X-CSRF-TOKEN', token);
                        } else if (typeof args[1].headers === 'object') {
                            args[1].headers['X-CSRF-TOKEN'] = token;
                        }
                        
                        // Retry the request
                        return originalFetch.apply(this, args);
                    });
                }
                return response;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                throw error;
            });
    };

    // Intercept form submissions to handle 419 errors
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.tagName === 'FORM') {
            const formData = new FormData(form);
            const method = (formData.get('_method') || form.method || 'GET').toUpperCase();
            
            if (method !== 'GET') {
                // Ensure CSRF token is present
                const tokenInput = form.querySelector('input[name="_token"]');
                if (!tokenInput) {
                    const token = getCsrfToken();
                    if (token) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = '_token';
                        hiddenInput.value = token;
                        form.appendChild(hiddenInput);
                    }
                }
            }
        }
    }, true);

    // Refresh token periodically (every 30 minutes)
    setInterval(refreshCsrfToken, 30 * 60 * 1000);

    // Refresh token on page visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            refreshCsrfToken();
        }
    });

    // Refresh token on focus
    window.addEventListener('focus', refreshCsrfToken);

    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure CSRF token is in all forms
            updateCsrfTokenInForms(getCsrfToken());
        });
    } else {
        updateCsrfTokenInForms(getCsrfToken());
    }
})();
