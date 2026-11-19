
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';

if (empty($_SESSION['skills_csrf'])) {
    $_SESSION['skills_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['skills_csrf']) ||
        !hash_equals($_SESSION['skills_csrf'], $token)
    ) {
        $error = 'Invalid form submission. Please refresh the page.';

    } else {

        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {

            $name = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $level = filter_var($_POST['level'] ?? null, FILTER_VALIDATE_INT);

            if ($name === '') {
                $error = 'Skill name is required.';

            } elseif ($level === false || $level === null || $level < 0 || $level > 100) {
                $error = 'Skill level must be between 0 and 100.';

            } else {

                if ($action === 'add') {

                    $stmt = $pdo->prepare("
                        INSERT INTO skills (name, category, level)
                        VALUES (?, ?, ?)
                    ");

                    $stmt->execute([
                        $name,
                        $category !== '' ? $category : null,
                        $level
                    ]);

                } else {

                    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

                    if (!$id || $id < 1) {
                        $error = 'Invalid skill ID.';

                    } else {

                        $stmt = $pdo->prepare("
                            UPDATE skills
                            SET name = ?, category = ?, level = ?
                            WHERE id = ?
                        ");

                        $stmt->execute([
                            $name,
                            $category !== '' ? $category : null,
                            $level,
                            $id
                        ]);
                    }
                }

                if ($error === '') {
                    header('Location: skills.php?success=1');
                    exit;
                }
            }

        } elseif ($action === 'delete') {

            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                $error = 'Invalid skill ID.';

            } else {

                $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
                $stmt->execute([$id]);

                header('Location: skills.php?deleted=1');
                exit;
            }

        } else {
            $error = 'Invalid action.';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM skills ORDER BY id ASC");
$skills = $stmt->fetchAll();

$success = '';

if (isset($_GET['success'])) {
    $success = 'Skill saved successfully!';
} elseif (isset($_GET['deleted'])) {
    $success = 'Skill deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Skills Management</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">

    <script src="../js/script.js" defer></script>
</head>

<body class="admin-body">

<main class="admin-page">

    <header class="admin-header">

        <div>
            <h1>Skills Management</h1>
            <p>Add, edit, or remove the skills displayed on your portfolio.</p>
        </div>

        <div class="admin-header-actions">

            <button type="button" class="theme-toggle" aria-label="Toggle theme">
                🌙
            </button>

        </div>

    </header>

    <section class="admin-panel">

        <h2>Add New Skill</h2>

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

        <form method="POST" action="skills.php" class="admin-form">

            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['skills_csrf'], ENT_QUOTES, 'UTF-8') ?>">

            <input type="hidden" name="action" value="add">

            <div class="admin-form-group">
                <label for="new_name">Skill Name</label>
                <input type="text" id="new_name" name="name" maxlength="100" required>
            </div>

            <div class="admin-form-group">
                <label for="new_category">Category</label>
                <input type="text" id="new_category" name="category" maxlength="50"
                    placeholder="Example: Programming">
            </div>

            <div class="admin-form-group">
                <label for="new_level">Skill Level (0–100)</label>
                <input type="number" id="new_level" name="level"
                    min="0" max="100" value="50" required>
            </div>

            <button type="submit" class="admin-submit-button">Add Skill</button>

        </form>

    </section>

    <section class="admin-panel">

        <h2>Existing Skills</h2>

        <?php if (empty($skills)): ?>

            <p>No skills found.</p>

        <?php else: ?>

            <?php foreach ($skills as $skill): ?>

                <div class="admin-card">

                    <form method="POST" action="skills.php" class="admin-form">

                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['skills_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                        <input type="hidden" name="action" value="edit">

                        <input type="hidden" name="id"
                            value="<?= (int) $skill['id'] ?>">

                        <div class="admin-form-group">
                            <label>Skill Name</label>
                            <input type="text" name="name"
                                value="<?= htmlspecialchars($skill['name'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="100" required>
                        </div>

                        <div class="admin-form-group">
                            <label>Category</label>
                            <input type="text" name="category"
                                value="<?= htmlspecialchars($skill['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="50">
                        </div>

                        <div class="admin-form-group">
                            <label>Level (0–100)</label>
                            <input type="number" name="level"
                                value="<?= (int) $skill['level'] ?>"
                                min="0" max="100" required>
                        </div>

                        <button type="submit" class="admin-submit-button">
                            Save Changes
                        </button>

                    </form>

                    <form method="POST" action="skills.php"
                        onsubmit="return confirm('Are you sure you want to delete this skill?');">

                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['skills_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                        <input type="hidden" name="action" value="delete">

                        <input type="hidden" name="id"
                            value="<?= (int) $skill['id'] ?>">

                        <button type="submit" class="admin-button delete-button">
                            Delete Skill
                        </button>

                    </form>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

    <div class="admin-footer-actions">

        <a href="index.php">← Back to Dashboard</a>

        <a href="../skills.php" target="_blank">View Public Skills Page →</a>

    </div>

</main>

</body>
</html>
