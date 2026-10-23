<?php
require 'auth.php';
require_login();
require 'db.php';

$stmt = $pdo->prepare('SELECT COALESCE(MAX(score),0) best_score, COALESCE(SUM(score),0) total_food, COALESCE(SUM(deaths),0) deaths, COUNT(*) games FROM game_records WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$stats = $stmt->fetch();
$stmt = $pdo->prepare('SELECT badge_code, unlocked_at FROM user_badges WHERE user_id = ? ORDER BY unlocked_at');
$stmt->execute([$_SESSION['user_id']]);
$badges = $stmt->fetchAll();
$badgeNames = ['half' => ['Halfway Hero','Reach 50% of the board'], 'three_quarters' => ['Grid Master','Reach 75% of the board'], 'full' => ['Snake Legend','Fill all 400 cells']];
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Snake Bite Dashboard</title><link rel="stylesheet" href="style.css"></head>
<body><header class="topbar"><a class="logo" href="dashboard.php">🐍 SNAKE BITE</a><nav><span>Hi, <?= htmlspecialchars($_SESSION['username']) ?></span><a href="logout.php">Log out ↗</a></nav></header>
<main class="container"><section class="hero"><div><p class="eyebrow">YOUR ARCADE, YOUR RECORD</p><h1>Ready to slither?</h1><p class="muted">Eat food, grow longer, unlock every badge.</p><a class="primary button-link" href="game.php">▶ Play Snake</a></div><div class="hero-snake" aria-hidden="true">🟩🟩🟩🐍</div></section>
<section class="stats"><article class="stat"><span>🏆 Best score</span><strong><?= (int)$stats['best_score'] ?></strong></article><article class="stat"><span>🍎 Total food</span><strong><?= (int)$stats['total_food'] ?></strong></article><article class="stat"><span>💥 Deaths</span><strong><?= (int)$stats['deaths'] ?></strong></article><article class="stat"><span>🎮 Games played</span><strong><?= (int)$stats['games'] ?></strong></article></section>
<section class="panel"><div class="section-heading"><div><p class="eyebrow">COLLECTION</p><h2>Your achievement badges</h2></div><span class="badge-count"><?= count($badges) ?>/3 unlocked</span></div><div class="badges">
<?php foreach ($badgeNames as $code => $info): $unlocked = false; foreach ($badges as $b) { if ($b['badge_code'] === $code) $unlocked = true; } ?>
<article class="badge-card <?= $unlocked ? 'unlocked' : 'locked' ?>"><div class="badge-icon"><?= $code === 'half' ? '🥉' : ($code === 'three_quarters' ? '🥈' : '👑') ?></div><h3><?= htmlspecialchars($info[0]) ?></h3><p><?= htmlspecialchars($info[1]) ?></p><span><?= $unlocked ? '✓ Unlocked' : '🔒 Locked' ?></span></article>
<?php endforeach; ?></div></section><p class="footnote">Snake Bite · PHP + MySQL mini project · 20 × 20 grid</p></main></body></html>
