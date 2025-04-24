/**
 * Work Gallery JavaScript
 * 
 * Initializes the Swiper gallery for Work post type
 */
(function() {
    'use strict';
    
    // Initialize Swiper gallery when document is ready
    function initWorkGallery() {
        if (typeof Swiper === 'undefined') {
            // If Swiper is not loaded yet, try again in 500ms
            console.log('Swiper not loaded yet, retrying...');
            setTimeout(initWorkGallery, 500);
            return;
        }
        
        const galleryElements = document.querySelectorAll('.swiper');
        
        if (galleryElements.length) {
            try {
                const workGallerySwiper = new Swiper('.swiper', {
                    // Optional parameters
                    loop: true,
                    
                    // Pagination
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },

                    keyboard: {
                        enabled: true,
                        onlyInViewport: false,
                    },
                    mousewheel: {
                        invert: true,
                    },
                    
                    // Navigation arrows
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    
                    // Auto height
                    autoHeight: false,
                    
                    // Accessibility
                    a11y: true,
                    
                    // Speed
                    speed: 500,
                    
                    // Effect
                    effect: 'slide',
                });
                
                console.log('Work gallery initialized successfully');
            } catch (error) {
                console.error('Error initializing work gallery:', error);
            }
        }
    }
    
    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWorkGallery);
    } else {
        // DOM already loaded
        initWorkGallery();
    }
    
    // Also try to initialize when window loads (as a fallback)
    window.addEventListener('load', initWorkGallery);
    
})(); 