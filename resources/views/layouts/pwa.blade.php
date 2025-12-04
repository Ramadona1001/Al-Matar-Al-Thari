<!-- PWA Install Button -->
@if(!isset($hidePwaInstall) || !$hidePwaInstall)
<div id="pwaInstallContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div id="pwaInstallBanner" class="alert alert-info alert-dismissible fade show d-none" role="alert">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fas fa-mobile-alt fa-2x"></i>
            </div>
            <div>
                <h6 class="alert-heading mb-1">Install Al-Matar Al-Thari App</h6>
                <p class="mb-2 small">Get the best experience with our mobile app. Install now for offline access and push notifications.</p>
                <div class="d-flex gap-2">
                    <button id="installButton" class="btn btn-primary btn-sm">
                        <i class="fas fa-download me-1"></i>Install
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="alert">
                        Later
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Connection Status Indicator -->
<div id="connectionStatusContainer" class="position-fixed top-0 start-50 translate-middle-x mt-2" style="z-index: 1050;">
    <span id="connectionStatus" class="badge bg-success">Online</span>
</div>

<!-- PWA Scripts -->
<script src="{{ asset('js/pwa.js') }}" defer></script>

<script>
// PWA Install Banner Logic
document.addEventListener('DOMContentLoaded', function() {
    // Check if app is already installed
    if (window.matchMedia('(display-mode: standalone)').matches) {
        // App is installed, hide install banner
        return;
    }

    // Show install banner after 3 seconds
    setTimeout(function() {
        const banner = document.getElementById('pwaInstallBanner');
        if (banner) {
            banner.classList.remove('d-none');
        }
    }, 3000);

    // Handle banner dismissal
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) {
        banner.addEventListener('closed.bs.alert', function() {
            // Store dismissal in localStorage
            localStorage.setItem('pwaInstallDismissed', Date.now().toString());
        });
    }

    // Check if user previously dismissed the banner (within last 7 days)
    const dismissedTime = localStorage.getItem('pwaInstallDismissed');
    if (dismissedTime) {
        const daysSinceDismissed = (Date.now() - parseInt(dismissedTime)) / (1000 * 60 * 60 * 24);
        if (daysSinceDismissed < 7) {
            // Don't show banner if dismissed recently
            const banner = document.getElementById('pwaInstallBanner');
            if (banner) {
                banner.remove();
            }
        } else {
            // Remove old dismissal record
            localStorage.removeItem('pwaInstallDismissed');
        }
    }
});

// Offline Transaction Handler
function handleOfflineTransaction(transactionData) {
    if (!navigator.onLine) {
        // Store transaction for later sync
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            storeOfflineTransaction(transactionData);
            showOfflineNotification('Transaction saved offline and will sync when connection is restored.');
        } else {
            showOfflineNotification('You are offline. Transaction will be processed when connection is restored.');
        }
    }
}

function showOfflineNotification(message) {
    // Create a toast notification
    const toastHtml = `
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-wifi text-warning me-2"></i>
                <strong class="me-auto">Offline Mode</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    toastContainer.innerHTML = toastHtml;
    document.body.appendChild(toastContainer);
    
    const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
    toast.show();
    
    // Remove container after toast is hidden
    toastContainer.querySelector('.toast').addEventListener('hidden.bs.toast', function() {
        toastContainer.remove();
    });
}

// Network request with offline handling
async function fetchWithOfflineSupport(url, options = {}) {
    try {
        const response = await fetch(url, options);
        return response;
    } catch (error) {
        if (!navigator.onLine) {
            // Return offline response
            return new Response(JSON.stringify({ 
                offline: true, 
                message: 'You are offline. This action will be synced when connection is restored.' 
            }), {
                status: 503,
                headers: { 'Content-Type': 'application/json' }
            });
        }
        throw error;
    }
}
</script>