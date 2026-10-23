<?php
session_start();
require 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = 'Use a username of at least 3 characters, a valid email, and a password of at least 6 characters.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = 'That username or email may already be registered.';
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Join Snake Bite</title><link rel="stylesheet" href="style.css"></head>
<body class="auth-page"><main class="auth-card"><div class="logo">🐍 SNAKE BITE</div><p class="eyebrow">CREATE YOUR PLAYER ACCOUNT</p><h1>Join the arena</h1>
<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post"><label>Username</label><input name="username" required minlength="3" maxlength="50" autocomplete="username"><label>Email</label><input type="email" name="email" required autocomplete="email"><label>Password</label><input type="password" name="password" required minlength="6" autocomplete="new-password"><button class="primary full">Create account</button></form>
<p class="muted">Already registered? <a href="login.php">Log in</a></p></main></body></html>
