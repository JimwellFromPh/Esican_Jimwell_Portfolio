
<?php
require_once __DIR__ . '/includes/db.php';

// Get profile information
$stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();

if (!$profile) {
    die('Profile information not found.');
}

// Get website settings
$settingsStmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $settingsStmt->fetch();

$siteTitle = !empty($settings['site_title'])
    ? $settings['site_title']
    : $profile['full_name'] . ' | About';

// Get education records
$educationStmt = $pdo->query("SELECT * FROM education ORDER BY id ASC");
$education = $educationStmt->fetchAll();

// Convert interests into a list
$interests = preg_split('/\r\n|\r|\n/', $profile['interests'] ?? '');
$interests = array_filter(array_map('trim', $interests));
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
                GET TO KNOW ME
            </p>

            <h1>
                About Me
            </h1>

            <p>
                A little information about who I am,
                what I do, and what I am interested in.
            </p>

        </section>

        <section class="about-section container">

            <div class="about-card">

                <div class="about-icon">
                    👋
                </div>

                <h2>
                    Hello!
                </h2>

                <p>
                    <?= nl2br(htmlspecialchars($profile['biography'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                </p>

            </div>

            <div class="about-card">

                <div class="about-icon">
                    📚
                </div>

                <h2>
                    Education
                </h2>

                <?php if (!empty($education)): ?>

                    <?php foreach ($education as $item): ?>

                        <p>
                            <strong>
                                <?= htmlspecialchars($item['degree'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </strong>
                        </p>

                        <p>
                            <?= htmlspecialchars($item['school'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p>
                            <?= htmlspecialchars($item['start_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            –
                            <?= htmlspecialchars($item['end_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p>
                            <?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>No education records have been added yet.</p>

                <?php endif; ?>

            </div>

            <div class="about-card">

                <div class="about-icon">
                    💡
                </div>

                <h2>
                    My Interests
                </h2>

                <ul class="interest-list">

                    <?php if (!empty($interests)): ?>

                        <?php foreach ($interests as $interest): ?>

                            <li>
                                <?= htmlspecialchars($interest, ENT_QUOTES, 'UTF-8') ?>
                            </li>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <li>No interests have been added yet.</li>

                    <?php endif; ?>

                </ul>

            </div>

            <div class="about-card">

                <div class="about-icon">
                    🚀
                </div>

                <h2>
                    My Goals
                </h2>

                <p>
                    <?= nl2br(htmlspecialchars($profile['goals'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                </p>

            </div>

        </section>

        <section class="about-statement">

            <div class="container">

                <p class="eyebrow">
                    MY JOURNEY
                </p>

                <h2>
                    Always Learning, Always Creating
                </h2>

                <p>
                    <?= nl2br(htmlspecialchars($profile['journey'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                </p>

            </div>

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
