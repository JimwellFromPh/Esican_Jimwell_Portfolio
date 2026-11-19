
<?php
require_once __DIR__ . '/includes/db.php';

// Get projects from the database
$stmt = $pdo->query("SELECT * FROM projects ORDER BY id ASC");
$projects = $stmt->fetchAll();

// Get website settings
$settingsStmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $settingsStmt->fetch();

$siteTitle = !empty($settings['site_title'])
    ? $settings['site_title']
    : 'My Portfolio | Projects';
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
                MY WORK
            </p>

            <h1>
                Projects
            </h1>

            <p>
                Here are some of my school and personal projects
                that show my creativity, design skills, and
                knowledge of web development.
            </p>

        </section>

        <section class="projects-section container">

            <?php foreach ($projects as $project): ?>

                <article class="project-card">

                    <?php if (!empty($project['image'])): ?>

                        <img
                            src="<?= htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>"
                            class="project-photo">

                    <?php endif; ?>

                    <div class="project-content">

                        <h2>
                            <?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars($project['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <?php if (!empty($project['link'])): ?>

                            <a
                                href="<?= htmlspecialchars($project['link'], ENT_QUOTES, 'UTF-8') ?>"
                                target="_blank"
                                rel="noopener noreferrer">
                                View Project
                            </a>

                        <?php endif; ?>

                    </div>

                </article>

            <?php endforeach; ?>

            <?php if (empty($projects)): ?>

                <p>No projects have been added yet.</p>

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
