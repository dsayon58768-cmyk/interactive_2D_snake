<?php
require 'auth.php';
require_login();
require 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Invalid game data']);
    exit;
}
$score = max(0, min(397, (int)($data['score'] ?? 0)));
$deaths = !empty($data['died']) ? 1 : 0;
$length = max(3, min(400, (int)($data['max_length'] ?? 3)));
$won = !empty($data['won']) ? 1 : 0;

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('INSERT INTO game_records (user_id, score, deaths, max_length, won) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$_SESSION['user_id'], $score, $deaths, $length, $won]);

    $earned = [];
    $milestones = [
        200 => ['half', 'Halfway Hero'],
        300 => ['three_quarters', 'Grid Master'],
        400 => ['full', 'Snake Legend']
    ];
    foreach ($milestones as $threshold => $badge) {
        if ($length >= $threshold) {
            $stmt = $pdo->prepare('INSERT IGNORE INTO user_badges (user_id, badge_code) VALUES (?, ?)');
            $stmt->execute([$_SESSION['user_id'], $badge[0]]);
            if ($stmt->rowCount() > 0) $earned[] = $badge[1];
        }
    }
    $pdo->commit();
    echo json_encode(['ok' => true, 'earned' => $earned]);
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Could not save this game.']);
}
