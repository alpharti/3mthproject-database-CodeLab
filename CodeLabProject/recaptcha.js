// ─────────────────────────────────────────────
//  recaptcha.js — reusable reCAPTCHA v3 helper
//  Usage: import or <script src="recaptcha.js">
// ─────────────────────────────────────────────

const RECAPTCHA_SITE_KEY = '6LcDBrssAAAAAHrH4oMz12Mk9rqdYTrKPNaOHAmk';

// ── Load the reCAPTCHA script dynamically ──────────────────────────────────
function loadRecaptcha() {
  return new Promise((resolve, reject) => {
    if (window.grecaptcha) return resolve(); // already loaded

    const script = document.createElement('script');
    script.src = `https://www.google.com/recaptcha/api.js?render=${RECAPTCHA_SITE_KEY}`;
    script.onload = resolve;
    script.onerror = () => reject(new Error('Failed to load reCAPTCHA script'));
    document.head.appendChild(script);
  });
}

// ── Get a fresh token for a given action ──────────────────────────────────
// action: string — e.g. 'register', 'login', 'checkout'
// Returns: Promise<string> — the token to send to your backend
async function getRecaptchaToken(action = 'submit') {
  await loadRecaptcha();

  return new Promise((resolve, reject) => {
    grecaptcha.ready(() => {
      grecaptcha
        .execute(RECAPTCHA_SITE_KEY, { action })
        .then(resolve)
        .catch(reject);
    });
  });
}

// ── Attach reCAPTCHA to a form automatically ──────────────────────────────
// formId:       ID of the <form> element
// action:       reCAPTCHA action name
// tokenFieldId: ID of the hidden <input> that will hold the token
// onSubmit:     async function called after token is injected — receives the FormData
//               return false from onSubmit to prevent the default form submission
async function attachRecaptchaToForm(formId, action, tokenFieldId, onSubmit) {
  const form = document.getElementById(formId);
  if (!form) return console.error(`[reCAPTCHA] Form #${formId} not found`);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    try {
      const token = await getRecaptchaToken(action);

      const tokenField = document.getElementById(tokenFieldId);
      if (tokenField) tokenField.value = token;

      if (typeof onSubmit === 'function') {
        const result = await onSubmit(new FormData(form), token);
        if (result === false) return; // caller decided to stop
      }

      form.submit(); // default submit if no onSubmit or it didn't return false
    } catch (err) {
      console.error('[reCAPTCHA] Token error:', err);
    }
  });
}

// ── Backend verification helper (Node.js / server-side only) ──────────────
// Call this in your backend route — never in the browser.
// token:      the token sent from the frontend
// secretKey:  your reCAPTCHA secret key
// minScore:   minimum acceptable score (default 0.5)
// Returns:    { success, score, action, hostname } or throws on failure
async function verifyRecaptchaToken(token, secretKey, minScore = 0.5) {
  const res = await fetch('https://www.google.com/recaptcha/api/siteverify', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `secret=${secretKey}&response=${token}`,
  });

  const data = await res.json();

  if (!data.success) {
    throw new Error(`reCAPTCHA verification failed: ${(data['error-codes'] || []).join(', ')}`);
  }

  if (data.score < minScore) {
    throw new Error(`reCAPTCHA score too low: ${data.score} (min: ${minScore})`);
  }

  return data; // { success, score, action, challenge_ts, hostname }
}

// ── Exports (works with ES modules or CommonJS) ────────────────────────────
if (typeof module !== 'undefined' && module.exports) {
  // CommonJS (Node.js backend)
  module.exports = { verifyRecaptchaToken };
} else {
  // Browser globals
  window.Recaptcha = { getRecaptchaToken, attachRecaptchaToForm, loadRecaptcha };
}