
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';

if (empty($_SESSION['achievements_csrf'])) {
    $_SESSION['achievements_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['achievements_csrf'], $token)) {
        $error = 'Invalid form submission. Please refresh the page.';

    } else {

        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {

            $title = trim($_POST['title'] ?? '');
            $issuer = trim($_POST['issuer'] ?? '');
            $dateReceived = trim($_POST['date_received'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($title === '') {
                $error = 'Achievement title is required.';

            } else {

                if ($action === 'add') {

                    $stmt = $pdo->prepare("
                        INSERT INTO achievements
                        (title, issuer, date_received, description)
                        VALUES (?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $title,
                        $issuer,
                        $dateReceived,
                        $description
                    ]);

                } else {

                    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

                    if (!$id || $id < 1) {
                        $error = 'Invalid achievement ID.';

                    } else {

                        $stmt = $pdo->prepare("
                            UPDATE achievements
                            SET title = ?, issuer = ?,
                                date_received = ?, description = ?
                            WHERE id = ?
                        ");

                        $stmt->execute([
                            $title,
                            $issuer,
                            $dateReceived,
                            $description,
                            $id
                        ]);
                    }
                }

                if ($error === '') {
                    header('Location: achievements.php?success=1');
                    exit;
                }
            }

        } elseif ($action === 'delete') {

            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                $error = 'Invalid achievement ID.';

            } else {

                $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
                $stmt->execute([$id]);

                header('Location: achievements.php?deleted=1');
                exit;
            }

        } else {
            $error = 'Invalid action.';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM achievements ORDER BY id ASC");
$achievementList = $stmt->fetchAll();

$success = '';

if (isset($_GET['success'])) {
    $success = 'Achievement saved successfully!';
} elseif (isset($_GET['deleted'])) {
    $success = 'Achievement deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Achievements Management</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        .achievement-admin {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }

        .achievement-admin input,
        .achievement-admin textarea {
            width: 100%;
            padding: 10px;
            margin: 6px 0 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font: inherit;
        }

        .achievement-admin textarea {
            min-height: 100px;
            resize: vertical;
        }

        .achievement-admin button {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            background: #222;
            color: white;
            cursor: pointer;
            margin: 4px;
        }

        .achievement-admin-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .achievement-message {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .achievement-success {
            background: #e1f5e5;
            color: #176b2c;
        }

        .achievement-error {
            background: #ffe5e5;
            color: #a00000;
        }

        .delete-button {
            background: #b42318 !important;
        }
    </style>
</head>

<body>

<main class="achievement-admin">

    <h1>Achievements Management</h1>
    <p>Manage the achievements displayed on your portfolio.</p>

    <?php if ($success): ?>
        <p class="achievement-message achievement-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="achievement-message achievement-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <h2>Add Achievement</h2>

    <form method="POST" action="achievements.php">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['achievements_csrf'], ENT_QUOTES, 'UTF-8') ?>">

        <input type="hidden" name="action" value="add">

        <label>Achievement Title</label>
        <input type="text" name="title" maxlength="255" required>

        <label>Issuer / Organization</label>
        <input type="text" name="issuer" maxlength="255">

        <label>Date Received</label>
        <input type="text" name="date_received" maxlength="100"
            placeholder="2026 or Present">

        <label>Description</label>
        <textarea name="description"></textarea>

        <button type="submit">Add Achievement</button>

    </form>

    <hr>

    <h2>Existing Achievements</h2>

    <?php if (empty($achievementList)): ?>

        <p>No achievements found.</p>

    <?php else: ?>

        <?php foreach ($achievementList as $achievement): ?>

            <div class="achievement-admin-card">

                <form method="POST" action="achievements.php">

                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['achievements_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="edit">

                    <input type="hidden" name="id"
                        value="<?= (int) $achievement['id'] ?>">

                    <label>Achievement Title</label>
                    <input type="text" name="title"
                        value="<?= htmlspecialchars($achievement['title'], ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="255" required>

                    <label>Issuer / Organization</label>
                    <input type="text" name="issuer"
                        value="<?= htmlspecialchars($achievement['issuer'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="255">

                    <label>Date Received</label>
                    <input type="text" name="date_received"
                        value="<?= htmlspecialchars($achievement['date_received'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="100">

                    <label>Description</label>
                    <textarea name="description"><?= htmlspecialchars($achievement['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                    <button type="submit">Save Changes</button>

                </form>

                <form method="POST" action="achievements.php"
                    onsubmit="return confirm('Are you sure you want to delete this achievement?');">

                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['achievements_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="delete">

                    <input type="hidden" name="id"
                        value="<?= (int) $achievement['id'] ?>">

                    <button type="submit" class="delete-button">
                        Delete Achievement
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <p>
        <a href="index.php">Back to Dashboard</a> |
        <a href="../resume.php" target="_blank">View Public Resume Page</a>
    </p>

</main>

</body>
</html>
