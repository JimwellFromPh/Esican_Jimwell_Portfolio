
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';

if (empty($_SESSION['experience_csrf'])) {
    $_SESSION['experience_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['experience_csrf'], $token)) {
        $error = 'Invalid form submission. Please refresh the page.';
    } else {

        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {

            $position = trim($_POST['position'] ?? '');
            $company = trim($_POST['company'] ?? '');
            $startDate = trim($_POST['start_date'] ?? '');
            $endDate = trim($_POST['end_date'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($position === '' || $company === '') {
                $error = 'Position and company are required.';
            } else {

                if ($action === 'add') {

                    $stmt = $pdo->prepare("
                        INSERT INTO experience
                        (position, company, start_date, end_date, description)
                        VALUES (?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $position,
                        $company,
                        $startDate !== '' ? $startDate : null,
                        $endDate !== '' ? $endDate : null,
                        $description
                    ]);

                } else {

                    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

                    if (!$id || $id < 1) {
                        $error = 'Invalid experience ID.';
                    } else {

                        $stmt = $pdo->prepare("
                            UPDATE experience
                            SET position = ?, company = ?, start_date = ?,
                                end_date = ?, description = ?
                            WHERE id = ?
                        ");

                        $stmt->execute([
                            $position,
                            $company,
                            $startDate !== '' ? $startDate : null,
                            $endDate !== '' ? $endDate : null,
                            $description,
                            $id
                        ]);
                    }
                }

                if ($error === '') {
                    header('Location: experience.php?success=1');
                    exit;
                }
            }

        } elseif ($action === 'delete') {

            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                $error = 'Invalid experience ID.';
            } else {

                $stmt = $pdo->prepare("DELETE FROM experience WHERE id = ?");
                $stmt->execute([$id]);

                header('Location: experience.php?deleted=1');
                exit;
            }

        } else {
            $error = 'Invalid action.';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM experience ORDER BY id ASC");
$experienceList = $stmt->fetchAll();

$success = '';

if (isset($_GET['success'])) {
    $success = 'Experience record saved successfully!';
} elseif (isset($_GET['deleted'])) {
    $success = 'Experience record deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Experience Management</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">

    <script src="../js/script.js" defer></script>
</head>

<body class="admin-body">

<main class="admin-page">

    <header class="admin-header">
        <div>
            <h1>Experience Management</h1>
            <p>Manage the experience displayed on your portfolio.</p>
        </div>

        <button type="button" class="theme-toggle" aria-label="Toggle theme">
            🌙
        </button>
    </header>

    <?php if ($success): ?>
        <p class="admin-message admin-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="admin-message admin-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <section class="admin-panel">

        <h2>Add Experience</h2>

        <form method="POST" action="experience.php" class="admin-form">

            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['experience_csrf'], ENT_QUOTES, 'UTF-8') ?>">

            <input type="hidden" name="action" value="add">

            <div class="admin-form-group">
                <label>Position</label>
                <input type="text" name="position" maxlength="255" required>
            </div>

            <div class="admin-form-group">
                <label>Company / Organization</label>
                <input type="text" name="company" maxlength="255" required>
            </div>

            <div class="admin-form-group">
                <label>Start Date</label>
                <input type="text" name="start_date" placeholder="2024">
            </div>

            <div class="admin-form-group">
                <label>End Date</label>
                <input type="text" name="end_date" placeholder="Present">
            </div>

            <div class="admin-form-group">
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <button type="submit" class="admin-submit-button">
                Add Experience
            </button>

        </form>

    </section>

    <section class="admin-panel">

        <h2>Existing Experience Records</h2>

        <?php if (empty($experienceList)): ?>

            <p>No experience records found.</p>

        <?php else: ?>

            <?php foreach ($experienceList as $experience): ?>

                <div class="admin-card">

                    <form method="POST" action="experience.php" class="admin-form">

                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['experience_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                        <input type="hidden" name="action" value="edit">

                        <input type="hidden" name="id"
                            value="<?= (int) $experience['id'] ?>">

                        <div class="admin-form-group">
                            <label>Position</label>
                            <input type="text" name="position"
                                value="<?= htmlspecialchars($experience['position'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="255" required>
                        </div>

                        <div class="admin-form-group">
                            <label>Company / Organization</label>
                            <input type="text" name="company"
                                value="<?= htmlspecialchars($experience['company'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="255" required>
                        </div>

                        <div class="admin-form-group">
                            <label>Start Date</label>
                            <input type="text" name="start_date"
                                value="<?= htmlspecialchars($experience['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="admin-form-group">
                            <label>End Date</label>
                            <input type="text" name="end_date"
                                value="<?= htmlspecialchars($experience['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="admin-form-group">
                            <label>Description</label>
                            <textarea name="description"><?= htmlspecialchars($experience['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <button type="submit" class="admin-submit-button">
                            Save Changes
                        </button>

                    </form>

                    <form method="POST" action="experience.php"
                        onsubmit="return confirm('Are you sure you want to delete this experience record?');">

                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['experience_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                        <input type="hidden" name="action" value="delete">

                        <input type="hidden" name="id"
                            value="<?= (int) $experience['id'] ?>">

                        <button type="submit" class="admin-button delete-button">
                            Delete Experience
                        </button>

                    </form>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

    <div class="admin-footer-actions">
        <a href="index.php">Back to Dashboard</a>
        |
        <a href="../resume.php" target="_blank">View Public Resume Page</a>
    </div>

</main>

</body>
</html>
