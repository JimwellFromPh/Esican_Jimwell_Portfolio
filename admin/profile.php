
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();

if (!$profile) {
    die('Profile information not found.');
}

$error = '';
$success = '';

if (empty($_SESSION['profile_csrf'])) {
    $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['profile_csrf']) ||
        !hash_equals($_SESSION['profile_csrf'], $token)
    ) {
        $error = 'Invalid form submission. Please refresh the page.';

    } else {

        $fullName = trim($_POST['full_name'] ?? '');
        $headline = trim($_POST['headline'] ?? '');
        $introduction = trim($_POST['introduction'] ?? '');
        $biography = trim($_POST['biography'] ?? '');
        $interests = trim($_POST['interests'] ?? '');
        $goals = trim($_POST['goals'] ?? '');
        $journey = trim($_POST['journey'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $location = trim($_POST['location'] ?? '');

        if ($fullName === '' || $headline === '') {
            $error = 'Full name and headline are required.';

        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';

        } else {

            $newImagePath = $profile['profile_image'];
            $uploadedFile = null;

            if (
                isset($_FILES['profile_image']) &&
                $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Image upload failed. Please try again.';

                } elseif ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
                    $error = 'Image must not exceed 2 MB.';

                } else {

                    $tmpPath = $_FILES['profile_image']['tmp_name'];
                    $imageInfo = @getimagesize($tmpPath);

                    if ($imageInfo === false) {
                        $error = 'Please upload a valid image.';

                    } else {

                        $mime = $imageInfo['mime'];

                        $allowedTypes = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/webp' => 'webp'
                        ];

                        if (!isset($allowedTypes[$mime])) {
                            $error = 'Only JPG, PNG, and WEBP images are allowed.';

                        } else {

                            $uploadDirectory = __DIR__ . '/../uploads/';

                            if (!is_dir($uploadDirectory)) {
                                $error = 'Uploads folder does not exist.';

                            } elseif (!is_writable($uploadDirectory)) {
                                $error = 'Uploads folder is not writable.';

                            } else {

                                $extension = $allowedTypes[$mime];
                                $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
                                $destination = $uploadDirectory . $fileName;

                                if (move_uploaded_file($tmpPath, $destination)) {
                                    $newImagePath = 'uploads/' . $fileName;
                                    $uploadedFile = $destination;
                                } else {
                                    $error = 'Unable to save the uploaded image.';
                                }
                            }
                        }
                    }
                }
            }

            if ($error === '') {

                try {

                    $update = $pdo->prepare("
                        UPDATE profile
                        SET
                            full_name = ?,
                            headline = ?,
                            introduction = ?,
                            biography = ?,
                            interests = ?,
                            goals = ?,
                            journey = ?,
                            email = ?,
                            phone = ?,
                            location = ?,
                            profile_image = ?
                        WHERE id = ?
                    ");

                    $update->execute([
                        $fullName,
                        $headline,
                        $introduction,
                        $biography,
                        $interests,
                        $goals,
                        $journey,
                        $email,
                        $phone,
                        $location,
                        $newImagePath,
                        $profile['id']
                    ]);

                    if (
                        $uploadedFile !== null &&
                        !empty($profile['profile_image']) &&
                        strpos($profile['profile_image'], 'uploads/') === 0
                    ) {
                        $oldImage = __DIR__ . '/../' . $profile['profile_image'];

                        if (is_file($oldImage) && $oldImage !== $uploadedFile) {
                            unlink($oldImage);
                        }
                    }

                    $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));

                    header('Location: profile.php?updated=1');
                    exit;

                } catch (PDOException $e) {

                    if ($uploadedFile !== null && is_file($uploadedFile)) {
                        unlink($uploadedFile);
                    }

                    error_log($e->getMessage());
                    $error = 'Unable to update profile information.';
                }
            }
        }
    }
}

if (isset($_GET['updated'])) {
    $success = 'Profile information updated successfully!';

    $stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
    $profile = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profile Management</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">

    <script src="../js/script.js" defer></script>
</head>

<body class="admin-body">

    <main class="admin-page profile-admin">

        <div class="admin-header">
            <div>
                <h1>Profile Management</h1>
                <p>Edit the personal information displayed on your portfolio.</p>
            </div>

            <div class="admin-header-actions">
                <button type="button" class="theme-toggle" aria-label="Toggle theme">🌙</button>
            </div>
        </div>

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

            <form method="POST" action="profile.php" enctype="multipart/form-data" class="admin-form">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['profile_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="admin-form-group">
                    <label>Current Profile Picture</label>

                    <img
                        src="../<?= htmlspecialchars($profile['profile_image'] ?? 'assets/profilepic.jpg', ENT_QUOTES, 'UTF-8') ?>"
                        alt="Profile Picture"
                        class="profile-preview">
                </div>

                <div class="admin-form-group">
                    <label for="profile_image">Upload New Profile Picture</label>

                    <input
                        type="file"
                        id="profile_image"
                        name="profile_image"
                        accept=".jpg,.jpeg,.png,.webp">

                    <small>Allowed formats: JPG, PNG, WEBP. Maximum size: 2 MB.</small>
                </div>

                <div class="admin-form-group">
                    <label for="full_name">Full Name</label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        value="<?= htmlspecialchars($profile['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <div class="admin-form-group">
                    <label for="headline">Headline</label>

                    <input
                        type="text"
                        id="headline"
                        name="headline"
                        value="<?= htmlspecialchars($profile['headline'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <div class="admin-form-group">
                    <label for="introduction">Introduction</label>

                    <textarea
                        id="introduction"
                        name="introduction"><?= htmlspecialchars($profile['introduction'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label for="biography">Biography</label>

                    <textarea
                        id="biography"
                        name="biography"><?= htmlspecialchars($profile['biography'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label for="interests">My Interests</label>

                    <textarea
                        id="interests"
                        name="interests"
                        placeholder="Enter your interests, one per line"><?= htmlspecialchars($profile['interests'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label for="goals">My Goals</label>

                    <textarea
                        id="goals"
                        name="goals"><?= htmlspecialchars($profile['goals'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label for="journey">My Journey</label>

                    <textarea
                        id="journey"
                        name="journey"><?= htmlspecialchars($profile['journey'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="admin-form-group">
                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($profile['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="admin-form-group">
                    <label for="phone">Phone</label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="<?= htmlspecialchars($profile['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="admin-form-group">
                    <label for="location">Location</label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="<?= htmlspecialchars($profile['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <button type="submit" class="admin-submit-button">
                    Save Changes
                </button>

            </form>

        </section>

        <div class="admin-footer-actions">
            <a href="index.php">Back to Dashboard</a>
            <a href="../index.php" target="_blank">View Portfolio</a>
        </div>

    </main>

</body>
</html>
