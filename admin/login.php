
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

        $cleanup = $pdo->prepare("
            DELETE FROM login_attempts
            WHERE attempted_at < NOW() - INTERVAL 15 MINUTE
        ");
        $cleanup->execute();

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

            $stmt = $pdo->prepare("
                SELECT id, username, password_hash
                FROM admins
                WHERE username = ?
                LIMIT 1
            ");

            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {

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

    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
        }

        .login-theme {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
        }

        .login-theme .theme-toggle {
            border: 1px solid var(--border-color, #303442);
            border-radius: 50px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            padding: 40px;
            border-radius: 18px;
            background: var(--card-bg, #191c26);
            border: 1px solid var(--border-color, #303442);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .login-description {
            text-align: center;
            margin-bottom: 30px;
            opacity: 0.75;
        }

        .login-card .form-group {
            margin-bottom: 20px;
        }

        .login-card label {
            display: block;
            margin-bottom: 8px;
        }

        .login-card input {
            width: 100%;
            box-sizing: border-box;
        }

        .login-card .contact-button {
            width: 100%;
            margin-top: 10px;
        }

        .login-back {
            display: block;
            text-align: center;
            margin-top: 25px;
            text-decoration: none;
            font-size: 14px;
        }

        .login-error {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: rgba(220, 53, 69, 0.12);
            color: #e05b68;
            text-align: center;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 22px;
            }
        }
    </style>
</head>

<body>

    <main class="login-page">

        <div class="login-theme">
            <button
                type="button"
                class="theme-toggle"
                aria-label="Toggle dark mode">
                🌙
            </button>
        </div>

        <div class="login-card">

            <h2>Admin Login</h2>

            <p class="login-description">
                Sign in to manage your portfolio.
            </p>

            <?php if ($error): ?>
                <div class="login-error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" autocomplete="off">

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

            <a href="../index.php" class="login-back">
                ← Back to Portfolio
            </a>

        </div>

    </main>

    <script src="../js/script.js" defer></script>

</body>
</html>
