
<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create CSRF token
if (empty($_SESSION['contact_csrf'])) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}

$errors = [];

$name = '';
$email = '';
$subject = '';
$message = '';

// Display success message after redirect
$success = $_SESSION['contact_success'] ?? '';
unset($_SESSION['contact_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // CSRF validation
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['contact_csrf'], $_POST['csrf_token'])
    ) {
        $errors[] = 'Invalid form submission. Please refresh the page and try again.';
    }

    // Honeypot spam protection
    if (!empty($_POST['website'] ?? '')) {
        $errors[] = 'Invalid submission.';
    }

    // Validate name
    if ($name === '' || strlen($name) > 100) {
        $errors[] = 'Please enter a name with no more than 100 characters.';
    }

    // Validate email
    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 100
    ) {
        $errors[] = 'Please enter a valid email address.';
    }

    // Validate subject
    if ($subject === '' || strlen($subject) > 150) {
        $errors[] = 'Please enter a subject with no more than 150 characters.';
    }

    // Validate message
    if ($message === '' || strlen($message) > 10000) {
        $errors[] = 'Please enter a message with no more than 10,000 characters.';
    }

    // Submission cooldown
    if (
        isset($_SESSION['last_contact_submission']) &&
        time() - $_SESSION['last_contact_submission'] < 15
    ) {
        $errors[] = 'Please wait a few seconds before sending another message.';
    }

    // Save message if there are no errors
    if (empty($errors)) {

        try {
            $stmt = $pdo->prepare("
                INSERT INTO messages (name, email, subject, message)
                VALUES (:name, :email, :subject, :message)
            ");

            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message
            ]);

            $_SESSION['last_contact_submission'] = time();
            $_SESSION['contact_success'] = 'Your message has been sent successfully!';

            // Redirect to prevent duplicate submissions
            header('Location: contact.php');
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors[] = 'Something went wrong. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Portfolio | Contact</title>

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
                GET IN TOUCH
            </p>

            <h1>
                Contact Me
            </h1>

            <p>
                If you have any questions, ideas, or would
                like to connect with me, feel free to send
                me a message.
            </p>

        </section>

        <section class="contact-section container">

            <div class="contact-info">

                <h2>
                    Let's Connect
                </h2>

                <p>
                    I am always open to learning, sharing
                    ideas, and connecting with other people.
                </p>

                <div class="contact-item">

                    <div class="contact-icon">
                        📧
                    </div>

                    <div>
                        <h3>Email</h3>
                        <p>
                            jimwellesican415@gmail.com
                        </p>
                    </div>

                </div>

                <div class="contact-item">

                    <div class="contact-icon">
                        📱
                    </div>

                    <div>
                        <h3>Phone</h3>
                        <p>
                            +63 961 905 6564
                        </p>
                    </div>

                </div>

                <div class="contact-item">

                    <div class="contact-icon">
                        📍
                    </div>

                    <div>
                        <h3>Location</h3>
                        <p>
                            Bayawan City, Negros Oriental, Philippines
                        </p>
                    </div>

                </div>

            </div>

            <div class="contact-form-container">

                <h2>
                    Send Me a Message
                </h2>

                <?php if ($success): ?>
                    <p class="form-success">
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="form-error">
                        <?php foreach ($errors as $error): ?>
                            <p>
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form id="contactFormServer" method="POST" action="contact.php">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['contact_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Hidden spam-trap field -->
                    <div style="position:absolute; left:-9999px;" aria-hidden="true">
                        <label for="website">Leave this field empty</label>
                        <input
                            type="text"
                            id="website"
                            name="website"
                            tabindex="-1"
                            autocomplete="off">
                    </div>

                    <div class="form-group">

                        <label for="name">
                            Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Enter your name"
                            maxlength="100"
                            value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                            required>

                    </div>

                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            maxlength="100"
                            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                            required>

                    </div>

                    <div class="form-group">

                        <label for="subject">
                            Subject
                        </label>

                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            placeholder="Enter your subject"
                            maxlength="150"
                            value="<?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') ?>"
                            required>

                    </div>

                    <div class="form-group">

                        <label for="message">
                            Message
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            maxlength="10000"
                            placeholder="Write your message here..."
                            required><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></textarea>

                    </div>

                    <button
                        type="submit"
                        class="contact-button">

                        Send Message

                    </button>

                </form>

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
