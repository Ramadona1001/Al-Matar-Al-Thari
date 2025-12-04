import './bootstrap';

import Alpine from 'alpinejs';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger);

// Make GSAP available globally
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;

window.Alpine = Alpine;

Alpine.start();

// Initialize animations on page load
document.addEventListener('DOMContentLoaded', function() {
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
});
