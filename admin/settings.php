
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';
$success = '';

$stmt = $pdo->query("SELECT * FROM settings ORDER BY id ASC LIMIT 1");
$settings = $stmt->fetch();

if (empty($_SESSION['settings_csrf'])) {
    $_SESSION['settings_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['settings_csrf'], $token)) {
        $error = 'Invalid form submission. Please refresh the page.';

    } else {

        $siteTitle = trim($_POST['site_title'] ?? '');
        $theme = $_POST['theme'] ?? 'light';

        if ($siteTitle === '') {
            $error = 'Website title is required.';

        } elseif (strlen($siteTitle) > 150) {
            $error = 'Website title must not exceed 150 characters.';

        } elseif (!in_array($theme, ['light', 'dark'], true)) {
            $error = 'Please select a valid theme.';

        } else {

            $logoPath = $settings['logo'] ?? null;
            $newLogo = null;

            if (
                isset($_FILES['logo']) &&
                $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE
            ) {

                $file = $_FILES['logo'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Logo upload failed. Please try again.';

                } elseif ($file['size'] > 2 * 1024 * 1024) {
                    $error = 'Logo must not exceed 2 MB.';

                } else {

                    $imageInfo = @getimagesize($file['tmp_name']);

                    $allowedTypes = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp'
                    ];

                    $mime = $imageInfo['mime'] ?? '';

                    if (!$imageInfo || !isset($allowedTypes[$mime])) {
                        $error = 'Only JPG, PNG, and WEBP images are allowed.';

                    } else {

                        $uploadDirectory = __DIR__ . '/../uploads/';

                        if (!is_dir($uploadDirectory)) {
                            $error = 'Uploads folder was not found.';

                        } else {

                            $filename = bin2hex(random_bytes(16))
                                . '.' . $allowedTypes[$mime];

                            if (move_uploaded_file(
                                $file['tmp_name'],
                                $uploadDirectory . $filename
                            )) {
                                $newLogo = 'uploads/' . $filename;
                                $logoPath = $newLogo;

                            } else {
                                $error = 'Unable to save the uploaded logo.';
                            }
                        }
                    }
                }
            }

            if ($error === '') {

                try {

                    if ($settings) {

                        $stmt = $pdo->prepare("
                            UPDATE settings
                            SET site_title = ?, logo = ?, theme = ?
                            WHERE id = ?
                        ");

                        $stmt->execute([
                            $siteTitle,
                            $logoPath,
                            $theme,
                            $settings['id']
                        ]);

                    } else {

                        $stmt = $pdo->prepare("
                            INSERT INTO settings (site_title, logo, theme)
                            VALUES (?, ?, ?)
                        ");

                        $stmt->execute([
                            $siteTitle,
                            $logoPath,
                            $theme
                        ]);
                    }

                    if (
                        $newLogo &&
                        !empty($settings['logo']) &&
                        strpos($settings['logo'], 'uploads/') === 0
                    ) {
                        $oldLogo = __DIR__ . '/../' . $settings['logo'];

                        if (is_file($oldLogo)) {
                            unlink($oldLogo);
                        }
                    }

                    header('Location: settings.php?saved=1');
                    exit;

                } catch (PDOException $e) {

                    if ($newLogo && is_file(__DIR__ . '/../' . $newLogo)) {
                        unlink(__DIR__ . '/../' . $newLogo);
                    }

                    error_log($e->getMessage());
                    $error = 'Unable to save website settings.';
                }
            }

        }
    }
}

$stmt = $pdo->query("SELECT * FROM settings ORDER BY id ASC LIMIT 1");
$settings = $stmt->fetch();

if (isset($_GET['saved'])) {
    $success = 'Website settings saved successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Website Settings</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        .settings-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .settings-container input,
        .settings-container select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font: inherit;
        }

        .settings-container button {
            padding: 12px 20px;
            background: #222;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .settings-logo {
            max-width: 150px;
            max-height: 150px;
            object-fit: contain;
            display: block;
            margin: 10px 0 20px;
        }

        .settings-message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .settings-success {
            background: #e1f5e5;
            color: #176b2c;
        }

        .settings-error {
            background: #ffe5e5;
            color: #a00000;
        }
    </style>
</head>

<body>

<main class="settings-container">

    <h1>Website Settings</h1>

    <p>Manage your portfolio's title, logo, and appearance.</p>

    <?php if ($success): ?>
        <p class="settings-message settings-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="settings-message settings-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="settings.php" enctype="multipart/form-data">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['settings_csrf'], ENT_QUOTES, 'UTF-8') ?>">

        <label>Website Title</label>

        <input
            type="text"
            name="site_title"
            maxlength="150"
            value="<?= htmlspecialchars($settings['site_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            placeholder="Jimwell Esican | Portfolio"
            required
        >

        <label>Website Logo</label>

        <?php if (!empty($settings['logo'])): ?>

            <p>Current Logo:</p>

            <img
                src="../<?= htmlspecialchars($settings['logo'], ENT_QUOTES, 'UTF-8') ?>"
                alt="Current Website Logo"
                class="settings-logo"
            >

        <?php else: ?>

            <p>No logo uploaded yet.</p>

        <?php endif; ?>

        <input
            type="file"
            name="logo"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <small>Allowed formats: JPG, PNG, WEBP. Maximum size: 2 MB.</small>

        <br><br>

        <label>Website Theme</label>

        <select name="theme">

            <option value="light"
                <?= ($settings['theme'] ?? 'light') === 'light' ? 'selected' : '' ?>>
                Light
            </option>

            <option value="dark"
                <?= ($settings['theme'] ?? 'light') === 'dark' ? 'selected' : '' ?>>
                Dark
            </option>

        </select>

        <button type="submit">Save Settings</button>

    </form>

    <p>
        <a href="index.php">Back to Dashboard</a> |
        <a href="../index.php" target="_blank">View Portfolio</a>
    </p>

</main>

</body>
</html>
