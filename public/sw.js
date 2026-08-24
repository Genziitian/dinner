// Service Worker for DinePOS
const CACHE_NAME = 'dinepos-v1';
const ASSETS_TO_CACHE = [
  '/assets/css/bootstrap.min.css',
  '/assets/css/app.css',
  '/assets/js/bootstrap.bundle.min.js',
  '/assets/js/qrcode.min.js',
  '/assets/js/billing.js',
  '/assets/js/app.js'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE).catch((err) => {
        console.warn('SW asset pre-caching non-fatal warning:', err);
      });
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.map((key) => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  // Never cache POST / mutations or API / dynamic pages to preserve strict billing state
  if (event.request.method !== 'GET') {
    return;
  }

  const url = new URL(event.request.url);

  // For static assets, try cache first, fallback to network
  if (url.pathname.startsWith('/assets/')) {
    event.respondWith(
      caches.match(event.request).then((response) => {
        return response || fetch(event.request);
      })
    );
    return;
  }

  // For HTML requests, network first with graceful fallback
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});
