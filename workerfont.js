/**
 * Kaneism Service Worker
 * Provides asset caching for improved performance
 */

const CACHE_NAME = 'kaneism-asset-cache-v1';
const ASSETS_TO_CACHE = [
  // CSS files
  '/assets/css/build/kaneism-inline-head.min.css',
  '/assets/css/build/kaneism-base-layout.min.css',
  '/assets/css/build/kaneism-global-layout.min.css',
  '/assets/css/build/01-theme-clean.min.css',
  '/assets/css/build/kaneism-helpers.min.css',
  
  // JS files
  '/assets/js/core/base.js',
  
  // Images
  '/assets/img/bg/splat-corner.webp',
  
  // Icons
  '/favicon.ico',
  '/assets/img/icon/safari-pinned-tab.svg',
  '/assets/img/icon/favicon-32x32.png',
  '/assets/img/icon/favicon-16x16.png',
  '/assets/img/icon/apple-touch-icon.png',
  '/assets/img/icon/mstile-144x144.png',
  
  // Add your font files here - examples:
  // '/assets/fonts/your-font-regular.woff2',
  // '/assets/fonts/your-font-bold.woff2',
  // '/assets/fonts/your-font-italic.woff2'
];

/**
 * Installation event
 * Caches all predefined assets when the service worker is installed
 */
self.addEventListener('install', event => {
  // Use waitUntil to signal the duration of the install event
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache opened, adding assets');
        return cache.addAll(ASSETS_TO_CACHE);
      })
      .catch(error => {
        console.error('Error during service worker install:', error);
      })
  );
  
  // Force this service worker to activate immediately if another version is waiting
  self.skipWaiting();
});

/**
 * Activation event
 * Cleans up old caches when a new service worker is activated
 */
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.filter(cacheName => {
          // Delete any caches that match our pattern but aren't the current version
          return cacheName.startsWith('kaneism-asset-cache-') && cacheName !== CACHE_NAME;
        }).map(cacheName => {
          console.log('Deleting old cache:', cacheName);
          return caches.delete(cacheName);
        })
      );
    })
  );
  
  // Ensure the service worker immediately takes control of the page
  return self.clients.claim();
});

/**
 * Fetch event
 * Serves cached assets when available, otherwise fetches from network
 * Only caches static assets like CSS, JS, images, and fonts
 */
self.addEventListener('fetch', event => {
  // Skip cross-origin requests
  if (!event.request.url.startsWith(self.location.origin)) {
    return;
  }
  
  // Only cache GET requests for static assets
  if (event.request.method !== 'GET' || !event.request.url.match(/\.(css|js|png|jpg|jpeg|gif|svg|webp|woff|woff2|ttf|eot|ico)$/)) {
    return;
  }
  
  event.respondWith(
    caches.match(event.request)
      .then(cachedResponse => {
        if (cachedResponse) {
          // Return cached asset
          return cachedResponse;
        }
        
        // Not in cache, fetch from network
        return fetch(event.request)
          .then(response => {
            // Check if we received a valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }
            
            // Clone the response stream so we can both cache it and return it
            const responseToCache = response.clone();
            
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseToCache);
              })
              .catch(error => {
                console.error('Error caching new asset:', error);
              });
            
            return response;
          })
          .catch(error => {
            console.error('Fetch failed:', error);
            // You could return a custom offline asset here if needed
          });
      })
  );
});

/**
 * Push event - for future implementation of push notifications
 */
// self.addEventListener('push', event => {
//   // Handle push notifications here when you're ready to implement them
// });

/**
 * Message event - for communication with main thread
 */
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
}); 