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
  <title>CodeLab — Home</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="topbar">
    <a href="home.php" class="logo">&gt; code<span>lab</span></a>
    <nav style="display:flex;gap:4px;align-items:center;">
      <a href="home.php"   style="font-family:var(--font-mono);font-size:12px;color:#7ee787;background:#161b22;padding:5px 10px;border-radius:6px;text-decoration:none;">home</a>
      <a href="labs.php"    style="font-family:var(--font-mono);font-size:12px;color:#8b949e;padding:5px 10px;border-radius:6px;text-decoration:none;">tracks</a>
      <a href="labs.php"      style="font-family:var(--font-mono);font-size:12px;color:#8b949e;padding:5px 10px;border-radius:6px;text-decoration:none;">labs</a>
      <a href="freestyle.php" style="font-family:var(--font-mono);font-size:12px;color:#bc8cff;border:1px solid #2d1f4a;padding:5px 10px;border-radius:6px;text-decoration:none;">✦ freestyle</a>
    </nav>
    <div style="display:flex;align-items:center;gap:14px;">
      <span style="font-family:var(--font-mono);font-size:11px;background:#161b22;border:1px solid #21262d;padding:5px 12px;border-radius:6px;color:#8b949e;">
        <?= $username ?>@lab ~
      </span>
      <a href="logout.php" class="logout-link">logout</a>
    </div>
  </header>

  <main class="page" style="align-items:flex-start;padding-top:88px;">
    <div class="home-wrap">

      <!-- hero -->
      <div style="margin-bottom:36px;">
        <div class="home-prompt">welcome back, <?= $username ?>. ready to ship?</div>
        <h1 class="home-title">learn by<br><em>building.</em></h1>
        <p class="home-sub">Hands-on coding labs. Real projects. Zero fluff.<br>Write code, break things, understand why.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <a href="labs.php" class="btn btn-primary" style="width:auto;display:inline-block;">start a lab</a>
          <a href="labs.php" class="btn btn-ghost" style="width:auto;display:inline-block;">browse tracks →</a>
        </div>
      </div>

      <!-- tracks -->
      <div id="tracks" style="margin-bottom:32px;">
        <div class="home-section-title">learning tracks</div>
        <div class="track-grid">

          <a href="#" class="track-card">
            <div class="track-top" style="background:#238636"></div>
            <span class="track-icon">{ }</span>
            <div class="track-name">JavaScript</div>
            <div class="track-desc">From closures to async. Master the language.</div>
            <div class="track-meta">
              <span>24 labs</span>
              <span class="badge badge-green">beginner</span>
            </div>
          </a>

          <a href="#" class="track-card">
            <div class="track-top" style="background:#58a6ff"></div>
            <span class="track-icon">&lt;/&gt;</span>
            <div class="track-name">Web Dev</div>
            <div class="track-desc">HTML, CSS, DOM — build things that render.</div>
            <div class="track-meta">
              <span>18 labs</span>
              <span class="badge badge-blue">popular</span>
            </div>
          </a>

          <a href="#" class="track-card">
            <div class="track-top" style="background:#d29922"></div>
            <span class="track-icon">[ ]</span>
            <div class="track-name">Data Structures</div>
            <div class="track-desc">Arrays, trees, graphs. The hard stuff.</div>
            <div class="track-meta">
              <span>32 labs</span>
              <span class="badge badge-amber">advanced</span>
            </div>
          </a>

          <a href="#" class="track-card">
            <div class="track-top" style="background:#f85149"></div>
            <span class="track-icon">%%</span>
            <div class="track-name">Algorithms</div>
            <div class="track-desc">Sort, search, and conquer complexity.</div>
            <div class="track-meta">
              <span>28 labs</span>
              <span class="badge badge-red">hard</span>
            </div>
          </a>

        </div>
      </div>

      <!-- divider -->
      <div style="height:1px;background:#1e2530;margin-bottom:28px;"></div>

      <!-- freestyle -->
      <div id="freestyle" style="margin-bottom:32px;">
        <div class="home-section-title">open sandbox</div>
        <a href="freestyle.php" class="freestyle-banner">
          <div class="freestyle-glow"></div>
          <div class="freestyle-icon">~</div>
          <div class="freestyle-body">
            <h3>freestyle mode</h3>
            <p>No tasks. No rules. Just an editor and a blank canvas.<br>Experiment, prototype, and see what happens.</p>
          </div>
          <div class="freestyle-arrow">→</div>
        </a>
      </div>

      <!-- footer -->
      <div style="height:1px;background:#1e2530;margin-bottom:16px;"></div>
      <div style="display:flex;justify-content:space-between;font-size:11px;color:#3d444d;padding-bottom:24px;">
        <span>codelab v2.1.0 · built for devs · ⚔ kuoh.academy</span>
        <div style="display:flex;gap:16px;">
          <a href="#" style="color:#3d444d;text-decoration:none;">docs</a>
          <a href="#" style="color:#3d444d;text-decoration:none;">github</a>
          <a href="#" style="color:#3d444d;text-decoration:none;">discord</a>
        </div>
      </div>

    </div>
  </main>

</body>
</html>
