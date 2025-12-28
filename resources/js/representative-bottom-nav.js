/**
 * Representative Bottom Navigation Handler
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Handle menu toggle from bottom nav
        const menuToggleBtn = document.getElementById('bottom-nav-menu-toggle');
        if (menuToggleBtn) {
            menuToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Toggle sidebar if exists
                const html = document.getElementsByTagName('html')[0];
                const sidebarToggle = document.querySelector('.button-toggle-menu');
                
                if (sidebarToggle) {
                    sidebarToggle.click();
                } else {
                    // If no sidebar toggle, show a menu or do something else
                    console.log('Menu toggle clicked');
                }
            });
        }

        // Update active state based on current route
        const currentRoute = window.location.pathname;
        const bottomNavItems = document.querySelectorAll('.bottom-nav-item');
        
        bottomNavItems.forEach(function(item) {
            const route = item.getAttribute('data-route');
            const href = item.getAttribute('href');
            
            if (href && href !== '#' && currentRoute.includes(href.replace(window.location.origin, ''))) {
                item.classList.add('active');
            }
        });

        // Handle search functionality
        const searchInput = document.getElementById('search-input');
        const searchBtn = document.getElementById('search-btn');
        const filterBtn = document.getElementById('filter-btn');

        if (searchBtn && searchInput) {
            searchBtn.addEventListener('click', function() {
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    // TODO: Implement search functionality
                    console.log('Searching for:', searchTerm);
                }
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchBtn.click();
                }
            });
        }

        if (filterBtn) {
            filterBtn.addEventListener('click', function() {
                // TODO: Implement filter functionality
                console.log('Filter clicked');
            });
        }

        // Add scroll effect to floating navigation
        const bottomNav = document.querySelector('.representative-bottom-nav');
        if (bottomNav) {
            let lastScrollTop = 0;
            let ticking = false;

            function handleScroll() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        if (scrollTop > lastScrollTop && scrollTop > 100) {
                            // Scrolling down
                            bottomNav.classList.add('scrolled');
                        } else {
                            // Scrolling up
                            bottomNav.classList.remove('scrolled');
                        }
                        
                        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                        ticking = false;
                    });
                    
                    ticking = true;
                }
            }

            window.addEventListener('scroll', handleScroll, { passive: true });
        }
    });
})();

