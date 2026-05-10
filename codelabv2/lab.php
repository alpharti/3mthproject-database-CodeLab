<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: index.php');
    exit();
}
require_once 'db.php';
$username = htmlspecialchars($_SESSION['login']);
$user_id  = (int)$_SESSION['id'];
$lab_id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($lab_id === 0) { header('Location: labs.php'); exit(); }

$connect = new mysqli($host, $db_user, $db_password, $db_name);

$stmt = $connect->prepare("
    SELECT l.*, t.name AS track_name, t.slug AS track_slug, t.color AS track_color
    FROM labs l JOIN tracks t ON t.id = l.track_id
    WHERE l.id = ?
");
$stmt->bind_param("i", $lab_id);
$stmt->execute();
$lab = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$lab) { header('Location: labs.php'); exit(); }

$stmt = $connect->prepare("
    SELECT id FROM labs WHERE track_id = ? AND order_index = ? LIMIT 1
");
$next_order = $lab['order_index'] + 1;
$stmt->bind_param("ii", $lab['track_id'], $next_order);
$stmt->execute();
$next = $stmt->get_result()->fetch_assoc();
$stmt->close();
$next_lab_id = $next ? $next['id'] : null;

$stmt = $connect->prepare("
    SELECT completed, attempts FROM user_progress WHERE user_id = ? AND lab_id = ?
");
$stmt->bind_param("ii", $user_id, $lab_id);
$stmt->execute();
$progress = $stmt->get_result()->fetch_assoc();
$stmt->close();
$already_completed = $progress && $progress['completed'];

// AJAX completion handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete') {
    $output  = $_POST['output'] ?? '';
    $success = false;
    $message = '';

    switch ($lab['check_mode']) {
        case 'exact':
            $success = trim($output) === trim($lab['expected_output']);
            $message = $success ? 'correct' : 'output does not match expected';
            break;
        case 'contains':
            $success = str_contains($output, trim($lab['expected_output']));
            $message = $success ? 'correct' : 'missing: ' . htmlspecialchars($lab['expected_output']);
            break;
        default:
            $success = true;
            $message = 'marked complete';
    }

    if ($success) {
        $stmt = $connect->prepare("
            INSERT INTO user_progress (user_id, lab_id, completed, completed_at, attempts)
            VALUES (?, ?, 1, NOW(), 1)
            ON DUPLICATE KEY UPDATE completed=1, completed_at=NOW(), attempts=attempts+1
        ");
    } else {
        $stmt = $connect->prepare("
            INSERT INTO user_progress (user_id, lab_id, completed, attempts)
            VALUES (?, ?, 0, 1)
            ON DUPLICATE KEY UPDATE attempts=attempts+1
        ");
    }
    $stmt->bind_param("ii", $user_id, $lab_id);
    $stmt->execute();
    $stmt->close();
    $connect->close();

    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message, 'next_lab_id' => $success ? $next_lab_id : null]);
    exit();
}

$connect->close();

// map lang to filename for panel bar label
$lang_filename = ['py' => 'main.py', 'html' => 'index.html', 'full' => 'index.html', 'css' => 'style.css', 'js' => 'script.js'];
$lang_badge    = ['py' => 'badge-blue', 'js' => 'badge-amber', 'html' => 'badge-purple', 'css' => 'badge-blue', 'full' => 'badge-purple'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CodeLab — <?= htmlspecialchars($lab['title']) ?></title>
  <link rel="stylesheet" href="style.css">
  <?php if ($lab['lang'] === 'py'): ?>
  <script src="libs/pyodide/pyodide.js"></script>
  <?php endif; ?>
</head>
<body class="lab-page">

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
    <a href="home.php"  class="nav-link">home</a>
    <a href="labs.php"  class="nav-link">labs</a>
    <!-- track colour is a DB value — scoped as CSS var so children use it via CSS -->
    <span class="nav-link nav-link--track" style="--track-color:<?= $lab['track_color'] ?>">
      <?= htmlspecialchars($lab['track_name']) ?> · lab <?= $lab['order_index'] ?>
    </span>
  </nav>
  <div class="topbar-right-group">
    <span class="fs-user-badge"><?= $username ?>@gremory ~</span>
    <button class="run-btn" id="run-btn" onclick="runCode()">&#9654; run</button>
    <a href="logout.php" class="logout-link">logout</a>
  </div>
</header>

<div class="lab-workspace">

  <!-- INSTRUCTIONS PANEL -->
  <div class="lab-instructions" id="instructions-panel">
    <div class="fs-panel-bar">
      <span class="bar-label lab-track-label" style="--track-color:<?= $lab['track_color'] ?>">
        <?= htmlspecialchars($lab['track_name']) ?> · <?= str_pad($lab['order_index'], 2, '0', STR_PAD_LEFT) ?>
      </span>
      <button class="term-clear-btn" id="toggle-btn" onclick="toggleInstructions()" title="collapse">◀</button>
    </div>
    <div class="lab-instructions-body">
      <?= $lab['instructions'] ?>
    </div>
    <div class="lab-instructions-footer">
      <?php if ($already_completed): ?>
        <div class="lab-complete-badge">✓ completed</div>
        <?php if ($next_lab_id): ?>
          <a href="lab.php?id=<?= $next_lab_id ?>" class="btn btn-primary lab-next-btn">next lab →</a>
        <?php else: ?>
          <div class="lab-track-done">✦ track complete!</div>
        <?php endif; ?>
      <?php else: ?>
        <button class="btn btn-primary lab-submit-btn" id="submit-btn" onclick="submitLab()">
          <?= $lab['check_mode'] === 'none' ? 'mark complete ✓' : 'check answer ✓' ?>
        </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- EDITOR + RIGHT -->
  <div class="lab-editor-area">

    <div class="fs-editor-col lab-editor-col">
      <div class="fs-panel-bar">
        <div class="bar-dots">
          <div class="bar-dot r"></div>
          <div class="bar-dot y"></div>
          <div class="bar-dot g"></div>
        </div>
        <span class="bar-filename"><?= $lang_filename[$lab['lang']] ?? 'file' ?></span>
        <span class="lab-lang-badge badge <?= $lang_badge[$lab['lang']] ?? 'badge-purple' ?>">
          <?= strtoupper($lab['lang']) ?>
        </span>
      </div>
      <textarea class="fs-editor" id="editor" spellcheck="false" autocomplete="off"><?= htmlspecialchars($lab['starter_code'] ?? '') ?></textarea>
    </div>

    <div class="fs-right-col">
      <div class="fs-preview-panel">
        <div class="fs-panel-bar">
          <span class="bar-label" id="preview-label"><?= $lab['lang'] === 'py' ? 'python output' : 'preview' ?></span>
          <span id="py-status" class="py-status-text"></span>
        </div>
        <iframe id="preview" class="fs-preview-frame <?= $lab['lang'] === 'py' ? 'hidden' : '' ?>"
                sandbox="allow-scripts"></iframe>
        <pre id="py-out" class="fs-py-out <?= $lab['lang'] !== 'py' ? 'hidden' : '' ?>"></pre>
      </div>

      <div class="fs-terminal">
        <div class="fs-panel-bar">
          <span class="bar-label">terminal</span>
          <span id="check-result" class="lab-check-result"></span>
          <button class="term-clear-btn" onclick="clearTerm()">clear</button>
        </div>
        <div class="fs-term-body" id="term"></div>
        <div class="fs-repl-row">
          <span class="fs-repl-prompt">~ $</span>
          <input class="fs-repl-input" id="repl"
                 placeholder="eval JS expression..."
                 autocomplete="off" onkeydown="replEval(event)">
        </div>
      </div>
    </div>

  </div>
</div>

<!-- COMPLETION TOAST -->
<div class="lab-toast" id="lab-toast">
  <div class="lab-toast-icon">✦</div>
  <div class="lab-toast-body">
    <div class="lab-toast-title">objective complete!</div>
    <div class="lab-toast-sub" id="toast-sub">well done, partner. Ddraig approves.</div>
  </div>
  <a href="#" class="btn btn-primary lab-toast-next" id="toast-next">next lab →</a>
</div>

<div class="fs-statusbar">
  <span id="fs-status" class="fs-status-ok">ready</span>
  <span id="fs-linecol" class="fs-stat-hide">ln 1, col 1</span>
  <span class="fs-stat-hide fs-stat-track">
    <?= htmlspecialchars($lab['track_name']) ?> · Lab <?= $lab['order_index'] ?>/10
  </span>
  <span>⚔ kuoh.academy / labs</span>
</div>

<script>
const LANG        = '<?= $lab['lang'] ?>';
const CHECK_MODE  = '<?= $lab['check_mode'] ?>';
const LAB_ID      = <?= $lab_id ?>;
const NEXT_LAB_ID = <?= $next_lab_id ?? 'null' ?>;

const editor      = document.getElementById('editor');
const preview     = document.getElementById('preview');
const pyOut       = document.getElementById('py-out');
const term        = document.getElementById('term');
const statusEl    = document.getElementById('fs-status');
const linecolEl   = document.getElementById('fs-linecol');
const runBtn      = document.getElementById('run-btn');
const pyStatus    = document.getElementById('py-status');
const pyLoader    = document.getElementById('py-loader');
const checkResult = document.getElementById('check-result');

let pyodide    = null;
let pyLoading  = false;
let lastOutput = '';

if (LANG === 'py') initPyodide();

async function runCode() {
  const code = editor.value.trim();
  if (!code) return;
  clearTerm();
  lastOutput = '';
  setStatus('running...', '');
  try {
    if      (LANG === 'js')   runJS(code);
    else if (LANG === 'html') runHTML(code);
    else if (LANG === 'css')  runHTML(code);
    else if (LANG === 'full') runFull(code);
    else if (LANG === 'py')   await runPython(code);
    if (LANG !== 'py') setStatus('done ✓', 'ok');
  } catch(e) { setStatus('error', 'err'); termLog('err', e.message); }
}

function runJS(code) {
  preview.srcdoc = `<html><body><script>
    window.onerror=(msg,_,line)=>{parent.postMessage({t:'err',msg:msg+' (line '+line+')'},'*');return true};
    ['log','warn','error','info'].forEach(k=>{
      const orig=console[k].bind(console);
      console[k]=(...args)=>{
        const msg=args.map(x=>{try{return typeof x==='object'?JSON.stringify(x,null,2):String(x)}catch(e){return String(x)}}).join(' ');
        parent.postMessage({t:k==='error'?'err':k,msg},'*');orig(...args);
      };
    });
    try{${code}}catch(e){parent.postMessage({t:'err',msg:e.message},'*')}
  <\/script></body></html>`;
}

function runHTML(code) { preview.srcdoc = code; }

function runFull(code) {
  const inject=`<script>window.onerror=(msg,_,l)=>{parent.postMessage({t:'err',msg:msg+' (ln '+l+')'},'*');return true};['log','warn','error','info'].forEach(k=>{const o=console[k].bind(console);console[k]=(...a)=>{const m=a.map(x=>{try{return typeof x==='object'?JSON.stringify(x):String(x)}catch(e){return String(x)}}).join(' ');parent.postMessage({t:k==='error'?'err':k,msg:m},'*');o(...a);};});<\/script>`;
  preview.srcdoc = code.includes('</head>') ? code.replace('</head>',inject+'</head>') : inject+code;
}

async function runPython(code) {
  if (!pyodide) { termLog('err','Python not ready yet.'); return; }
  pyodide.runPython(`import sys,io\nsys.stdout=io.StringIO()\nsys.stderr=io.StringIO()`);
  try {
    await pyodide.runPythonAsync(code);
    const out = pyodide.runPython('sys.stdout.getvalue()');
    const err = pyodide.runPython('sys.stderr.getvalue()');
    lastOutput = out;
    pyOut.textContent = out || '// no output';
    if (out) termLog('log', out.trim());
    if (err) { const el=document.createElement('span'); el.className='py-err'; el.textContent='\n'+err; pyOut.appendChild(el); termLog('warn',err.trim()); }
    setStatus('done ✓','ok');
  } catch(e) { const msg=e.message||String(e); pyOut.textContent=msg; termLog('err',msg); setStatus('error','err'); }
}

window.addEventListener('message', e => {
  if (!e.data||!e.data.t) return;
  termLog(e.data.t, e.data.msg);
  if (e.data.t !== 'err') lastOutput += e.data.msg + '\n';
  if (e.data.t === 'err') setStatus('error','err');
});

async function submitLab() {
  const btn = document.getElementById('submit-btn');
  if (btn) btn.disabled = true;
  const resp = await fetch('lab.php?id=' + LAB_ID, {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'action=complete&output=' + encodeURIComponent(lastOutput.trim())
  });
  const data = await resp.json();
  if (data.success) {
    checkResult.textContent = '✓ ' + data.message;
    checkResult.className   = 'lab-check-result lab-check-ok';
    showToast(data.next_lab_id);
    const footer = document.querySelector('.lab-instructions-footer');
    if (footer) {
      footer.innerHTML = '<div class="lab-complete-badge">✓ completed</div>' +
        (data.next_lab_id
          ? `<a href="lab.php?id=${data.next_lab_id}" class="btn btn-primary lab-next-btn">next lab →</a>`
          : '<div class="lab-track-done">✦ track complete!</div>');
    }
  } else {
    checkResult.textContent = '✖ ' + data.message;
    checkResult.className   = 'lab-check-result lab-check-err';
    if (btn) btn.disabled = false;
  }
}

function showToast(next_id) {
  const toast = document.getElementById('lab-toast');
  const toastNext = document.getElementById('toast-next');
  if (next_id) { toastNext.href = 'lab.php?id='+next_id; toastNext.classList.remove('hidden'); }
  else { toastNext.classList.add('hidden'); document.getElementById('toast-sub').textContent = '✦ track complete!'; }
  toast.classList.add('lab-toast--visible');
  setTimeout(() => toast.classList.remove('lab-toast--visible'), 6000);
}

async function initPyodide() {
  if (pyodide||pyLoading) return;
  pyLoading = true;
  pyLoader.classList.add('visible');
  pyStatus.textContent = 'python: summoning...';
  if (runBtn) runBtn.disabled = true;
  try {
    pyodide = await loadPyodide({ indexURL: 'libs/pyodide/' });
    pyStatus.textContent = 'python: ready ✓';
    pyStatus.style.color = 'var(--green)';
  } catch(e) {
    pyStatus.textContent = 'python: failed';
    pyStatus.style.color = 'var(--red)';
    termLog('err','Pyodide failed to load.');
  }
  pyLoader.classList.remove('visible');
  if (runBtn) runBtn.disabled = false;
  pyLoading = false;
}

function toggleInstructions() {
  const panel = document.getElementById('instructions-panel');
  const btn   = document.getElementById('toggle-btn');
  panel.classList.toggle('lab-instructions--collapsed');
  btn.textContent = panel.classList.contains('lab-instructions--collapsed') ? '▶' : '◀';
}

const glyphs = { log:'›', err:'✖', warn:'⚠', info:'ℹ', sys:'·' };
function termLog(type, msg) {
  const line = document.createElement('div');
  line.className = 'term-line t-'+(type||'log');
  line.innerHTML = `<span class="term-glyph">${glyphs[type]||'›'}</span><span class="term-msg">${escHtml(msg)}</span>`;
  term.appendChild(line);
  term.scrollTop = term.scrollHeight;
}
function clearTerm() { term.innerHTML=''; }
function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function setStatus(msg,state) {
  statusEl.textContent=msg;
  statusEl.className=state==='err'?'fs-status-err':state==='ok'?'fs-status-ok':'';
}
function replEval(e) {
  if(e.key!=='Enter') return;
  const inp=document.getElementById('repl');
  const expr=inp.value.trim(); if(!expr) return;
  termLog('sys','> '+expr);
  try { const r=eval(expr); if(r!==undefined) termLog('info',String(r)); } catch(err){termLog('err',err.message);}
  inp.value='';
}
editor.addEventListener('keydown', e => {
  if(e.key==='Tab'){e.preventDefault();const s=editor.selectionStart,en=editor.selectionEnd;editor.value=editor.value.substring(0,s)+'  '+editor.value.substring(en);editor.selectionStart=editor.selectionEnd=s+2;}
  if((e.ctrlKey||e.metaKey)&&e.key==='Enter'){e.preventDefault();runCode();}
});
editor.addEventListener('keyup',  updateCursor);
editor.addEventListener('click',  updateCursor);
function updateCursor() {
  const before=editor.value.substring(0,editor.selectionStart);
  const lines=before.split('\n');
  linecolEl.textContent=`ln ${lines.length}, col ${lines[lines.length-1].length+1}`;
}
</script>
</body>
</html>
