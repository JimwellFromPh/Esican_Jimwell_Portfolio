
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$projectCount = (int) $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$skillCount = (int) $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$experienceCount = (int) $pdo->query("SELECT COUNT(*) FROM experience")->fetchColumn();
$achievementCount = (int) $pdo->query("SELECT COUNT(*) FROM achievements")->fetchColumn();
$messageCount = (int) $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unreadCount = (int) $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();

$username = htmlspecialchars($_SESSION['admin_username'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">

    <script src="../js/script.js" defer></script>
</head>

<body class="admin-body">

<div class="admin-dashboard">

    <!-- Dashboard Header -->
    <header class="admin-header">

        <div>
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo $username; ?>!</p>
        </div>

        <div class="admin-header-actions">

            <button type="button" class="theme-toggle" aria-label="Toggle theme">
                🌙
            </button>

            <a href="logout.php" class="admin-logout">Logout</a>

        </div>

    </header>

    <!-- Website Overview -->
    <section class="admin-management">

        <h2 class="admin-section-title">
            Website <span>Overview</span>
        </h2>

        <div class="admin-stats">

            <div class="admin-stat-card">
                <h3>Projects</h3>
                <p><?php echo $projectCount; ?></p>
            </div>

            <div class="admin-stat-card">
                <h3>Skills</h3>
                <p><?php echo $skillCount; ?></p>
            </div>

            <div class="admin-stat-card">
                <h3>Experience</h3>
                <p><?php echo $experienceCount; ?></p>
            </div>

            <div class="admin-stat-card">
                <h3>Achievements</h3>
                <p><?php echo $achievementCount; ?></p>
            </div>

            <div class="admin-stat-card">
                <h3>Total Messages</h3>
                <p><?php echo $messageCount; ?></p>
            </div>

            <div class="admin-stat-card">
                <h3>Unread Messages</h3>
                <p><?php echo $unreadCount; ?></p>
            </div>

        </div>

    </section>

    <!-- Content Management -->
    <section class="admin-management">

        <h2 class="admin-section-title">
            Content <span>Management</span>
        </h2>

        <div class="admin-actions">

            <a href="profile.php" class="admin-action-link">
                Profile Management <span>→</span>
            </a>

            <a href="skills.php" class="admin-action-link">
                Skills Management <span>→</span>
            </a>

            <a href="projects.php" class="admin-action-link">
                Projects Management <span>→</span>
            </a>

            <a href="education.php" class="admin-action-link">
                Education Management <span>→</span>
            </a>

            <a href="experience.php" class="admin-action-link">
                Experience Management <span>→</span>
            </a>

            <a href="achievements.php" class="admin-action-link">
                Achievements Management <span>→</span>
            </a>

            <a href="messages.php" class="admin-action-link">
                Messages Management <span>→</span>
            </a>

            <a href="settings.php" class="admin-action-link">
                Website Settings <span>→</span>
            </a>

            <a href="change-password.php" class="admin-action-link">
                Change Password <span>→</span>
            </a>

        </div>

    </section>

    <!-- Footer -->
    <div class="admin-footer-actions">

        <a href="../index.php" class="admin-portfolio-link">
            ← View Portfolio
        </a>

    </div>

</div>

</body>
</html>
