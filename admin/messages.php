
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';

if (empty($_SESSION['messages_csrf'])) {
    $_SESSION['messages_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['messages_csrf'], $token)) {
        $error = 'Invalid form submission. Please refresh the page.';

    } else {

        $action = $_POST['action'] ?? '';
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            $error = 'Invalid message ID.';

        } elseif ($action === 'toggle') {

            $stmt = $pdo->prepare("
                UPDATE messages
                SET is_read = CASE WHEN is_read = 1 THEN 0 ELSE 1 END
                WHERE id = ?
            ");

            $stmt->execute([$id]);

            header('Location: messages.php?updated=1');
            exit;

        } elseif ($action === 'delete') {

            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
            $stmt->execute([$id]);

            header('Location: messages.php?deleted=1');
            exit;

        } else {
            $error = 'Invalid action.';
        }
    }
}

$stmt = $pdo->query("
    SELECT * FROM messages
    ORDER BY created_at DESC
");

$messageList = $stmt->fetchAll();

$success = '';

if (isset($_GET['updated'])) {
    $success = 'Message status updated successfully!';
} elseif (isset($_GET['deleted'])) {
    $success = 'Message deleted successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Messages Management</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        .messages-admin {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .message-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            overflow-wrap: anywhere;
        }

        .message-card.unread {
            border-left: 5px solid #2563eb;
            background: #f5f8ff;
        }

        .message-card.read {
            border-left: 5px solid #999;
        }

        .message-card button {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            background: #222;
            color: white;
            cursor: pointer;
            margin: 4px;
        }

        .message-card .delete-button {
            background: #b42318;
        }

        .message-alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .message-success {
            background: #e1f5e5;
            color: #176b2c;
        }

        .message-error {
            background: #ffe5e5;
            color: #a00000;
        }

        .message-status {
            font-weight: bold;
        }
    </style>
</head>

<body>

<main class="messages-admin">

    <h1>Messages Management</h1>

    <p>View and manage messages submitted through your contact form.</p>

    <?php if ($success): ?>
        <p class="message-alert message-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="message-alert message-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <h2>Received Messages (<?= count($messageList) ?>)</h2>

    <?php if (empty($messageList)): ?>

        <p>No messages received yet.</p>

    <?php else: ?>

        <?php foreach ($messageList as $message): ?>

            <div class="message-card <?= $message['is_read'] ? 'read' : 'unread' ?>">

                <p class="message-status">
                    <?= $message['is_read'] ? 'Read' : 'Unread' ?>
                </p>

                <h3>
                    <?= htmlspecialchars($message['subject'], ENT_QUOTES, 'UTF-8') ?>
                </h3>

                <p>
                    <strong>From:</strong>
                    <?= htmlspecialchars($message['name'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?= htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    <strong>Date:</strong>
                    <?= htmlspecialchars($message['created_at'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p><strong>Message:</strong></p>

                <p>
                    <?= nl2br(htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8')) ?>
                </p>

                <form method="POST" action="messages.php" style="display:inline;">

                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['messages_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="toggle">

                    <input type="hidden" name="id"
                        value="<?= (int) $message['id'] ?>">

                    <button type="submit">
                        Mark as <?= $message['is_read'] ? 'Unread' : 'Read' ?>
                    </button>

                </form>

                <form method="POST" action="messages.php" style="display:inline;"
                    onsubmit="return confirm('Are you sure you want to delete this message?');">

                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['messages_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                    <input type="hidden" name="action" value="delete">

                    <input type="hidden" name="id"
                        value="<?= (int) $message['id'] ?>">

                    <button type="submit" class="delete-button">
                        Delete Message
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <p>
        <a href="index.php">Back to Dashboard</a> |
        <a href="../contact.php" target="_blank">View Contact Page</a>
    </p>

</main>

</body>
</html>
