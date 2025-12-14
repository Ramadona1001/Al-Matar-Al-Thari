/**
 * Main JavaScript File
 * Handles all interactive features
 */

(function($) {
  'use strict';

  // Wait for DOM and language system to be ready
  $(document).ready(function() {
    // Initialize all features
    initNavbar();
    initSmoothScroll();
    initFAQ();
    initForms();
    initCarousel();
    initScrollToTop();
    initMobileMenu();
  });

  /**
   * Navbar scroll effects
   */
  function initNavbar() {
    const navbar = $('.navbar');
    
    $(window).scroll(function() {
      if ($(window).scrollTop() > 50) {
        navbar.addClass('scrolled');
      } else {
        navbar.removeClass('scrolled');
      }
    });
  }

  /**
   * Smooth scroll for anchor links
   */
  function initSmoothScroll() {
    $('a[href^="#"]').on('click', function(e) {
      const target = $(this.getAttribute('href'));
      
      if (target.length) {
        e.preventDefault();
        $('html, body').stop().animate({
          scrollTop: target.offset().top - 80
        }, 1000, 'easeInOutExpo');
      }
    });
  }

  /**
   * FAQ Accordion functionality
   */
  function initFAQ() {
    $('.faq-question').on('click', function() {
      const $this = $(this);
      const $answer = $this.next('.faq-answer');
      const $allAnswers = $('.faq-answer');
      const $allQuestions = $('.faq-question');
      
      // Close all other FAQs
      $allQuestions.not($this).removeClass('active');
      $allAnswers.not($answer).removeClass('active').slideUp(300);
      
      // Toggle current FAQ
      $this.toggleClass('active');
      $answer.toggleClass('active').slideToggle(300);
    });
  }

  /**
   * Form validation
   */
  function initForms() {
    // Contact form validation
    $('#contactForm').on('submit', function(e) {
      e.preventDefault();
      
      let isValid = true;
      const form = $(this);
      
      // Reset previous errors
      form.find('.is-invalid').removeClass('is-invalid');
      form.find('.invalid-feedback').remove();
      
      // Validate name
      const name = form.find('#contactName').val().trim();
      if (!name) {
        showFieldError('#contactName', window.t ? window.t('common.required') : 'This field is required');
        isValid = false;
      }
      
      // Validate email
      const email = form.find('#contactEmail').val().trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email) {
        showFieldError('#contactEmail', window.t ? window.t('common.required') : 'This field is required');
        isValid = false;
      } else if (!emailRegex.test(email)) {
        showFieldError('#contactEmail', window.t ? window.t('common.invalidEmail') : 'Please enter a valid email');
        isValid = false;
      }
      
      // Validate message
      const message = form.find('#contactMessage').val().trim();
      if (!message) {
        showFieldError('#contactMessage', window.t ? window.t('common.required') : 'This field is required');
        isValid = false;
      }
      
      if (isValid) {
        // Show success message (in real app, this would submit to server)
        showSuccessMessage(form, window.t ? window.t('common.success') : 'Message sent successfully!');
        form[0].reset();
      }
    });

    // Registration form validation
    $('#registerForm').on('submit', function(e) {
      let isValid = true;
      const form = $(this);
      
      // Reset previous errors
      form.find('.is-invalid').removeClass('is-invalid');
      form.find('.invalid-feedback').remove();
      
      // Check user type to determine which fields to validate
      const userType = form.find('input[name="user_type"]:checked').val() || form.find('#hidden_user_type').val() || 'customer';
      
      if (userType === 'customer') {
        // Validate customer name
        const nameInput = form.find('#name');
        if (nameInput.length) {
          const name = (nameInput.val() || '').trim();
          if (!name) {
            showFieldError('#name', window.t ? window.t('common.required') : 'This field is required');
            isValid = false;
          }
        }
        
        // Validate customer email
        const emailInput = form.find('#email');
        if (emailInput.length) {
          const email = (emailInput.val() || '').trim();
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!email) {
            showFieldError('#email', window.t ? window.t('common.required') : 'This field is required');
            isValid = false;
          } else if (!emailRegex.test(email)) {
            showFieldError('#email', window.t ? window.t('common.invalidEmail') : 'Please enter a valid email');
            isValid = false;
          }
        }
        
        // Phone is optional for customers
      } else if (userType === 'merchant') {
        // Validate merchant company name
        const companyNameInput = form.find('#company_name');
        if (companyNameInput.length) {
          const companyName = (companyNameInput.val() || '').trim();
          if (!companyName) {
            showFieldError('#company_name', window.t ? window.t('common.required') : 'This field is required');
            isValid = false;
          }
        }
        
        // Validate merchant name
        const merchantNameInput = form.find('#merchant_name');
        if (merchantNameInput.length) {
          const merchantName = (merchantNameInput.val() || '').trim();
          if (!merchantName) {
            showFieldError('#merchant_name', window.t ? window.t('common.required') : 'This field is required');
            isValid = false;
          }
        }
        
        // Validate merchant email
        const merchantEmailInput = form.find('#merchant_email');
        if (merchantEmailInput.length) {
          const merchantEmail = (merchantEmailInput.val() || '').trim();
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!merchantEmail) {
            showFieldError('#merchant_email', window.t ? window.t('common.required') : 'This field is required');
            isValid = false;
          } else if (!emailRegex.test(merchantEmail)) {
            showFieldError('#merchant_email', window.t ? window.t('common.invalidEmail') : 'Please enter a valid email');
            isValid = false;
          }
        }
        
        // Validate merchant phone
        const merchantPhoneInput = form.find('#merchant_phone');
        if (merchantPhoneInput.length) {
          const merchantPhone = (merchantPhoneInput.val() || '').trim();
          if (!merchantPhone) {
            showFieldError('#merchant_phone', window.t ? window.t('common.required') : 'This field is required');
            isValid = false;
          }
        }
      }
      
      // Validate password (check for both #registerPassword and #password)
      const passwordInput = form.find('#registerPassword').length ? form.find('#registerPassword') : form.find('#password');
      const password = passwordInput.length ? passwordInput.val() : '';
      if (passwordInput.length && !password) {
        showFieldError(passwordInput.attr('id') ? '#' + passwordInput.attr('id') : '#password', window.t ? window.t('common.required') : 'This field is required');
        isValid = false;
      } else if (passwordInput.length && password && password.length < 6) {
        showFieldError(passwordInput.attr('id') ? '#' + passwordInput.attr('id') : '#password', 'Password must be at least 6 characters');
        isValid = false;
      }
      
      // Validate confirm password (check for both #confirmPassword and #password_confirmation)
      const confirmPasswordInput = form.find('#confirmPassword').length ? form.find('#confirmPassword') : form.find('#password_confirmation');
      const confirmPassword = confirmPasswordInput.length ? confirmPasswordInput.val() : '';
      if (confirmPasswordInput.length && !confirmPassword) {
        showFieldError(confirmPasswordInput.attr('id') ? '#' + confirmPasswordInput.attr('id') : '#password_confirmation', window.t ? window.t('common.required') : 'This field is required');
        isValid = false;
      } else if (confirmPasswordInput.length && password && confirmPassword && password !== confirmPassword) {
        showFieldError(confirmPasswordInput.attr('id') ? '#' + confirmPasswordInput.attr('id') : '#password_confirmation', window.t ? window.t('common.passwordMismatch') : 'Passwords do not match');
        isValid = false;
      }
      
      // Validate terms checkbox
      const termsInput = form.find('#terms');
      if (termsInput.length && !termsInput.is(':checked')) {
        showFieldError('#terms', window.t ? window.t('common.required') : 'You must accept the terms and conditions');
        isValid = false;
      }
      
      if (!isValid) {
        e.preventDefault();
        return false;
      }
      // If valid, allow form to submit normally - don't prevent default
      // Form will submit to server
      console.log('Form is valid, submitting...');
    });

    // Login form validation
    $('#loginForm').on('submit', function(e) {
      e.preventDefault();
      
      let isValid = true;
      const form = $(this);
      
      // Reset previous errors
      form.find('.is-invalid').removeClass('is-invalid');
      form.find('.invalid-feedback').remove();
      
      // Validate email
      const email = form.find('#loginEmail').val().trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email) {
        showFieldError('#loginEmail', window.t ? window.t('common.required') : 'This field is required');
        isValid = false;
      } else if (!emailRegex.test(email)) {
        showFieldError('#loginEmail', window.t ? window.t('common.invalidEmail') : 'Please enter a valid email');
        isValid = false;
      }
      
      // Validate password
      const password = form.find('#loginPassword').val();
      if (!password) {
        showFieldError('#loginPassword', window.t ? window.t('common.required') : 'This field is required');
        isValid = false;
      }
      
      if (isValid) {
        // Show success message (in real app, this would submit to server)
        showSuccessMessage(form, window.t ? window.t('common.success') : 'Login successful!');
      }
    });
  }

  /**
   * Show field error
   */
  function showFieldError(selector, message) {
    const field = $(selector);
    field.addClass('is-invalid');
    field.after(`<div class="invalid-feedback">${message}</div>`);
  }

  /**
   * Show success message
   */
  function showSuccessMessage(form, message) {
    const alert = $(`
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `);
    form.prepend(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      alert.fadeOut(() => alert.remove());
    }, 5000);
  }

  /**
   * Initialize carousel
   */
  function initCarousel() {
    // Auto-play carousel if it exists
    if ($('.carousel').length) {
      $('.carousel').carousel({
        interval: 5000,
        pause: 'hover'
      });
    }
  }

  /**
   * Scroll to top button
   */
  function initScrollToTop() {
    const scrollBtn = $('.scroll-to-top');
    
    $(window).scroll(function() {
      if ($(window).scrollTop() > 300) {
        scrollBtn.addClass('show');
      } else {
        scrollBtn.removeClass('show');
      }
    });
    
    scrollBtn.on('click', function() {
      $('html, body').animate({
        scrollTop: 0
      }, 800);
    });
  }

  /**
   * Mobile menu toggle
   */
  function initMobileMenu() {
    $('.navbar-toggler').on('click', function() {
      $(this).toggleClass('active');
    });
    
    // Close mobile menu when clicking on a link
    $('.navbar-nav .nav-link').on('click', function() {
      if ($(window).width() < 992) {
        $('.navbar-collapse').collapse('hide');
        $('.navbar-toggler').removeClass('active');
      }
    });
  }

  /**
   * Animate on scroll
   */
  function initScrollAnimations() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in');
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Observe elements with animation class
    document.querySelectorAll('.step-card, .company-card, .offer-card, .info-box').forEach(el => {
      observer.observe(el);
    });
  }

  // Initialize scroll animations when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollAnimations);
  } else {
    initScrollAnimations();
  }

})(jQuery);

