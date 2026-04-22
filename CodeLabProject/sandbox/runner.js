const ALLOWED_ORIGIN = 'http://localhost'; // zmień na swój port jeśli inny

window.addEventListener('message', (event) => {
  if (event.origin !== ALLOWED_ORIGIN) return;

  const logs = [];
  const fakeConsole = {
    log:   (...a) => logs.push(a.map(String).join(' ')),
    error: (...a) => logs.push('Błąd: ' + a.join(' ')),
    warn:  (...a) => logs.push('Uwaga: ' + a.join(' '))
  };

  try {
    const fn = new Function('console', event.data.code);
    fn(fakeConsole);
    window.parent.postMessage({ type: 'success', logs }, ALLOWED_ORIGIN);
  } catch(e) {
    window.parent.postMessage({ type: 'error', message: e.message }, ALLOWED_ORIGIN);
  }
});