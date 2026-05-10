<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: index.php');
    exit();
}
require_once 'db.php';
$username = htmlspecialchars($_SESSION['login']);
$user_id  = (int)$_SESSION['id'];

$connect = new mysqli($host, $db_user, $db_password, $db_name);

$tracks_query = $connect->prepare("
    SELECT t.*,
           COUNT(l.id) AS total_labs,
           SUM(CASE WHEN up.completed = 1 THEN 1 ELSE 0 END) AS completed_labs
    FROM tracks t
    LEFT JOIN labs l ON l.track_id = t.id
    LEFT JOIN user_progress up ON up.lab_id = l.id AND up.user_id = ?
    GROUP BY t.id
    ORDER BY t.order_index ASC
");
$tracks_query->bind_param("i", $user_id);
$tracks_query->execute();
$tracks = $tracks_query->get_result()->fetch_all(MYSQLI_ASSOC);

$labs_query = $connect->prepare("
    SELECT l.*, t.name AS track_name, t.slug AS track_slug, t.color AS track_color,
           COALESCE(up.completed, 0) AS completed
    FROM labs l
    JOIN tracks t ON t.id = l.track_id
    LEFT JOIN user_progress up ON up.lab_id = l.id AND up.user_id = ?
    ORDER BY t.order_index ASC, l.order_index ASC
");
$labs_query->bind_param("i", $user_id);
$labs_query->execute();
$labs = $labs_query->get_result()->fetch_all(MYSQLI_ASSOC);
$connect->close();

$labs_by_track = [];
foreach ($labs as $lab) {
    $labs_by_track[$lab['track_slug']][] = $lab;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CodeLab — Labs</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
  <a href="home.php" class="logo">&gt; code<span>lab</span></a>
  <nav class="topbar-nav">
    <a href="home.php"      class="nav-link">home</a>
    <a href="labs.php"      class="nav-link nav-link--active">labs</a>
    <a href="freestyle.php" class="nav-link nav-link--freestyle">✦ freestyle</a>
  </nav>
  <div class="topbar-right-group">
    <span class="fs-user-badge"><?= $username ?>@gremory ~</span>
    <a href="logout.php" class="logout-link">logout</a>
  </div>
</header>

<main class="page labs-page">
  <div class="labs-wrap">

    <div class="labs-hero">
      <div class="home-prompt">choose your path, <?= $username ?>.</div>
      <h1 class="home-title">learning <em>tracks.</em></h1>
      <p class="home-sub">Complete labs in order. Each track runs from Lab 1 to Lab 10.<br>Finish a lab to unlock the next one.</p>
    </div>

    <?php foreach ($tracks as $track):
      $slug       = $track['slug'];
      $total      = (int)$track['total_labs'];
      $completed  = (int)$track['completed_labs'];
      $pct        = $total > 0 ? round(($completed / $total) * 100) : 0;
      $track_labs = $labs_by_track[$slug] ?? [];
    ?>
    <div class="track-section" style="--track-color:<?= $track['color'] ?>">
      <div class="track-section-header">
        <div class="track-section-icon">
          <?= htmlspecialchars($track['icon']) ?>
        </div>
        <div>
          <div class="track-section-name"><?= htmlspecialchars($track['name']) ?></div>
          <div class="track-section-desc"><?= htmlspecialchars($track['description']) ?></div>
        </div>
        <div class="track-section-meta">
          <span class="track-difficulty"><?= htmlspecialchars($track['difficulty']) ?></span>
          <div class="track-section-progress">
            <div class="progress-label"><?= $completed ?>/<?= $total ?> completed</div>
            <div class="progress-bar">
              <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $track['color'] ?>"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="lab-list">
        <?php
        $prev_completed = true;
        foreach ($track_labs as $lab):
          $is_completed = (bool)$lab['completed'];
          $is_locked    = !$prev_completed && !$is_completed;
          $prev_completed = $is_completed;
          $state_class  = $is_completed ? 'lab-card--done' : ($is_locked ? 'lab-card--locked' : '');
        ?>
        <a href="<?= $is_locked ? '#' : 'lab.php?id='.$lab['id'] ?>"
           class="lab-card <?= $state_class ?>">
          <div class="lab-card-top">
            <span class="lab-card-num <?= $is_completed ? 'lab-card-num--done' : '' ?>">
              <?= $is_completed ? '✓ done' : 'lab '.str_pad($lab['order_index'], 2, '0', STR_PAD_LEFT) ?>
            </span>
            <?php if ($is_locked): ?>
              <span class="lab-card-lock">🔒</span>
            <?php else: ?>
              <span class="lab-card-lang badge badge-<?= $lab['lang'] === 'py' ? 'blue' : ($lab['lang'] === 'js' ? 'amber' : 'purple') ?>">
                <?= strtoupper($lab['lang']) ?>
              </span>
            <?php endif; ?>
          </div>
          <div class="lab-card-title"><?= htmlspecialchars($lab['title']) ?></div>
          <div class="lab-card-desc"><?= htmlspecialchars($lab['description']) ?></div>
          <div class="lab-card-footer">
            <?= $is_completed ? '✓ completed' : ($is_locked ? '🔒 locked' : '→ start') ?>
          </div>
        </a>
        <?php endforeach; ?>

        <?php if (empty($track_labs)): ?>
        <div class="lab-empty">// no labs yet — coming soon.</div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>

  </div>
</main>

</body>
</html>
