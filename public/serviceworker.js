const CACHE_NAME = 'epos-v2';
const STATIC_ASSETS = [
    '/manifest.json',
    // External assets are cached on runtime to handle CORS/Opaque responses
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Static Assets Cache
    if (STATIC_ASSETS.includes(url.pathname)) {
        event.respondWith(
            caches.match(event.request).then((response) => {
                return response || fetch(event.request);
            })
        );
        return;
    }

    // Network First for everything else (Navigation, API)
    event.respondWith(
        (async () => {
            // Navigation Preload / Special Handling for Navigation
            if (event.request.mode === 'navigate') {
                // ... existing navigation logic ...
                try {
                    const networkResponse = await fetch(event.request);
                    // Update Cache with new HTML
                    const cache = await caches.open(CACHE_NAME);
                    cache.put(event.request, networkResponse.clone());
                    return networkResponse;
                } catch (error) {
                    const cachedResponse = await caches.match(event.request); // Match exact request (e.g. /pos)
                    if (cachedResponse) return cachedResponse;
                    return caches.match('/manifest.json');
                }
            }

            // Only cache GET requests
            if (event.request.method !== 'GET') {
                return fetch(event.request);
            }

            try {
                // Special handling for CDNs to avoid CORS errors
                if (url.hostname.includes('cdn.tailwindcss.com') ||
                    url.hostname.includes('fonts.googleapis.com') ||
                    url.hostname.includes('cdn.jsdelivr.net')) {

                    // Try cache first for these static assets
                    const cachedResponse = await caches.match(event.request);
                    if (cachedResponse) return cachedResponse;

                    // Fetch with no-cors to allow caching opaque response
                    const response = await fetch(event.request, { mode: 'no-cors' });
                    const cache = await caches.open(CACHE_NAME);
                    cache.put(event.request, response.clone());
                    return response;
                }

                // Standard Network First for other assets
                const response = await fetch(event.request);

                // Check if we received a valid response
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    if (response && response.type === 'opaque') {
                        const responseToCache = response.clone();
                        const cache = await caches.open(CACHE_NAME);
                        cache.put(event.request, responseToCache);
                        return response;
                    }
                    return response;
                }

                const responseToCache = response.clone();
                const cache = await caches.open(CACHE_NAME);
                cache.put(event.request, responseToCache);

                return response;
            } catch (error) {
                return caches.match(event.request);
            }
        })()
    );
});
