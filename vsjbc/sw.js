const CACHE = 'vsjbc-v1';
const ASSETS = [
    '/vsjbc/assets/css/app.css',
    '/vsjbc/assets/js/app.js',
    '/vsjbc/assets/js/dashboard.js',
    '/vsjbc/assets/js/oracle.js',
];

// Instalar: cachear assets estáticos
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(c => c.addAll(ASSETS))
    );
    self.skipWaiting();
});

// Ativar: limpar caches antigos
self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Fetch: assets em cache, páginas sempre da rede
self.addEventListener('fetch', e => {
    const url = new URL(e.request.url);

    // Sempre buscar páginas PHP da rede (dados em tempo real)
    if (e.request.mode === 'navigate' || url.pathname.endsWith('.php')) {
        e.respondWith(
            fetch(e.request).catch(() =>
                caches.match('/vsjbc/') || new Response('Sem conexão', { status: 503 })
            )
        );
        return;
    }

    // Assets estáticos: cache primeiro
    e.respondWith(
        caches.match(e.request).then(r => r || fetch(e.request))
    );
});
