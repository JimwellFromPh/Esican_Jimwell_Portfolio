
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

    <style>
        .admin-dashboard {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .admin-stat-card {
            padding: 25px;
            border-radius: 12px;
            background: #f4f4f4;
            color: #222;
        }

        .admin-stat-card h3 {
            margin-bottom: 10px;
        }

        .admin-stat-card p {
            font-size: 28px;
            font-weight: bold;
        }

        .admin-actions {
            margin-top: 35px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .admin-actions a {
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            background: #222;
            color: white;
        }

        .admin-actions a:hover {
            background: #444;
        }

        .admin-actions .section-link {
            background: #444;
        }
    </style>
</head>

<body>

<div class="admin-dashboard">

    <div class="admin-header">

        <div>
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo $username; ?>!</p>
        </div>

        <a href="logout.php">Logout</a>

    </div>

    <h2>Website Overview</h2>

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

    <h2 style="margin-top: 40px;">Content Management</h2>

    <div class="admin-actions">

        <a href="profile.php">Profile Management</a>

        <a href="skills.php">Skills Management</a>

        <a href="projects.php">Projects Management</a>

        <a href="education.php">Education Management</a>

        <a href="experience.php">Experience Management</a>

        <a href="achievements.php">Achievements Management</a>

        <a href="messages.php">Messages Management</a>

        <a href="settings.php">Website Settings</a>

    </div>

    <div class="admin-actions">

        <a href="../index.php" class="section-link">View Portfolio</a>

    </div>

</div>

</body>
</html>
