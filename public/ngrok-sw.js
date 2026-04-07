/* Service worker: add ngrok-skip-browser-warning to same-origin requests (free ngrok tunnels). */
self.addEventListener('fetch', (event) => {
  const req = event.request;
  try {
    const url = new URL(req.url);
    if (!url.hostname.includes('ngrok')) {
      return;
    }
    const headers = new Headers(req.headers);
    if (!headers.has('ngrok-skip-browser-warning')) {
      headers.set('ngrok-skip-browser-warning', 'true');
    }
    const newReq = new Request(req, { headers });
    event.respondWith(fetch(newReq));
  } catch (e) {
    event.respondWith(fetch(req));
  }
});
