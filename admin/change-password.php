
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';
$success = '';

if (empty($_SESSION['password_csrf'])) {
    $_SESSION['password_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (
        !hash_equals($_SESSION['password_csrf'], $token)
    ) {
        $error = 'Invalid form submission. Please refresh the page.';

    } elseif (
        $currentPassword === '' ||
        $newPassword === '' ||
        $confirmPassword === ''
    ) {
        $error = 'Please complete all fields.';

    } elseif (strlen($newPassword) < 8) {
        $error = 'Your new password must be at least 8 characters long.';

    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Your new passwords do not match.';

    } elseif ($currentPassword === $newPassword) {
        $error = 'Your new password must be different from your current password.';

    } else {

        $stmt = $pdo->prepare("
            SELECT password_hash
            FROM admins
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($currentPassword, $admin['password_hash'])) {

            $error = 'Your current password is incorrect.';

        } else {

            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = $pdo->prepare("
                UPDATE admins
                SET password_hash = ?
                WHERE id = ?
            ");

            $update->execute([
                $newPasswordHash,
                $_SESSION['admin_id']
            ]);

            session_regenerate_id(true);

            $_SESSION['password_csrf'] = bin2hex(random_bytes(32));

            $success = 'Your password has been changed successfully!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password | Admin</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">

    <script src="../js/script.js" defer></script>
</head>

<body class="admin-body">

    <main class="admin-page">

        <header class="admin-header">

            <h1>Change Password</h1>

            <button
                type="button"
                class="theme-toggle"
                aria-label="Toggle dark mode">
                🌙
            </button>

        </header>

        <section class="admin-panel">

            <h2>Update Your Password</h2>

            <p>Enter your current password and choose a new one.</p>

            <?php if ($error): ?>
                <div class="admin-message admin-error">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="admin-message admin-success">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="admin-form">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['password_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="admin-form-group">

                    <label for="current_password">Current Password</label>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Enter current password"
                        autocomplete="current-password"
                        required>

                </div>

                <div class="admin-form-group">

                    <label for="new_password">New Password</label>

                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter new password"
                        minlength="8"
                        autocomplete="new-password"
                        required>

                </div>

                <div class="admin-form-group">

                    <label for="confirm_password">Confirm New Password</label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm new password"
                        minlength="8"
                        autocomplete="new-password"
                        required>

                </div>

                <button type="submit" class="admin-submit-button">
                    Save New Password
                </button>

            </form>

            <div class="admin-footer-actions">
                <a href="index.php" class="admin-button">
                    Back to Dashboard
                </a>
            </div>

        </section>

    </main>

</body>
</html>
