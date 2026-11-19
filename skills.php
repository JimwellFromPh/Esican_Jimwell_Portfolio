
<?php
require_once __DIR__ . '/includes/db.php';

// Get all skills from the database
$stmt = $pdo->query("SELECT * FROM skills ORDER BY id ASC");
$skills = $stmt->fetchAll();

// Get website settings
$settingsStmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $settingsStmt->fetch();

$siteTitle = !empty($settings['site_title'])
    ? $settings['site_title']
    : 'My Portfolio | Skills';

// Assign an icon based on the skill category
function getSkillIcon($category) {
    $icons = [
        'Web Development' => '🌐',
        'Programming' => '💻',
        'Database' => '🗄️',
        'Graphic Design' => '🎨',
        'Animation' => '🎬'
    ];

    return $icons[$category] ?? '⭐';
}

// Convert skill level into a label
function getSkillLabel($level) {
    if ($level >= 75) {
        return 'Advanced';
    } elseif ($level >= 50) {
        return 'Intermediate';
    } else {
        return 'Beginner';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header class="header">

        <nav class="navbar container">

            <a href="index.php" class="logo">
                Jim<span>well</span>
            </a>

            <button
                class="menu-toggle"
                aria-label="Open navigation">
                ☰
            </button>

            <div class="nav-links">

                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="skills.php">Skills</a>
                <a href="projects.php">Projects</a>
                <a href="resume.php">Resume</a>
                <a href="contact.php">Contact</a>

                <button
                    class="theme-toggle"
                    aria-label="Toggle dark mode">
                    🌙
                </button>

            </div>

        </nav>

    </header>

    <main>

        <section class="page-header container">

            <p class="eyebrow">
                WHAT I CAN DO
            </p>

            <h1>
                My Skills
            </h1>

            <p>
                Here are some of the skills I have learned
                and developed through school activities,
                projects, and practice.
            </p>

        </section>

        <section class="skills-section container">

            <?php foreach ($skills as $skill): ?>

                <article class="skill-card">

                    <div class="skill-icon">
                        <?= getSkillIcon($skill['category']) ?>
                    </div>

                    <h2>
                        <?= htmlspecialchars($skill['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars($skill['category'] ?? 'Skill', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <div class="skill-bar">

                        <div
                            class="skill-progress"
                            style="width: <?= max(0, min(100, (int)$skill['level'])) ?>%;">
                        </div>

                    </div>

                    <span class="skill-level">
                        <?= getSkillLabel((int)$skill['level']) ?>
                    </span>

                </article>

            <?php endforeach; ?>

            <?php if (empty($skills)): ?>

                <p>No skills have been added yet.</p>

            <?php endif; ?>

        </section>

    </main>

    <button
        id="scrollTop"
        class="scroll-top"
        aria-label="Scroll to top">
        ↑
    </button>

    <footer class="footer">

        <p>
            © 2026 Jimwell Esican.
            Built with HTML, CSS & JavaScript.
        </p>

    </footer>

    <script src="js/script.js"></script>

</body>

</html>
