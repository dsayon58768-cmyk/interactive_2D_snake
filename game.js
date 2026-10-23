const SIZE = 20;
const board = document.getElementById('board');
const scoreEl = document.getElementById('score');
const lengthEl = document.getElementById('length');
const deathsEl = document.getElementById('deaths');
const statusEl = document.getElementById('status');
const modal = document.getElementById('modal');
const modeSelect = document.getElementById('modeSelect');
const cells = [];
const speeds = { easy: 220, medium: 140, hard: 75 };
let snake, food, direction, nextDirection, score, maxLength, deaths = 0;
let timer, running, ended, saved, restartTimer;

for (let i = 0; i < SIZE * SIZE; i++) {
  const cell = document.createElement('div');
  cell.className = 'cell';
  board.appendChild(cell);
  cells.push(cell);
}

function randomFood() {
  const free = [];
  for (let i = 0; i < SIZE * SIZE; i++) {
    if (!snake.some(part => part.x + part.y * SIZE === i)) free.push(i);
  }
  if (!free.length) return null;
  const index = free[Math.floor(Math.random() * free.length)];
  return { x: index % SIZE, y: Math.floor(index / SIZE) };
}
function resetGame(keepDeaths = true) {
  clearInterval(timer);
  clearTimeout(restartTimer);
  snake = [{x: 9, y: 10}, {x: 8, y: 10}, {x: 7, y: 10}];
  direction = {x: 1, y: 0};
  nextDirection = {x: 1, y: 0};
  score = 0; maxLength = 3;
  if (!keepDeaths) deaths = 0;
  food = randomFood(); running = false; ended = false; saved = false;
  modal.classList.add('hidden');
  statusEl.textContent = 'READY';
  updateStats(); draw();
}
function updateStats() {
  scoreEl.textContent = score;
  lengthEl.textContent = `${snake.length} / 400`;
  deathsEl.textContent = deaths;
}
function draw() {
  cells.forEach(c => c.className = 'cell');
  if (food) cells[food.x + food.y * SIZE].classList.add('food');
  snake.forEach((part, i) => {
    cells[part.x + part.y * SIZE].classList.add(i === 0 ? 'snake-head' : 'snake-body');
  });
}
function setDirection(name) {
  const dirs = {up:{x:0,y:-1}, down:{x:0,y:1}, left:{x:-1,y:0}, right:{x:1,y:0}};
  const chosen = dirs[name];
  if (!chosen) return;
  if (chosen.x === -direction.x && chosen.y === -direction.y) return;
  nextDirection = chosen;
  if (!running && !ended) startGame();
}
function startGame() {
  if (ended || running) return;
  running = true; statusEl.textContent = 'PLAYING';
  clearInterval(timer);
  timer = setInterval(step, speeds[modeSelect.value] || speeds.medium);
}
function pauseGame() {
  if (!running) return;
  running = false; clearInterval(timer); statusEl.textContent = 'PAUSED';
}
function step() {
  direction = nextDirection;
  const head = {x: snake[0].x + direction.x, y: snake[0].y + direction.y};
  const eating = food && head.x === food.x && head.y === food.y;
  const bodyToCheck = eating ? snake : snake.slice(0, -1);
  if (head.x < 0 || head.x >= SIZE || head.y < 0 || head.y >= SIZE ||
      bodyToCheck.some(p => p.x === head.x && p.y === head.y)) {
    deaths++;
    updateStats();
    endGame(false, true);
    return;
  }
  snake.unshift(head);
  if (eating) {
    score++; maxLength = Math.max(maxLength, snake.length);
    food = randomFood();
    if (snake.length >= 200 && !sessionStorage.getItem('halfSeen')) {
      sessionStorage.setItem('halfSeen', '1');
      showModal('🥉', 'Halfway Hero!', 'Amazing! Your snake has covered 50% of the board.');
    } else if (snake.length >= 300 && !sessionStorage.getItem('threeSeen')) {
      sessionStorage.setItem('threeSeen', '1');
      showModal('🥈', 'Grid Master!', 'Fantastic! You have covered 75% of the board.');
    }
    if (!food) {
      endGame(true, false);
      return;
    }
  } else {
    snake.pop();
  }
  updateStats(); draw();
}
async function saveRun(won, died) {
  try {
    const response = await fetch('save_game.php', {
      method: 'POST', headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({score, deaths: died ? 1 : 0, died, max_length: maxLength, won})
    });
    return await response.json();
  } catch (e) {
    console.error('Could not save game', e);
    return {ok: false, earned: []};
  }
}
async function endGame(won, died) {
  running = false; ended = true; clearInterval(timer);
  statusEl.textContent = won ? 'VICTORY' : 'RESTARTING…';
  if (saved) return;
  saved = true;
  const result = await saveRun(won, died);

  if (died) {
    // Start a fresh run automatically while keeping the death counter visible.
    statusEl.textContent = 'NEW RUN…';
    restartTimer = setTimeout(() => {
      resetGame(true);
      startGame();
    }, 900);
    return;
  }
  if (result.ok && result.earned && result.earned.length) {
    showModal('🏆', 'Badge unlocked!', result.earned.join(', ') + ' — check your dashboard!');
    return;
  }
  showModal('👑', 'You filled the grid!', 'Incredible! You filled every cell. Your run has been saved.');
}
function showModal(icon, title, text) {
  document.getElementById('modalIcon').textContent = icon;
  document.getElementById('modalTitle').textContent = title;
  document.getElementById('modalText').textContent = text;
  modal.classList.remove('hidden');
}
document.getElementById('startBtn').addEventListener('click', startGame);
document.getElementById('pauseBtn').addEventListener('click', pauseGame);
document.getElementById('resetBtn').addEventListener('click', () => resetGame(true));
document.getElementById('modalBtn').addEventListener('click', () => modal.classList.add('hidden'));
modeSelect.addEventListener('change', () => {
  if (running) {
    clearInterval(timer);
    timer = setInterval(step, speeds[modeSelect.value] || speeds.medium);
  }
});
document.querySelectorAll('[data-dir]').forEach(btn => btn.addEventListener('click', () => setDirection(btn.dataset.dir)));
document.addEventListener('keydown', e => {
  const keys = {ArrowUp:'up', ArrowDown:'down', ArrowLeft:'left', ArrowRight:'right'};
  if (keys[e.key]) { e.preventDefault(); setDirection(keys[e.key]); }
  if (e.code === 'Space') { e.preventDefault(); running ? pauseGame() : startGame(); }
});
resetGame(false);
