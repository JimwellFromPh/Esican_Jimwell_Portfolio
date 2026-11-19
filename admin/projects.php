
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';

if (empty($_SESSION['projects_csrf'])) {
    $_SESSION['projects_csrf'] = bin2hex(random_bytes(32));
}

function uploadProjectImage($file)
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed.');
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Image must not exceed 2 MB.');
    }

    $imageInfo = @getimagesize($file['tmp_name']);

    if ($imageInfo === false) {
        throw new RuntimeException('Please upload a valid image.');
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($allowedTypes[$imageInfo['mime']])) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
    }

    $uploadDirectory = __DIR__ . '/../uploads/';

    if (!is_dir($uploadDirectory) || !is_writable($uploadDirectory)) {
        throw new RuntimeException('Uploads folder is missing or not writable.');
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$imageInfo['mime']];
    $destination = $uploadDirectory . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Unable to save the uploaded image.');
    }

    return [
        'path' => 'uploads/' . $fileName,
        'file' => $destination
    ];
}

function removeProjectImage($path)
{
    if (strpos($path, 'uploads/') !== 0) {
        return;
    }

    $filePath = __DIR__ . '/../' . $path;

    if (is_file($filePath)) {
        unlink($filePath);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['projects_csrf']) ||
        !hash_equals($_SESSION['projects_csrf'], $token)
    ) {
        $error = 'Invalid form submission. Please refresh the page.';

    } else {

        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {

            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $link = trim($_POST['link'] ?? '');

            if ($title === '') {
                $error = 'Project title is required.';

            } elseif ($link !== '' && !filter_var($link, FILTER_VALIDATE_URL)) {
                $error = 'Please enter a valid project URL.';

            } else {

                $id = null;
                $oldImage = null;

                if ($action === 'edit') {
                    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

                    if (!$id || $id < 1) {
                        $error = 'Invalid project ID.';
                    } else {
                        $find = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
                        $find->execute([$id]);
                        $existingProject = $find->fetch();

                        if (!$existingProject) {
                            $error = 'Project not found.';
                        } else {
                            $oldImage = $existingProject['image'];
                        }
                    }
                }

                $uploaded = null;

                if ($error === '') {
                    try {
                        $uploaded = uploadProjectImage($_FILES['image'] ?? [
                            'error' => UPLOAD_ERR_NO_FILE
                        ]);

                        $imagePath = $uploaded['path'] ?? $oldImage;

                        if ($action === 'add') {

                            $stmt = $pdo->prepare("
                                INSERT INTO projects (title, description, link, image)
                                VALUES (?, ?, ?, ?)
                            ");

                            $stmt->execute([
                                $title,
                                $description,
                                $link !== '' ? $link : null,
                                $imagePath
                            ]);

                        } else {

                            $stmt = $pdo->prepare("
                                UPDATE projects
                                SET title = ?, description = ?, link = ?, image = ?
                                WHERE id = ?
                            ");

                            $stmt->execute([
                                $title,
                                $description,
                                $link !== '' ? $link : null,
                                $imagePath,
                                $id
                            ]);
                        }

                        if ($uploaded && $oldImage) {
                            removeProjectImage($oldImage);
                        }

                        $_SESSION['projects_csrf'] = bin2hex(random_bytes(32));

                        header('Location: projects.php?success=1');
                        exit;

                    } catch (RuntimeException $e) {
                        $error = $e->getMessage();

                    } catch (PDOException $e) {
                        if ($uploaded) {
                            removeProjectImage($uploaded['path']);
                        }

                        error_log($e->getMessage());
                        $error = 'Unable to save the project.';
                    }
                }
            }

        } elseif ($action === 'delete') {

            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                $error = 'Invalid project ID.';

            } else {

                $find = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
                $find->execute([$id]);
                $project = $find->fetch();

                if ($project) {
                    $delete = $pdo->prepare("DELETE FROM projects WHERE id = ?");
                    $delete->execute([$id]);

                    removeProjectImage($project['image'] ?? '');

                    header('Location: projects.php?deleted=1');
                    exit;
                } else {
                    $error = 'Project not found.';
                }
            }

        } else {
            $error = 'Invalid action.';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM projects ORDER BY id ASC");
$projects = $stmt->fetchAll();

$success = '';

if (isset($_GET['success'])) {
    $success = 'Project saved successfully!';
} elseif (isset($_GET['deleted'])) {
    $success = 'Project deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Projects Management</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">

    <script src="../js/script.js" defer></script>
</head>

<body class="admin-body">

<main class="admin-page">

    <header class="admin-header">

        <div>
            <h1>Projects Management</h1>
            <p>Add, edit, or delete the projects displayed on your portfolio.</p>
        </div>

        <div class="admin-header-actions">

            <button type="button" class="theme-toggle" aria-label="Toggle theme">
                🌙
            </button>

        </div>

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

        <h2>Add New Project</h2>

        <form method="POST" action="projects.php" enctype="multipart/form-data"
            class="admin-form">

            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['projects_csrf'], ENT_QUOTES, 'UTF-8') ?>">

            <input type="hidden" name="action" value="add">

            <div class="admin-form-group">
                <label>Project Title</label>
                <input type="text" name="title" maxlength="255" required>
            </div>

            <div class="admin-form-group">
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <div class="admin-form-group">
                <label>Project Link</label>
                <input type="url" name="link" placeholder="https://example.com">
            </div>

            <div class="admin-form-group">
                <label>Project Image</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                <small>Allowed formats: JPG, PNG, WEBP. Maximum size: 2 MB.</small>
            </div>

            <button type="submit" class="admin-submit-button">
                Add Project
            </button>

        </form>

    </section>

    <section class="admin-panel">

        <h2>Existing Projects</h2>

        <?php if (empty($projects)): ?>

            <p>No projects found.</p>

        <?php else: ?>

            <?php foreach ($projects as $project): ?>

                <div class="admin-card">

                    <?php if (!empty($project['image'])): ?>
                        <img
                            src="../<?= htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="Project Image"
                            class="profile-preview">
                    <?php endif; ?>

                    <form method="POST" action="projects.php"
                        enctype="multipart/form-data" class="admin-form">

                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['projects_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                        <input type="hidden" name="action" value="edit">

                        <input type="hidden" name="id"
                            value="<?= (int) $project['id'] ?>">

                        <div class="admin-form-group">
                            <label>Project Title</label>
                            <input type="text" name="title"
                                value="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="255" required>
                        </div>

                        <div class="admin-form-group">
                            <label>Description</label>
                            <textarea name="description"><?= htmlspecialchars($project['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="admin-form-group">
                            <label>Project Link</label>
                            <input type="url" name="link"
                                value="<?= htmlspecialchars($project['link'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="https://example.com">
                        </div>

                        <div class="admin-form-group">
                            <label>Replace Project Image (Optional)</label>
                            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                        </div>

                        <button type="submit" class="admin-submit-button">
                            Save Changes
                        </button>

                    </form>

                    <form method="POST" action="projects.php"
                        onsubmit="return confirm('Are you sure you want to delete this project?');">

                        <input type="hidden" name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['projects_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                        <input type="hidden" name="action" value="delete">

                        <input type="hidden" name="id"
                            value="<?= (int) $project['id'] ?>">

                        <button type="submit" class="admin-button delete-button">
                            Delete Project
                        </button>

                    </form>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

    <div class="admin-footer-actions">

        <a href="index.php">← Back to Dashboard</a>

        <a href="../projects.php" target="_blank">View Public Projects Page →</a>

    </div>

</main>

</body>
</html>
