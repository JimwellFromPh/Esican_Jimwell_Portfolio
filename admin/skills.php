
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

    <style>
        .skills-admin {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }

        .skills-admin form {
            margin-bottom: 20px;
        }

        .skills-admin input,
        .skills-admin select {
            padding: 10px;
            width: 100%;
            margin: 6px 0 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font: inherit;
        }

        .skills-admin button {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            background: #222;
            color: white;
            cursor: pointer;
            margin: 4px;
        }

        .skill-admin-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 15px;
        }

        .skill-admin-card form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            align-items: end;
        }

        .skill-admin-card input {
            margin-bottom: 0;
        }

        .skill-admin-card button {
            margin: 0;
        }

        .skill-message {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .skill-success {
            background: #e1f5e5;
            color: #176b2c;
        }

        .skill-error {
            background: #ffe5e5;
            color: #a00000;
        }

        .delete-button {
            background: #b42318 !important;
        }
    </style>
</head>

<body>

<main class="skills-admin">

    <h1>Skills Management</h1>
    <p>Add, edit, or remove the skills displayed on your portfolio.</p>

    <?php if ($success): ?>
        <p class="skill-message skill-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="skill-message skill-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <h2>Add New Skill</h2>

    <form method="POST" action="skills.php">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['skills_csrf'], ENT_QUOTES, 'UTF-8') ?>">

        <input type="hidden" name="action" value="add">

        <label for="new_name">Skill Name</label>
        <input type="text" id="new_name" name="name" maxlength="100" required>

        <label for="new_category">Category</label>
        <input type="text" id="new_category" name="category" maxlength="50"
            placeholder="Example: Programming">

        <label for="new_level">Skill Level (0–100)</label>
        <input type="number" id="new_level" name="level" min="0" max="100"
            value="50" required>

        <button type="submit">Add Skill</button>

    </form>

    <hr>

    <h2>Existing Skills</h2>

    <?php if (empty($skills)): ?>

        <p>No skills found.</p>

    <?php else: ?>

        <?php foreach ($skills as $skill): ?>

            <div class="skill-admin-card">

                <form method="POST" action="skills.php">

                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['skills_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="edit">

                    <input type="hidden" name="id"
                        value="<?= (int) $skill['id'] ?>">

                    <div>
                        <label>Skill Name</label>
                        <input type="text" name="name"
                            value="<?= htmlspecialchars($skill['name'], ENT_QUOTES, 'UTF-8') ?>"
                            maxlength="100" required>
                    </div>

                    <div>
                        <label>Category</label>
                        <input type="text" name="category"
                            value="<?= htmlspecialchars($skill['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            maxlength="50">
                    </div>

                    <div>
                        <label>Level (0–100)</label>
                        <input type="number" name="level"
                            value="<?= (int) $skill['level'] ?>"
                            min="0" max="100" required>
                    </div>

                    <button type="submit">Save Changes</button>

                </form>

                <form method="POST" action="skills.php"
                    onsubmit="return confirm('Are you sure you want to delete this skill?');">

                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['skills_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="delete">

                    <input type="hidden" name="id"
                        value="<?= (int) $skill['id'] ?>">

                    <button type="submit" class="delete-button">Delete Skill</button>

                </form>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <p>
        <a href="index.php">Back to Dashboard</a> |
        <a href="../skills.php" target="_blank">View Public Skills Page</a>
    </p>

</main>

</body>
</html>
