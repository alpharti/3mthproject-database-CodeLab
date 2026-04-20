const SANDBOX_ORIGIN = 'http://localhost';
// W Twoim głównym app.js – tworzysz iframe i wysyłasz kod
function runCodeSafe(userCode, callback) {
  const iframe = document.createElement('iframe');

  // sandbox blokuje: dostęp do rodzica, cookies, localStorage, popupy
  iframe.setAttribute('sandbox', 'allow-scripts');
  iframe.src = '/runner.html';
  iframe.style.display = 'none';
  document.body.appendChild(iframe);

  // Odbierz wynik
  window.addEventListener('message', function handler(event) {
    if (event.origin !== SANDBOX_ORIGIN) return;
    clearTimeout(timeout);
    iframe.remove();
    window.removeEventListener('message', handler);
    callback(event.data);
  });

  // Wyślij kod do iframe po załadowaniu
  iframe.onload = () => {
    iframe.contentWindow.postMessage({ code: userCode }, window.location.origin);
  };
}

// Użycie:
runCodeSafe(editor.value, (result) => {
  if (result.type === 'success') {
    result.logs.forEach(line => showOutput(line));
  } else {
    showError(result.message);
  }
});

 const timeout = setTimeout(() => {
    iframe.remove();
    callback({ type: 'error', message: 'Kod działa zbyt długo – czy masz pętlę nieskończoną?' });
  }, 4000);
