
<?php
require_once __DIR__ . '/includes/db.php';

// Get education records
$educationStmt = $pdo->query("SELECT * FROM education ORDER BY id ASC");
$education = $educationStmt->fetchAll();

// Get experience records
$experienceStmt = $pdo->query("SELECT * FROM experience ORDER BY id ASC");
$experiences = $experienceStmt->fetchAll();

// Get achievement records
$achievementStmt = $pdo->query("SELECT * FROM achievements ORDER BY id ASC");
$achievements = $achievementStmt->fetchAll();

// Get website settings
$settingsStmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $settingsStmt->fetch();

$siteTitle = !empty($settings['site_title'])
    ? $settings['site_title']
    : 'My Portfolio | Resume';
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
                MY BACKGROUND
            </p>

            <h1>
                Resume / Experience
            </h1>

            <p>
                A simple overview of my education,
                activities, training, and experience.
            </p>

        </section>

        <section class="resume-section container">

            <!-- Education -->

            <?php foreach ($education as $item): ?>

                <article class="resume-card">

                    <p class="resume-date">
                        <?= htmlspecialchars($item['start_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        –
                        <?= htmlspecialchars($item['end_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <h2>
                        <?= htmlspecialchars($item['school'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <h3>
                        <?= htmlspecialchars($item['degree'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </article>

            <?php endforeach; ?>


            <!-- Experience -->

            <?php foreach ($experiences as $item): ?>

                <article class="resume-card">

                    <p class="resume-date">
                        <?= htmlspecialchars($item['start_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        –
                        <?= htmlspecialchars($item['end_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <h2>
                        <?= htmlspecialchars($item['position'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <h3>
                        <?= htmlspecialchars($item['company'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </article>

            <?php endforeach; ?>


            <!-- Achievements and Training -->

            <?php foreach ($achievements as $item): ?>

                <article class="resume-card">

                    <p class="resume-date">
                        <?= htmlspecialchars($item['date_received'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <h2>
                        <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <h3>
                        <?= htmlspecialchars($item['issuer'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                </article>

            <?php endforeach; ?>


            <!-- Career Goals -->

            <article class="resume-card">

                <p class="resume-date">
                    Future Goals
                </p>

                <h2>
                    Career Goals
                </h2>

                <p>
                    My goal is to continue improving my skills in
                    web development, programming, graphic design,
                    and digital media. I want to create useful and
                    creative digital projects in the future.
                </p>

            </article>

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
