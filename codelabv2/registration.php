<?php 
require_once "db.php";

mysqli_report(MYSQLI_REPORT_STRICT);

session_start();

if (isset($_POST['email'])) {
    $verified = true;
    $pass1 = $_POST['password'];
    $pass2 = $_POST['confirm'];
    $nick  = $_POST['nick'];

    if (strlen($nick) < 3 || strlen($nick) > 20) {
        $verified = false;
        $_SESSION['e_nick'] = "Nick must be between 3 and 20 characters long.";
    }
    if (ctype_alnum($nick) == false) {
        $verified = false;
        $_SESSION['e_nick'] = "Nick can only contain letters and numbers.";
    }
    if ($pass1 != $pass2) {
        $verified = false;
        $_SESSION['e_password'] = "Passwords do not match.";
    } elseif (strlen($pass1) < 8 || strlen($pass1) > 20) {
        $verified = false;
        $_SESSION['e_password'] = "Password must be between 8 and 20 characters long.";
    }

    $email  = $_POST['email'];
    $emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
    if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)) {
        $verified = false;
        $_SESSION['e_email'] = "Please provide a valid email address.";
    }

    $pass_hash = password_hash($pass1, PASSWORD_DEFAULT);

    if (!isset($_POST['terms'])) {
        $verified = false;
        $_SESSION['e_terms'] = "You must accept the terms and conditions.";
    }

    try {
        $connect = new mysqli($host, $db_user, $db_password, $db_name);
        if ($connect->connect_errno != 0) {
            throw new Exception(mysqli_connect_errno());
        } else {
            $stmt = $connect->prepare("SELECT id FROM users WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $verified = false;
                $_SESSION['e_email'] = "An account with this email already exists.";
            }
            $stmt->close();

            if ($verified) {
                $stmt = $connect->prepare("SELECT id FROM users WHERE login=?");
                $stmt->bind_param("s", $nick);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $verified = false;
                    $_SESSION['e_nick'] = "An account with this nickname already exists.";
                }
                $stmt->close();

                if ($verified) {
                    $stmt = $connect->prepare("INSERT INTO users (email, login, password) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $email, $nick, $pass_hash);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            $connect->close();
        }
    } catch (Exception $e) {
        $_SESSION['e_server'] = "Server error. Please try again later.";
    }

    if ($verified) {
        $_SESSION['registered'] = true;
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CodeLab — Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="topbar">
    <a href="index.php" class="logo">&gt; code<span>lab</span></a>
    <span class="topbar-right">⚔ kuoh.academy / register</span>
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
        <div class="auth-eyebrow">new account</div>
        <div class="auth-title">join the<br><em>academy.</em></div>
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

        <?php if (isset($_SESSION['e_server'])): ?>
          <div class="error"><?= $_SESSION['e_server']; unset($_SESSION['e_server']); ?></div>
        <?php endif; ?>

        <form id="register-form" method="POST">

          <div class="field">
            <label for="nick">nickname</label>
            <input id="nick" type="text" name="nick" placeholder="issei_hyoudou" required
                   value="<?= isset($_POST['nick']) ? htmlspecialchars($_POST['nick']) : '' ?>">
            <?php if (isset($_SESSION['e_nick'])): ?>
              <div class="error"><?= $_SESSION['e_nick']; unset($_SESSION['e_nick']); ?></div>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="email">email</label>
            <input id="email" type="email" name="email" placeholder="you@kuoh.academy" required
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            <?php if (isset($_SESSION['e_email'])): ?>
              <div class="error"><?= $_SESSION['e_email']; unset($_SESSION['e_email']); ?></div>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="password">password</label>
            <input id="password" type="password" name="password" placeholder="••••••••" required>
          </div>

          <div class="field">
            <label for="confirm">confirm password</label>
            <input id="confirm" type="password" name="confirm" placeholder="••••••••" required>
            <?php if (isset($_SESSION['e_password'])): ?>
              <div class="error"><?= $_SESSION['e_password']; unset($_SESSION['e_password']); ?></div>
            <?php endif; ?>
          </div>

          <div class="field-check">
            <input type="checkbox" id="terms" name="terms">
            <label for="terms">I accept the <a href="#">Terms &amp; Conditions</a></label>
          </div>
          <?php if (isset($_SESSION['e_terms'])): ?>
            <div class="error" style="margin-top:-10px;margin-bottom:14px;"><?= $_SESSION['e_terms']; unset($_SESSION['e_terms']); ?></div>
          <?php endif; ?>

          <button class="btn btn-primary" type="submit" id="submit-btn">create account →</button>
        </form>

      </div>

      <div class="auth-foot">
        <span>already have an account?</span>
        <a href="index.php">login →</a>
      </div>

    </div>
  </main>

</body>
</html>
