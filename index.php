
<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query("SELECT * FROM profile LIMIT 1");
$profile = $stmt->fetch();

if (!$profile) {
    die('Profile information not found.');
}

$settingsStmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $settingsStmt->fetch();

$siteTitle = !empty($settings['site_title'])
    ? $settings['site_title']
    : $profile['full_name'] . ' | Home';

$fullName = htmlspecialchars($profile['full_name'], ENT_QUOTES, 'UTF-8');
$headline = htmlspecialchars($profile['headline'], ENT_QUOTES, 'UTF-8');
$introduction = htmlspecialchars($profile['introduction'], ENT_QUOTES, 'UTF-8');
$profileImage = htmlspecialchars($profile['profile_image'], ENT_QUOTES, 'UTF-8');
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
                aria-label="Open navigation"
                aria-expanded="false">
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

        <section class="hero container">

            <div class="hero-text">

                <p class="eyebrow">
                    HELLO, I'M
                </p>

                <h1>
                    <?= $fullName ?>
                </h1>

                <h2>
                    I am a
                    <span id="typing-text">
                        <?= $headline ?>
                    </span>
                </h2>

                <p>
                    <?= $introduction ?>
                </p>

                <div class="hero-buttons">

                    <a href="projects.php" class="btn primary">
                        View My Work
                    </a>

                    <a href="contact.php" class="btn secondary">
                        Contact Me
                    </a>

                </div>

            </div>

            <div class="profile-card">

                <img
                    src="<?= $profileImage ?>"
                    alt="My Profile Picture"
                    class="profile-image">

                <p>
                    Creative Student
                </p>

                <span>
                    HTML • CSS • JavaScript
                </span>

            </div>

        </section>

        <section class="quick-section">

            <div class="container">

                <div class="section-heading">

                    <p class="eyebrow">
                        WELCOME
                    </p>

                    <h2>
                        A little about my portfolio
                    </h2>

                </div>

                <div class="info-grid">

                    <article class="info-card">

                        <div class="icon">
                            🎨
                        </div>

                        <h3>
                            Creative Design
                        </h3>

                        <p>
                            I enjoy making layouts, logos,
                            posters, and digital artwork.
                        </p>

                    </article>

                    <article class="info-card">

                        <div class="icon">
                            💻
                        </div>

                        <h3>
                            Web Development
                        </h3>

                        <p>
                            I am learning how HTML, CSS,
                            and JavaScript work together.
                        </p>

                    </article>

                    <article class="info-card">

                        <div class="icon">
                            📚
                        </div>

                        <h3>
                            Always Learning
                        </h3>

                        <p>
                            Every project helps me improve
                            my skills and understand new tools.
                        </p>

                    </article>

                </div>

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
