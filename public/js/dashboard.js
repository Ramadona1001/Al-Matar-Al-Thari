document.addEventListener('DOMContentLoaded', () => {
    // Theme initialization
    const applyTheme = (theme) => {
        const body = document.body;
        if (theme === 'dark') {
            body.setAttribute('data-theme', 'dark');
        } else {
            body.removeAttribute('data-theme');
        }
        const sunIcon = document.querySelector('[data-theme-icon="sun"]');
        const moonIcon = document.querySelector('[data-theme-icon="moon"]');
        const darkLabel = document.querySelector('[data-theme-label-dark]');
        const lightLabel = document.querySelector('[data-theme-label-light]');
        if (theme === 'dark') {
            sunIcon?.classList.remove('d-none');
            moonIcon?.classList.add('d-none');
            darkLabel?.classList.add('d-none');
            lightLabel?.classList.remove('d-none');
        } else {
            sunIcon?.classList.add('d-none');
            moonIcon?.classList.remove('d-none');
            darkLabel?.classList.remove('d-none');
            lightLabel?.classList.add('d-none');
        }
    };

    const savedTheme = localStorage.getItem('dashboard_theme') || 'light';
    applyTheme(savedTheme);

    const themeToggle = document.getElementById('themeToggle');
    themeToggle?.addEventListener('click', () => {
        const current = document.body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('dashboard_theme', next);
        applyTheme(next);
    });
    const sidebar = document.getElementById('sidebar');
    const sidebarToggleButtons = document.querySelectorAll('.sidebar-toggle');
    const sidebarCloseButton = document.querySelector('.sidebar-close');
    const sidebarBackdrop = document.querySelector('.sidebar-backdrop');

    const openSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.add('is-open');
        sidebarBackdrop?.classList.add('is-visible');
        document.body.classList.add('sidebar-open');
    };

    const closeSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.remove('is-open');
        sidebarBackdrop?.classList.remove('is-visible');
        document.body.classList.remove('sidebar-open');
    };

    sidebarToggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (sidebar?.classList.contains('is-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    });

    sidebarCloseButton?.addEventListener('click', closeSidebar);
    sidebarBackdrop?.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            try {
                new bootstrap.Alert(alert).close();
            } catch (error) {
                alert.classList.add('d-none');
            }
        }, 5000);
    });

    const markAllBtn = document.querySelector('[data-notification-mark-all]');
    markAllBtn?.addEventListener('click', event => {
        event.preventDefault();
        const url = markAllBtn.getAttribute('data-notification-mark-all');
        if (!url) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => window.location.reload());
    });

    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', () => {
            const readUrl = item.getAttribute('data-notification-read');
            const targetUrl = item.getAttribute('data-notification-url');

            if (!readUrl) return;

            fetch(readUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(() => {
                if (targetUrl) {
                    window.location.href = targetUrl;
                } else {
                    window.location.reload();
                }
            });
        });
    });

    document.querySelectorAll('[data-copy]').forEach(button => {
        const targetSelector = button.getAttribute('data-copy');
        const target = document.querySelector(targetSelector);
        if (!target) return;

        button.addEventListener('click', () => {
            const value = target.value || target.innerText || '';
            if (!value) return;

            navigator.clipboard.writeText(value).then(() => {
                button.classList.add('copied');
                setTimeout(() => button.classList.remove('copied'), 1500);
            });
        });
    });

    window.markNotificationAsRead = function (url) {
        if (!url) return;
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => window.location.reload());
    };
});