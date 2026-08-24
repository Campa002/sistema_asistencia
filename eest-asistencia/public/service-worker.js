/**
 * Service Worker — Sistema de Gestión de Asistencia (EEST N°1)
 *
 * Alcance de esta implementación (a propósito, limitado):
 *  - Habilita instalación (PWA installable) y un shell estático básico offline.
 *  - Cachea SOLO archivos estáticos y no personalizados: CSS, JS, imágenes,
 *    íconos, el manifest y la página de fallback offline.
 *  - NUNCA cachea nada que pase por index.php (todas las páginas dinámicas
 *    de la app — dashboards, listados, asistencia, mensajes, justificaciones,
 *    reemplazos, perfiles, etc. — se sirven exclusivamente vía index.php con
 *    datos reales y privados por usuario/sesión). Esas peticiones van
 *    siempre directo a la red, nunca al cache, para que un usuario jamás
 *    pueda ver datos de otro usuario en un dispositivo compartido.
 *  - NUNCA cachea peticiones que no sean GET (todas las escrituras de la app
 *    son POST y ya se ignoran por esta regla).
 *
 * Actualización de cache: el nombre de cache incluye una versión (CACHE_VERSION).
 * Al cambiar assets estáticos de forma relevante, subir CACHE_VERSION fuerza
 * que 'activate' borre el cache viejo y 'install' precargue el nuevo.
 */

const CACHE_VERSION = 'v1';
const CACHE_NAME = `eest-asistencia-static-${CACHE_VERSION}`;

// Shell estático mínimo y seguro (nada de HTML dinámico ni datos privados).
const PRECACHE_URLS = [
  'manifest.webmanifest',
  'offline.html',
  'assets/css/styles.css',
  'assets/css/toast.css',
  'assets/img/logo.webp',
  'assets/img/icons/icon-192.png',
  'assets/img/icons/icon-512.png',
  'assets/img/icons/icon-192-maskable.png',
  'assets/img/icons/icon-512-maskable.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      // addAll fallaría entero si un solo recurso falla; se agrega de a uno
      // para que la instalación no se rompa por un recurso opcional.
      return Promise.all(
        PRECACHE_URLS.map((url) =>
          cache.add(url).catch(() => { /* recurso opcional, se ignora */ })
        )
      );
    }).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key.startsWith('eest-asistencia-static-') && key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

/**
 * true solo para recursos estáticos y no personalizados que es seguro
 * cachear: bajo /assets/ (css, js, img) o el propio manifest. Todo lo
 * demás — y en particular cualquier URL con "index.php" — nunca se cachea.
 */
function esEstaticoCacheable(url) {
  if (url.pathname.includes('index.php')) return false;
  if (url.pathname.endsWith('manifest.webmanifest')) return true;
  if (url.pathname.endsWith('offline.html')) return true;
  return /\/assets\/(css|js|img)\//.test(url.pathname);
}

self.addEventListener('fetch', (event) => {
  const req = event.request;

  // Nunca interceptar nada que no sea GET (todas las escrituras son POST).
  if (req.method !== 'GET') return;

  const url = new URL(req.url);

  // Nunca interceptar peticiones a otro origen (ej. Google Fonts, CDNs).
  if (url.origin !== self.location.origin) return;

  if (esEstaticoCacheable(url)) {
    // Stale-while-revalidate: responde rápido desde cache si existe, y en
    // paralelo pide la versión actual a la red para mantener el cache al día.
    event.respondWith(
      caches.open(CACHE_NAME).then((cache) =>
        cache.match(req).then((cached) => {
          const fetchPromise = fetch(req)
            .then((res) => {
              if (res && res.ok) cache.put(req, res.clone());
              return res;
            })
            .catch(() => cached);
          return cached || fetchPromise;
        })
      )
    );
    return;
  }

  // Todo lo demás (index.php y cualquier otra ruta dinámica): siempre red,
  // nunca cache. Si es una navegación y falla por estar offline, se muestra
  // la página de fallback offline (sin datos de ningún usuario).
  if (req.mode === 'navigate') {
    event.respondWith(
      fetch(req).catch(() => caches.match('offline.html'))
    );
  }
  // Si no es navegación (ej. fetch/AJAX a index.php), no se intercepta:
  // se deja pasar tal cual a la red sin respondWith.
});
