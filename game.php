<?php
require 'auth.php';
require_login();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Play Snake Bite</title><link rel="stylesheet" href="style.css"></head>
<body><header class="topbar"><a class="logo" href="dashboard.php">🐍 SNAKE BITE</a><nav><a href="dashboard.php">← Dashboard</a><a href="logout.php">Log out</a></nav></header>
<main class="game-layout"><section class="game-info"><p class="eyebrow">ARCADE MODE · 20 × 20</p><h1>Eat. Grow. Survive.</h1><p class="muted">Use the arrow keys or the buttons below. Don't hit the wall or your own tail.</p>
<div class="game-stats"><div class="stat"><span>🍎 Food / Score</span><strong id="score">0</strong></div><div class="stat"><span>🐍 Snake length</span><strong id="length">3 / 400</strong></div><div class="stat"><span>💥 Deaths this run</span><strong id="deaths">0</strong></div></div>
<div class="mode-picker"><label for="modeSelect">Game mode</label><select id="modeSelect"><option value="easy">Easy · Slow</option><option value="medium" selected>Medium · Normal</option><option value="hard">Hard · Fast</option></select></div>
<div class="controls"><button id="startBtn" class="primary">▶ Start / Resume</button><button id="pauseBtn" class="secondary">Ⅱ Pause</button><button id="resetBtn" class="secondary">↻ New game</button></div>
<div class="dpad" aria-label="Direction controls"><span></span><button data-dir="up" aria-label="Up">▲</button><span></span><button data-dir="left" aria-label="Left">◀</button><button data-dir="down" aria-label="Down">▼</button><button data-dir="right" aria-label="Right">▶</button></div>
<p class="hint">Keyboard: ↑ ↓ ← → · Space: pause</p><a href="dashboard.php" class="text-link">View your badges →</a></section>
<section class="board-panel"><div class="board-top"><span>NEON GARDEN</span><span id="status">READY</span></div><div id="board" class="board" role="img" aria-label="20 by 20 snake game board"></div><p class="board-caption">Collect food to grow. Reach 200, 300, and 400 cells for rewards.</p></section></main>
<div id="modal" class="modal hidden" role="dialog" aria-modal="true"><div class="modal-card"><div id="modalIcon" class="modal-icon">🏆</div><p class="eyebrow">SNAKE BITE</p><h2 id="modalTitle">Achievement unlocked!</h2><p id="modalText">You did it.</p><button id="modalBtn" class="primary">Continue</button></div></div>
<script src="game.js"></script></body></html>
