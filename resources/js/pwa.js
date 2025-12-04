// PWA Registration and Setup
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
                
                // Check for updates periodically
                setInterval(() => {
                    registration.update();
                }, 60 * 60 * 1000); // Check every hour
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// PWA Install Prompt
let deferredPrompt;
const installButton = document.getElementById('installButton');

window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent the mini-infobar from appearing on mobile
    e.preventDefault();
    // Stash the event so it can be triggered later.
    deferredPrompt = e;
    // Update UI to notify the user they can install the PWA
    if (installButton) {
        installButton.style.display = 'block';
        installButton.addEventListener('click', installPWA);
    }
});

function installPWA() {
    if (!deferredPrompt) {
        return;
    }
    // Show the install prompt
    deferredPrompt.prompt();
    // Wait for the user to respond to the prompt
    deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the install prompt');
        } else {
            console.log('User dismissed the install prompt');
        }
        deferredPrompt = null;
    });
}

// App Installed Event
window.addEventListener('appinstalled', (evt) => {
    console.log('PWA was installed');
    // Hide the install button
    if (installButton) {
        installButton.style.display = 'none';
    }
});

// Offline Detection
function updateOnlineStatus() {
    const statusElement = document.getElementById('connectionStatus');
    if (statusElement) {
        statusElement.textContent = navigator.onLine ? 'Online' : 'Offline';
        statusElement.className = navigator.onLine ? 'badge bg-success' : 'badge bg-danger';
    }
}

window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);

// Background Sync Registration
function registerBackgroundSync() {
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        navigator.serviceWorker.ready.then(registration => {
            return registration.sync.register('sync-transactions');
        }).catch(error => {
            console.log('Background sync registration failed:', error);
        });
    }
}

// Call this when offline transactions are stored
function storeOfflineTransaction(data) {
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        // Store in IndexedDB for later sync
        openDB().then(db => {
            db.add('offline_transactions', {
                data: data,
                api_key: getApiKey(), // You'll need to implement this
                timestamp: Date.now()
            });
            registerBackgroundSync();
        });
    }
}

// Open IndexedDB
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('AlMatarDB', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve({
            getAll: (storeName) => {
                return new Promise((res, rej) => {
                    const transaction = request.result.transaction([storeName], 'readonly');
                    const store = transaction.objectStore(storeName);
                    const getAllRequest = store.getAll();
                    getAllRequest.onsuccess = () => res(getAllRequest.result);
                    getAllRequest.onerror = () => rej(getAllRequest.error);
                });
            },
            add: (storeName, data) => {
                return new Promise((res, rej) => {
                    const transaction = request.result.transaction([storeName], 'readwrite');
                    const store = transaction.objectStore(storeName);
                    const addRequest = store.add(data);
                    addRequest.onsuccess = () => res(addRequest.result);
                    addRequest.onerror = () => rej(addRequest.error);
                });
            },
            delete: (storeName, id) => {
                return new Promise((res, rej) => {
                    const transaction = request.result.transaction([storeName], 'readwrite');
                    const store = transaction.objectStore(storeName);
                    const deleteRequest = store.delete(id);
                    deleteRequest.onsuccess = () => res();
                    deleteRequest.onerror = () => rej(deleteRequest.error);
                });
            }
        });
        
        request.onupgradeneeded = event => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('offline_transactions')) {
                db.createObjectStore('offline_transactions', { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}

// Push Notification Subscription
function subscribeToPushNotifications() {
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.ready.then(registration => {
            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuSBkB8-Z0fD1vNpvA3iP-CZ8' // Replace with your VAPID public key
                )
            });
        }).then(subscription => {
            console.log('Push subscription successful:', subscription);
            // Send subscription to server
            fetch('/api/push-subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(subscription)
            });
        }).catch(error => {
            console.log('Push subscription failed:', error);
        });
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');
    
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Initialize PWA features
document.addEventListener('DOMContentLoaded', () => {
    updateOnlineStatus();
    
    // Auto-subscribe to push notifications if user has granted permission
    if (Notification.permission === 'granted') {
        subscribeToPushNotifications();
    }
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                subscribeToPushNotifications();
            }
        });
    }
});