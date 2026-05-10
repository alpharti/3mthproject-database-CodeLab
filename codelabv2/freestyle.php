<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: index.php');
    exit();
}
$username = htmlspecialchars($_SESSION['login']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CodeLab — Freestyle // Devil's Sandbox</title>
  <link rel="stylesheet" href="style.css">
  <script src="libs/pyodide/pyodide.js"></script>
</head>
<body class="freestyle-page">

<div id="py-loader">
  <svg class="py-loader-sigil" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" width="52" height="52">
    <circle cx="20" cy="20" r="18" stroke="#bc8cff" stroke-width="1"/>
    <polygon points="20,4 35,30 5,30" stroke="#d29922" stroke-width="1" fill="none"/>
    <polygon points="20,36 5,10 35,10" stroke="#58a6ff" stroke-width="1" fill="none"/>
    <circle cx="20" cy="20" r="3" fill="#7ee787"/>
  </svg>
  <div class="py-loader-text">summoning python grimoire...</div>
  <div class="py-loader-bar"><div class="py-loader-fill"></div></div>
</div>

<header class="topbar">
  <a href="home.php" class="logo">&gt; code<span>lab</span></a>

  <nav class="topbar-nav">
    <a href="home.php"      class="nav-link">home</a>
    <a href="#"             class="nav-link">tracks</a>
    <a href="#"             class="nav-link">labs</a>
    <a href="freestyle.php" class="nav-link nav-link--active">✦ freestyle</a>
  </nav>

  <div class="topbar-right-group">
    <span class="fs-user-badge"><?= $username ?>@gremory ~</span>
    <button class="run-btn" id="run-btn" onclick="runCode()">&#9654; execute</button>
    <a href="logout.php" class="logout-link">logout</a>
  </div>
</header>

<div class="freestyle-workspace">

  <div class="fs-editor-col">
    <div class="fs-panel-bar">
      <div class="bar-dots">
        <div class="bar-dot r"></div>
        <div class="bar-dot y"></div>
        <div class="bar-dot g"></div>
      </div>
      <span class="bar-filename" id="bar-filename">script.js</span>
      <div class="lang-tabs">
        <button class="lang-tab active-js"  id="tab-js"   onclick="setLang('js')">JS</button>
        <button class="lang-tab"            id="tab-html" onclick="setLang('html')">HTML</button>
        <button class="lang-tab"            id="tab-css"  onclick="setLang('css')">CSS</button>
        <button class="lang-tab"            id="tab-py"   onclick="setLang('py')">Python</button>
        <button class="lang-tab"            id="tab-full" onclick="setLang('full')">Full</button>
      </div>
    </div>
    <textarea class="fs-editor" id="editor"
              spellcheck="false"
              autocomplete="off"
              placeholder="// seal your code here, <?= $username ?>..."></textarea>
  </div>

  <div class="fs-right-col">

    <div class="fs-preview-panel">
      <div class="fs-panel-bar">
        <span class="bar-label" id="preview-label">preview</span>
        <span id="py-status" class="py-status-text"></span>
      </div>
      <iframe id="preview" class="fs-preview-frame" sandbox="allow-scripts"></iframe>
      <pre id="py-out" class="fs-py-out"></pre>
    </div>

    <div class="fs-terminal">
      <div class="fs-panel-bar">
        <span class="bar-label">terminal</span>
        <span id="ddraig-msg" class="ddraig-quote"></span>
        <button class="term-clear-btn" onclick="clearTerm()">clear</button>
      </div>
      <div class="fs-term-body" id="term"></div>
      <div class="fs-repl-row">
        <span class="fs-repl-prompt">~ $</span>
        <input class="fs-repl-input" id="repl"
               placeholder="eval JS expression... (Ddraig is watching)"
               autocomplete="off"
               onkeydown="replEval(event)">
      </div>
    </div>

  </div>
</div>

<div class="fs-statusbar">
  <span id="fs-status" class="fs-status-ok">sealed ✦</span>
  <span id="fs-linecol" class="fs-stat-hide">ln 1, col 1</span>
  <span style="margin-left:auto;" id="fs-mode" class="fs-stat-hide">JavaScript</span>
  <span>⚔ kuoh.academy / freestyle</span>
</div>

<script>
const ddraigQuotes = [
  '"Partner, your code awakens the Boosted Gear."',
  '"I, the Welsh Dragon, shall boost your output."',
  '"Run it. Even Rias is watching."',
  '"No bugs shall survive the Crimson Promotion."',
  '"Boost. Boost. Boost. Execute."',
  '"Akeno would approve of this logic."',
  '"A devil\'s contract was signed with the runtime."',
  '"The Oppai Dragon writes clean code."',
  '"Even Vali would envy this function."',
  '"Ddraig says: no null pointer exceptions today."',
];
const ddraigEl = document.getElementById('ddraig-msg');
function showDdraig() {
  ddraigEl.textContent = ddraigQuotes[Math.floor(Math.random() * ddraigQuotes.length)];
}
showDdraig();

const editor    = document.getElementById('editor');
const preview   = document.getElementById('preview');
const pyOut     = document.getElementById('py-out');
const term      = document.getElementById('term');
const statusEl  = document.getElementById('fs-status');
const linecolEl = document.getElementById('fs-linecol');
const modeLabel = document.getElementById('fs-mode');
const runBtn    = document.getElementById('run-btn');
const pyStatus  = document.getElementById('py-status');
const pyLoader  = document.getElementById('py-loader');

let lang = 'js';
let pyodide = null;
let pyLoading = false;

const defaults = {
  js:
`// ✦ JavaScript — Boosted Gear Mode
// console.log() output appears in the terminal below
// Tip: Ctrl+Enter to run without clicking

const boost = (power, times) => power * (2 ** times)

console.log("Boosted Gear activated!")
console.log("Power after 3 boosts:", boost(10, 3))

const clubMembers = ["Rias", "Akeno", "Koneko", "Asia", "Xenovia"]
clubMembers.forEach((name, i) => {
  console.log(\`[\${i + 1}] \${name} — ready for battle\`)
})`,

  html:
`<!-- ✦ HTML Mode — render your peerage -->
<style>
  body { font-family: sans-serif; padding: 24px; background: #0d0f11; color: #e2e8f0; }
  h1 { color: #bc8cff; margin-bottom: 8px; }
  p  { color: #8b949e; font-size: 14px; }
  .badge {
    display: inline-block; background: #1a0f33;
    border: 1px solid #2d1f4a; color: #bc8cff;
    padding: 3px 10px; border-radius: 4px;
    font-size: 12px; margin-top: 12px;
  }
</style>
<h1>✦ Occult Research Club</h1>
<p>Welcome to Kuoh Academy's most exclusive club.</p>
<p>Edit this HTML and hit <strong>Execute</strong> to see changes.</p>
<span class="badge">⚔ kuoh.academy</span>`,

  css:
`<!-- ✦ CSS Sandbox — style your Sacred Gear -->
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    min-height: 100vh; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    background: #0d0f11; font-family: 'JetBrains Mono', monospace; gap: 16px;
  }
  .gear {
    width: 140px; height: 140px; border-radius: 50%;
    background: radial-gradient(circle, #2d1f4a, #0d0f11);
    border: 2px solid #bc8cff;
    display: flex; align-items: center; justify-content: center; font-size: 44px;
    animation: pulse 2s ease-in-out infinite;
  }
  @keyframes pulse {
    0%, 100% { box-shadow: 0 0 32px rgba(188,140,255,.25); }
    50%       { box-shadow: 0 0 64px rgba(188,140,255,.55); }
  }
  p { color: #6e7681; font-size: 11px; letter-spacing: .1em; }
</style>
<div class="gear">⚔</div>
<p>// edit the CSS above to style the gear</p>`,

  py:
`# ✦ Python — Pyodide (Rias summoned it from WebAssembly)
# print() appears in the output panel and terminal below

def boost(power, times=1):
    """Ddraig's Boosted Gear — doubles power each boost."""
    return power * (2 ** times)

print("=== Boosted Gear System ===")
for i in range(1, 6):
    print(f"  Boost x{i}: {boost(100, i)}")

members = ["Rias Gremory", "Akeno Himejima", "Koneko Toujou", "Asia Argento"]
print("\\n=== Occult Research Club ===")
for m in members:
    print(f"  ✦ {m}")`,

  full:
`<!DOCTYPE html>
<html>
<head>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'JetBrains Mono', monospace; background: #0d0f11; color: #e2e8f0; padding: 24px; }
  h2 { color: #bc8cff; margin-bottom: 16px; font-size: 15px; }
  .counter { font-size: 56px; font-weight: 700; color: #7ee787; margin: 16px 0; }
  button {
    font-family: inherit; font-size: 12px; padding: 7px 18px;
    background: #1a0f33; color: #bc8cff; border: 1px solid #2d1f4a;
    border-radius: 6px; cursor: pointer; margin-right: 6px; transition: background .15s;
  }
  button:hover { background: #2d1f4a; }
  .log { margin-top: 14px; font-size: 11px; color: #6e7681; }
</style>
</head>
<body>
  <h2>⚔ Boost Counter — Full Document Mode</h2>
  <div class="counter" id="count">0</div>
  <button onclick="boost()">BOOST x2</button>
  <button onclick="reset()">reset</button>
  <div class="log" id="log">// waiting for boost...</div>
  <script>
    let n = 1
    function boost() {
      n *= 2
      document.getElementById('count').textContent = n
      document.getElementById('log').textContent = '// Ddraig: boosted to ' + n
      console.log('Boosted! Power:', n)
    }
    function reset() {
      n = 1
      document.getElementById('count').textContent = 0
      document.getElementById('log').textContent = '// reset. start again, partner.'
    }
  <\/script>
</body>
</html>`
};

const filenames = { js:'script.js', html:'index.html', css:'style.html', py:'main.py', full:'index.html' };
const modeNames = { js:'JavaScript', html:'HTML', css:'CSS Sandbox', py:'Python (Pyodide)', full:'Full Document' };
const activeClasses = { js:'active-js', html:'active-html', css:'active-css', py:'active-py', full:'active-full' };

function setLang(l) {
  lang = l;
  ['js','html','css','py','full'].forEach(k => {
    document.getElementById('tab-' + k).className = 'lang-tab' + (k === l ? ' ' + activeClasses[k] : '');
  });
  editor.value = defaults[l] || '';
  document.getElementById('bar-filename').textContent = filenames[l];
  modeLabel.textContent = modeNames[l];

  if (l === 'py') {
    preview.style.display = 'none';
    pyOut.style.display = 'block';
    pyOut.textContent = '';
    pyStatus.textContent = pyodide ? 'python: ready ✓' : 'python: not loaded';
    document.getElementById('preview-label').textContent = 'python output';
    initPyodide();
  } else {
    preview.style.display = 'block';
    pyOut.style.display = 'none';
    pyStatus.textContent = '';
    document.getElementById('preview-label').textContent = 'preview';
    preview.srcdoc = '';
  }

  clearTerm();
  setStatus('sealed ✦', '');
  showDdraig();
}

async function initPyodide() {
  if (pyodide || pyLoading) return;
  pyLoading = true;
  pyLoader.classList.add('visible');
  pyStatus.textContent = 'python: summoning...';
  runBtn.disabled = true;

  try {
    pyodide = await loadPyodide({ indexURL: 'libs/pyodide/' });
    pyStatus.textContent = 'python: ready ✓';
    pyStatus.style.color = 'var(--green)';
    termLog('sys', "Pyodide loaded. Python grimoire unsealed. Ddraig says: we're ready, partner.");
  } catch (e) {
    pyStatus.textContent = 'python: summon failed';
    pyStatus.style.color = 'var(--red)';
    termLog('err', 'Pyodide failed. Check that libs/pyodide/ exists.');
  }

  pyLoader.classList.remove('visible');
  runBtn.disabled = false;
  pyLoading = false;
}

async function runCode() {
  const code = editor.value.trim();
  if (!code) return;
  clearTerm();
  setStatus('executing...', '');
  showDdraig();
  try {
    if      (lang === 'js')   runJS(code);
    else if (lang === 'html') runHTML(code);
    else if (lang === 'css')  runHTML(code);
    else if (lang === 'full') runFull(code);
    else if (lang === 'py')   await runPython(code);
    if (lang !== 'py') setStatus('sealed ✦', 'ok');
  } catch (e) {
    setStatus('error — seal broken', 'err');
    termLog('err', e.message);
  }
}

function runJS(code) {
  preview.srcdoc = `<html><body><script>
    window.onerror = (msg, _, line) => { parent.postMessage({ t:'err', msg: msg+' (line '+line+')' }, '*'); return true; };
    ['log','warn','error','info'].forEach(k => {
      const orig = console[k].bind(console);
      console[k] = (...args) => {
        const msg = args.map(x => { try { return typeof x==='object' ? JSON.stringify(x,null,2) : String(x); } catch(e) { return String(x); } }).join(' ');
        parent.postMessage({ t: k==='error' ? 'err' : k, msg }, '*');
        orig(...args);
      };
    });
    try { ${code} } catch(e) { parent.postMessage({ t:'err', msg: e.message }, '*'); }
  <\/script></body></html>`;
}

function runHTML(code) { preview.srcdoc = code; }

function runFull(code) {
  const inject = `<script>
    window.onerror=(msg,_,l)=>{parent.postMessage({t:'err',msg:msg+' (ln '+l+')'},'*');return true};
    ['log','warn','error','info'].forEach(k=>{const o=console[k].bind(console);console[k]=(...a)=>{const m=a.map(x=>{try{return typeof x==='object'?JSON.stringify(x):String(x)}catch(e){return String(x)}}).join(' ');parent.postMessage({t:k==='error'?'err':k,msg:m},'*');o(...a);};});
  <\/script>`;
  preview.srcdoc = code.includes('</head>') ? code.replace('</head>', inject+'</head>') : inject+code;
}

async function runPython(code) {
  if (!pyodide) { termLog('err', 'Python not ready. Click Python tab first.'); return; }
  pyodide.runPython(`import sys, io\nsys.stdout = io.StringIO()\nsys.stderr = io.StringIO()`);
  try {
    await pyodide.runPythonAsync(code);
    const out = pyodide.runPython('sys.stdout.getvalue()');
    const err = pyodide.runPython('sys.stderr.getvalue()');
    pyOut.textContent = out || '// no output — Ddraig is disappointed, partner.';
    if (out) termLog('log', out.trim());
    if (err) { const el = document.createElement('span'); el.className='py-err'; el.textContent='\n'+err; pyOut.appendChild(el); termLog('warn', err.trim()); }
    setStatus('sealed ✦', 'ok');
  } catch (e) {
    const msg = e.message || String(e);
    pyOut.textContent = msg;
    termLog('err', msg);
    setStatus('error — seal broken', 'err');
  }
}

window.addEventListener('message', e => {
  if (!e.data || !e.data.t) return;
  termLog(e.data.t, e.data.msg);
  if (e.data.t === 'err') setStatus('error — seal broken', 'err');
});

const glyphs = { log:'›', err:'✖', warn:'⚠', info:'ℹ', sys:'·' };

function termLog(type, msg) {
  const line = document.createElement('div');
  line.className = 'term-line t-' + (type || 'log');
  line.innerHTML = `<span class="term-glyph">${glyphs[type]||'›'}</span><span class="term-msg">${escHtml(msg)}</span>`;
  term.appendChild(line);
  term.scrollTop = term.scrollHeight;
}

function clearTerm() { term.innerHTML = ''; }
function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function setStatus(msg, state) {
  statusEl.textContent = msg;
  statusEl.className = state==='err' ? 'fs-status-err' : state==='ok' ? 'fs-status-ok' : '';
}

function replEval(e) {
  if (e.key !== 'Enter') return;
  const inp = document.getElementById('repl');
  const expr = inp.value.trim();
  if (!expr) return;
  termLog('sys', '> ' + expr);
  try { const r = eval(expr); if (r !== undefined) termLog('info', String(r)); }
  catch (err) { termLog('err', err.message); }
  inp.value = '';
}

editor.addEventListener('keydown', e => {
  if (e.key === 'Tab') {
    e.preventDefault();
    const s = editor.selectionStart, en = editor.selectionEnd;
    editor.value = editor.value.substring(0,s) + '  ' + editor.value.substring(en);
    editor.selectionStart = editor.selectionEnd = s + 2;
  }
  if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); runCode(); }
});
editor.addEventListener('keyup',  updateCursor);
editor.addEventListener('click',  updateCursor);
function updateCursor() {
  const before = editor.value.substring(0, editor.selectionStart);
  const lines = before.split('\n');
  linecolEl.textContent = `ln ${lines.length}, col ${lines[lines.length-1].length+1}`;
}

setLang('js');
</script>
</body>
</html>
