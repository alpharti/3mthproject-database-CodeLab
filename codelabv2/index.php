<?php
session_start();
if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
    header('Location: home.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CodeLab — Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="topbar">
    <a href="index.php" class="logo">&gt; code<span>lab</span></a>
    <span class="topbar-right">⚔ kuoh.academy / auth</span>
  </header>

  <main class="page">
    <div class="auth-card">

      <!-- decorative sigil -->
      <svg class="sigil" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="20" cy="20" r="18" stroke="#bc8cff" stroke-width="1"/>
        <polygon points="20,4 35,30 5,30" stroke="#d29922" stroke-width="1" fill="none"/>
        <polygon points="20,36 5,10 35,10" stroke="#58a6ff" stroke-width="1" fill="none"/>
        <circle cx="20" cy="20" r="3" fill="#7ee787"/>
      </svg>

      <div class="auth-header">
        <div class="auth-eyebrow">authentication</div>
        <div class="auth-title">welcome back,<br><em>dev.</em></div>
      </div>

      <div class="auth-body">

        <div class="duality">
          <span class="duality-dot" style="background:#bc8cff"></span>
          <span>angel</span>
          <span style="color:#2d1f4a">────</span>
          <span>⚔</span>
          <span style="color:#2d1f4a">────</span>
          <span>demon</span>
          <span class="duality-dot" style="background:#d29922"></span>
        </div>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="login.php" method="POST">
          <div class="field">
            <label for="Login">login</label>
            <input id="Login" type="text" name="Login" placeholder="your_username" required autocomplete="username">
          </div>
          <div class="field">
            <label for="Password">password</label>
            <input id="Password" type="password" name="Password" placeholder="••••••••" required autocomplete="current-password">
          </div>
          <button class="btn btn-primary" type="submit">enter the academy →</button>
        </form>

      </div>

      <div class="auth-foot">
        <span>no account yet?</span>
        <a href="registration.php">register →</a>
      </div>

    </div>
  </main>

</body>
</html>
