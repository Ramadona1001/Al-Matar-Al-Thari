// Service Worker for Al-Matar Al-Thari PWA
const CACHE_NAME = 'almatar-althari-v1';
const urlsToCache = [
  '/',
  '/offline',
  '/css/app.css',
  '/js/app.js',
  '/js/bootstrap.js',
  '/images/logo.png',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png'
];

// Install event - cache resources
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Return cached version or fetch from network
        if (response) {
          return response;
        }

        return fetch(event.request).then(response => {
          // Don't cache non-successful responses
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response;
          }

          // Clone the response
          const responseToCache = response.clone();

          caches.open(CACHE_NAME)
            .then(cache => {
              cache.put(event.request, responseToCache);
            });

          return response;
        });
      })
      .catch(() => {
        // Return offline page for navigation requests
        if (event.request.mode === 'navigate') {
          return caches.match('/offline');
        }
        
        // Return offline placeholder for API requests
        if (event.request.url.includes('/api/')) {
          return new Response(
            JSON.stringify({
              success: false,
              message: 'You are offline. Please check your internet connection.',
              offline: true
            }),
            {
              headers: { 'Content-Type': 'application/json' }
            }
          );
        }
      })
  );
});

// Background sync for offline actions
self.addEventListener('sync', event => {
  if (event.tag === 'sync-transactions') {
    event.waitUntil(syncTransactions());
  }
});

// Push notifications
self.addEventListener('push', event => {
  const options = {
    body: event.data ? event.data.text() : 'New notification from Al-Matar Al-Thari',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-96x96.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    }
  };

  event.waitUntil(
    self.registration.showNotification('Al-Matar Al-Thari', options)
  );
});

// Notification click
self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow('/')
  );
});

// Sync offline transactions
async function syncTransactions() {
  try {
    // Get offline transactions from IndexedDB
    const db = await openDB();
    const offlineTransactions = await db.getAll('offline_transactions');
    
    for (const transaction of offlineTransactions) {
      try {
        const response = await fetch('/api/pos/transaction', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${transaction.api_key}`
          },
          body: JSON.stringify(transaction.data)
        });

        if (response.ok) {
          // Remove successfully synced transaction
          await db.delete('offline_transactions', transaction.id);
        }
      } catch (error) {
        console.error('Failed to sync transaction:', error);
      }
    }
  } catch (error) {
    console.error('Sync failed:', error);
  }
}

// Open IndexedDB
function openDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('AlMatarDB', 1);
    
    request.onerror = () => reject(request.error);
    request.onsuccess = () => resolve(request.result);
    
    request.onupgradeneeded = event => {
      const db = event.target.result;
      if (!db.objectStoreNames.contains('offline_transactions')) {
        db.createObjectStore('offline_transactions', { keyPath: 'id', autoIncrement: true });
      }
    };
  });
}