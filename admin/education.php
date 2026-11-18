
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';

if (empty($_SESSION['education_csrf'])) {
    $_SESSION['education_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['education_csrf']) ||
        !hash_equals($_SESSION['education_csrf'], $token)
    ) {
        $error = 'Invalid form submission. Please refresh the page.';

    } else {

        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {

            $school = trim($_POST['school'] ?? '');
            $degree = trim($_POST['degree'] ?? '');
            $startYear = trim($_POST['start_year'] ?? '');
            $endYear = trim($_POST['end_year'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($school === '' || $degree === '') {
                $error = 'School and degree are required.';

            } elseif (
                ($startYear !== '' && !preg_match('/^\d{4}$/', $startYear)) ||
                ($endYear !== '' && !preg_match('/^\d{4}$/', $endYear))
            ) {
                $error = 'Please enter valid four-digit years.';

            } elseif (
                $startYear !== '' &&
                $endYear !== '' &&
                $endYear !== 'Present' &&
                (int) $endYear < (int) $startYear
            ) {
                $error = 'End year cannot be earlier than start year.';

            } else {

                if ($action === 'add') {

                    $stmt = $pdo->prepare("
                        INSERT INTO education
                        (school, degree, start_year, end_year, description)
                        VALUES (?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $school,
                        $degree,
                        $startYear !== '' ? $startYear : null,
                        $endYear !== '' ? $endYear : null,
                        $description
                    ]);

                } else {

                    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

                    if (!$id || $id < 1) {
                        $error = 'Invalid education ID.';

                    } else {

                        $stmt = $pdo->prepare("
                            UPDATE education
                            SET school = ?, degree = ?, start_year = ?,
                                end_year = ?, description = ?
                            WHERE id = ?
                        ");

                        $stmt->execute([
                            $school,
                            $degree,
                            $startYear !== '' ? $startYear : null,
                            $endYear !== '' ? $endYear : null,
                            $description,
                            $id
                        ]);
                    }
                }

                if ($error === '') {
                    header('Location: education.php?success=1');
                    exit;
                }
            }

        } elseif ($action === 'delete') {

            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                $error = 'Invalid education ID.';

            } else {

                $stmt = $pdo->prepare("DELETE FROM education WHERE id = ?");
                $stmt->execute([$id]);

                header('Location: education.php?deleted=1');
                exit;
            }

        } else {
            $error = 'Invalid action.';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM education ORDER BY id ASC");
$educationList = $stmt->fetchAll();

$success = '';

if (isset($_GET['success'])) {
    $success = 'Education record saved successfully!';
} elseif (isset($_GET['deleted'])) {
    $success = 'Education record deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Education Management</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        .education-admin {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }

        .education-admin input,
        .education-admin textarea {
            width: 100%;
            padding: 10px;
            margin: 6px 0 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font: inherit;
        }

        .education-admin textarea {
            min-height: 100px;
            resize: vertical;
        }

        .education-admin button {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            background: #222;
            color: white;
            cursor: pointer;
            margin: 4px;
        }

        .education-admin-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .education-message {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .education-success {
            background: #e1f5e5;
            color: #176b2c;
        }

        .education-error {
            background: #ffe5e5;
            color: #a00000;
        }

        .delete-button {
            background: #b42318 !important;
        }
    </style>
</head>

<body>

<main class="education-admin">

    <h1>Education Management</h1>
    <p>Manage the educational background displayed on your portfolio.</p>

    <?php if ($success): ?>
        <p class="education-message education-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="education-message education-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <h2>Add Education</h2>

    <form method="POST" action="education.php">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['education_csrf'], ENT_QUOTES, 'UTF-8') ?>">

        <input type="hidden" name="action" value="add">

        <label>School</label>
        <input type="text" name="school" maxlength="255" required>

        <label>Degree / Program</label>
        <input type="text" name="degree" maxlength="255" required>

        <label>Start Year</label>
        <input type="text" name="start_year" maxlength="4" placeholder="2024">

        <label>End Year</label>
        <input type="text" name="end_year" maxlength="7" placeholder="Present">

        <label>Description</label>
        <textarea name="description"></textarea>

        <button type="submit">Add Education</button>

    </form>

    <hr>

    <h2>Existing Education Records</h2>

    <?php if (empty($educationList)): ?>

        <p>No education records found.</p>

    <?php else: ?>

        <?php foreach ($educationList as $education): ?>

            <div class="education-admin-card">

                <form method="POST" action="education.php">

                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['education_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="edit">

                    <input type="hidden" name="id"
                        value="<?= (int) $education['id'] ?>">

                    <label>School</label>
                    <input type="text" name="school"
                        value="<?= htmlspecialchars($education['school'], ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="255" required>

                    <label>Degree / Program</label>
                    <input type="text" name="degree"
                        value="<?= htmlspecialchars($education['degree'], ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="255" required>

                    <label>Start Year</label>
                    <input type="text" name="start_year"
                        value="<?= htmlspecialchars($education['start_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="4">

                    <label>End Year</label>
                    <input type="text" name="end_year"
                        value="<?= htmlspecialchars($education['end_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="7">

                    <label>Description</label>
                    <textarea name="description"><?= htmlspecialchars($education['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

                    <button type="submit">Save Changes</button>

                </form>

                <form method="POST" action="education.php"
                    onsubmit="return confirm('Are you sure you want to delete this education record?');">

                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['education_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="delete">

                    <input type="hidden" name="id"
                        value="<?= (int) $education['id'] ?>">

                    <button type="submit" class="delete-button">Delete Education</button>

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
