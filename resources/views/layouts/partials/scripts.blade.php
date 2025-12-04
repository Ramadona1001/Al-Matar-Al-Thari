<!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    <!-- Language System -->
    {{-- <script src="{{ asset('assets/js/lang.js') }}" defer></script> --}}

    <!-- Main JavaScript -->
    <script src="{{ asset('assets/js/main.js') }}" defer></script>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to update language
            function updateLanguage(lang, langText) {
                const isRTL = lang === 'rtl';
                const direction = isRTL ? 'rtl' : 'ltr';
                const locale = isRTL ? 'ar' : 'en';

                // Update document direction and language
                document.documentElement.setAttribute('dir', direction);
                document.documentElement.setAttribute('lang', locale);

                // Update body data-dir attribute if exists
                const body = document.body;
                if (body) {
                    body.setAttribute('data-dir', direction);
                }

                // Update desktop dropdown text
                const desktopTextElement = document.getElementById('currentLangTextDesktop');
                if (desktopTextElement) {
                    desktopTextElement.innerHTML = `
                        <i class="bi bi-globe" aria-hidden="true"></i>
                        <span>${langText}</span>
                    `;
                }

                // Update mobile dropdown text
                const mobileTextElement = document.getElementById('currentLangTextMobile');
                if (mobileTextElement) {
                    mobileTextElement.textContent = lang === 'ltr' ? 'EN' : 'AR';
                }

                // Update active state for all dropdowns
                document.querySelectorAll('.lang-dropdown-item').forEach(item => {
                    item.classList.remove('active');
                });

                document.querySelectorAll(`.lang-dropdown-item[data-lang="${lang}"]`).forEach(item => {
                    item.classList.add('active');
                });

                // Save preference
                localStorage.setItem('language', lang);
            }

            // RTL/LTR Toggle - Desktop (only for visual updates, let links handle navigation)
            document.querySelectorAll('.lang-dropdown-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    // Don't prevent default - let the link handle navigation
                    // Only update visual state if needed
                    const lang = this.getAttribute('data-lang');
                    const langText = this.getAttribute('data-text');

                    // Close dropdowns
                    const desktopDropdown = bootstrap.Dropdown.getInstance(document.getElementById(
                        'langDropdownDesktop'));
                    if (desktopDropdown) {
                        desktopDropdown.hide();
                    }

                    const mobileDropdown = bootstrap.Dropdown.getInstance(document.getElementById(
                        'langDropdownMobile'));
                    if (mobileDropdown) {
                        mobileDropdown.hide();
                    }
                });
            });

            // Load current language from page locale
            const currentDir = document.documentElement.getAttribute('dir') || 'ltr';
            const currentLang = document.documentElement.getAttribute('lang') || 'en';
            const isRTL = currentDir === 'rtl' || currentLang === 'ar';
            const langText = isRTL ? 'العربية' : 'English';
            const lang = isRTL ? 'rtl' : 'ltr';
            
            // Update language display
            updateLanguage(lang, langText);
            
            // Save preference
            localStorage.setItem('language', lang);

            // Sticky Navbar on Scroll (Desktop & Mobile)
            window.addEventListener('scroll', function() {
                if (window.innerWidth >= 768) {
                    const mainNavbar = document.querySelector('.main-navbar');
                    if (mainNavbar) {
                        if (window.scrollY > 50) {
                            mainNavbar.classList.add('scrolled');
                        } else {
                            mainNavbar.classList.remove('scrolled');
                        }
                    }
                } else {
                    // Mobile header scroll effect
                    const mobileHeader = document.querySelector('.mobile-header');
                    if (mobileHeader) {
                        if (window.scrollY > 50) {
                            mobileHeader.classList.add('scrolled');
                        } else {
                            mobileHeader.classList.remove('scrolled');
                        }
                    }
                }
            });

            // Update active state in bottom nav based on current page
            function updateBottomNavActive() {
                const currentPage = window.location.pathname.split('/').pop() || 'index.html';
                document.querySelectorAll('.mobile-bottom-nav .nav-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Map pages to bottom nav items
                const pageMap = {
                    'index.html': 'home',
                    'offers.html': 'offers',
                    'companies.html': 'companies',
                    'features.html': 'features',
                    'login.html': 'account',
                    'register.html': 'account'
                };

                const pageKey = pageMap[currentPage] || 'home';
                const activeItem = document.querySelector(`.mobile-bottom-nav .nav-item[data-page="${pageKey}"]`);
                if (activeItem) {
                    activeItem.classList.add('active');
                }
            }

            updateBottomNavActive();

            // Add body class for bottom nav padding
            function updateBottomNavPadding() {
                if (window.innerWidth <= 767.98) {
                    document.body.classList.add('has-bottom-nav');
                } else {
                    document.body.classList.remove('has-bottom-nav');
                }
            }
            
            // Initial check
            updateBottomNavPadding();

            // Update on resize with debounce
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    updateBottomNavPadding();
                }, 100);
            });
            
            // Update on orientation change
            window.addEventListener('orientationchange', function() {
                setTimeout(function() {
                    updateBottomNavPadding();
                }, 100);
            });

            // Scroll to top functionality
            const scrollToTopBtn = document.querySelector('.scroll-to-top');

            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollToTopBtn.style.display = 'flex';
                } else {
                    scrollToTopBtn.style.display = 'none';
                }
            });

            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Lazy loading for images
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        });

        // Hide preloader after 3 seconds
        setTimeout(function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.classList.add('hidden');
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            }
        }, 3000);

        // Newsletter form handler
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('.newsletter-input').value;
                if (email) {
                    // Here you can add your newsletter subscription logic
                    alert('Thank you for subscribing!');
                    this.querySelector('.newsletter-input').value = '';
                }
                return false;
            });
        }
    </script>