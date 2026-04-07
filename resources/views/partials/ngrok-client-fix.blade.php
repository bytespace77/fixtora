{{-- ngrok free tier checks the *incoming* request at the edge; Laravel response headers cannot skip the interstitial.
     This script + service worker add ngrok-skip-browser-warning on fetch/XHR and (after registration) on navigations. --}}
<script>
(function () {
  if (!location.hostname.includes('ngrok')) return;

  var origFetch = window.fetch;
  window.fetch = function (input, init) {
    init = init || {};
    if (typeof Request !== 'undefined' && input instanceof Request) {
      var h = new Headers(input.headers);
      if (!h.has('ngrok-skip-browser-warning')) h.set('ngrok-skip-browser-warning', 'true');
      return origFetch.call(this, new Request(input, { headers: h }), init);
    }
    var headers = new Headers(init.headers || {});
    if (!headers.has('ngrok-skip-browser-warning')) headers.set('ngrok-skip-browser-warning', 'true');
    init.headers = headers;
    return origFetch.call(this, input, init);
  };

  var origOpen = XMLHttpRequest.prototype.open;
  XMLHttpRequest.prototype.open = function () {
    var ret = origOpen.apply(this, arguments);
    try { this.setRequestHeader('ngrok-skip-browser-warning', 'true'); } catch (e) {}
    return ret;
  };

  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/ngrok-sw.js', { scope: '/' }).catch(function () {});
    });
  }
})();
</script>
