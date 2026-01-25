const CACHE_NAME = 'epos-v1';
const STATIC_ASSETS = [
    '/pos',
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
    // For API requests, try network first, falling back to nothing (handled by app)
    // Or we could cache get requests. For now, let's keep it simple: 
    // Cache First for static, Network First for navigation/API

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
                try {
                    // Try network first
                    const networkResponse = await fetch(event.request);
                    return networkResponse;
                } catch (error) {
                    // Fallback to cache if offline
                    const cachedResponse = await caches.match('/pos');
                    if (cachedResponse) return cachedResponse;
                    // Last resort valid offline page if /pos isn't cached
                    return caches.match('/manifest.json'); // Just to return something valid or custom offline.html
                }
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
