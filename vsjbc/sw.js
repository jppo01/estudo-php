const CACHE = 'vsjbc-v1';
const ASSETS = [
    '/vsjbc/assets/css/app.css',
    '/vsjbc/assets/js/app.js',
    '/vsjbc/assets/js/dashboard.js',
    '/vsjbc/assets/js/oracle.js',
];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(c => c.addAll(ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', e => {
    const url = new URL(e.request.url);
    if (e.request.mode === 'navigate' || url.pathname.endsWith('.php')) {
        e.respondWith(
            fetch(e.request).catch(() =>
                caches.match('/vsjbc/') || new Response('Sem conexão', { status: 503 })
            )
        );
        return;
    }
    e.respondWith(
        caches.match(e.request).then(r => r || fetch(e.request))
    );
});
