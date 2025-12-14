// Custom App JS - No build process needed
// This file replaces the need for npm build process

// Initialize Alpine.js from CDN (will be loaded separately)
document.addEventListener('DOMContentLoaded', function() {
    // Check if Alpine is available
    if (typeof window.Alpine !== 'undefined') {
        window.Alpine.start();
    }

    // Initialize GSAP animations if available
    if (typeof window.gsap !== 'undefined' && typeof window.ScrollTrigger !== 'undefined') {
        // Register ScrollTrigger plugin
        window.gsap.registerPlugin(window.ScrollTrigger);

        // Initialize animations on page load
        initAnimations();
    }
});

// Animation initialization function
function initAnimations() {
    if (typeof window.gsap === 'undefined') return;

    const gsap = window.gsap;
    const ScrollTrigger = window.ScrollTrigger;

    // Animate elements with data-animate attribute
    document.querySelectorAll('[data-animate]').forEach((el, index) => {
        const animation = el.getAttribute('data-animate');
        const delay = parseFloat(el.getAttribute('data-delay')) || index * 0.1;
        
        switch(animation) {
            case 'fade-in-up':
                gsap.from(el, {
                    opacity: 0,
                    y: 30,
                    duration: 0.8,
                    delay: delay,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    }
                });
                break;
            case 'fade-in':
                gsap.from(el, {
                    opacity: 0,
                    duration: 0.8,
                    delay: delay,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    }
                });
                break;
            case 'slide-in-left':
                gsap.from(el, {
                    opacity: 0,
                    x: -50,
                    duration: 0.8,
                    delay: delay,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    }
                });
                break;
            case 'slide-in-right':
                gsap.from(el, {
                    opacity: 0,
                    x: 50,
                    duration: 0.8,
                    delay: delay,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    }
                });
                break;
        }
    });
}

// Make functions available globally
window.initAnimations = initAnimations;

