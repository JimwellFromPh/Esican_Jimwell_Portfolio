
<?php
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
}

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['login_csrf']) ||
        !hash_equals($_SESSION['login_csrf'], $csrfToken)
    ) {
        $error = 'Invalid form submission. Please refresh the page.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Remove attempts older than 15 minutes
        $cleanup = $pdo->prepare("
            DELETE FROM login_attempts
            WHERE attempted_at < NOW() - INTERVAL 15 MINUTE
        ");
        $cleanup->execute();

        // Count recent failed attempts
        $attemptStmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM login_attempts
            WHERE ip_address = ?
            AND attempted_at >= NOW() - INTERVAL 15 MINUTE
        ");
        $attemptStmt->execute([$ipAddress]);

        $attemptCount = (int) $attemptStmt->fetchColumn();

        if ($attemptCount >= 5) {

            $error = 'Too many failed attempts. Please try again after 15 minutes.';

        } else {

            // Find admin account
            $stmt = $pdo->prepare("
                SELECT id, username, password_hash
                FROM admins
                WHERE username = ?
                LIMIT 1
            ");

            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {

                // Clear failed attempts after successful login
                $deleteAttempts = $pdo->prepare("
                    DELETE FROM login_attempts
                    WHERE ip_address = ?
                ");
                $deleteAttempts->execute([$ipAddress]);

                session_regenerate_id(true);

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];

                unset($_SESSION['login_csrf']);

                header('Location: index.php');
                exit;

            } else {

                // Record failed login
                $insertAttempt = $pdo->prepare("
                    INSERT INTO login_attempts (ip_address)
                    VALUES (?)
                ");
                $insertAttempt->execute([$ipAddress]);

                $error = 'Incorrect username or password.';
            }
        }
    }
}

if (empty($_SESSION['login_csrf'])) {
    $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Jimwell's Portfolio</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <main class="contact-section container">

        <div class="contact-form-container">

            <h2>Admin Login</h2>

            <p>Sign in to manage your portfolio.</p>

            <?php if ($error): ?>
                <p class="form-error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="login.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['login_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">

                    <label for="username">Username</label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        maxlength="50"
                        autocomplete="username"
                        required>

                </div>

                <div class="form-group">

                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required>

                </div>

                <button type="submit" class="contact-button">
                    Login
                </button>

            </form>

        </div>

    </main>

</body>

</html>
