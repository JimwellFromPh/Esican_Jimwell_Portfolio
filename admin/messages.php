
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

$unreadCount = 0;

foreach ($messageList as $message) {
    if (!$message['is_read']) {
        $unreadCount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Messages Management</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">

    <script src="../js/script.js" defer></script>

    <style>
        .messages-summary {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .messages-summary-item {
            flex: 1;
            min-width: 140px;
            padding: 18px;
            border: 1px solid var(--admin-border, #303442);
            border-radius: 12px;
        }

        .messages-summary-item strong {
            display: block;
            font-size: 25px;
            margin-top: 5px;
        }

        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .message-item {
            padding: 22px;
            border: 1px solid var(--admin-border, #303442);
            border-radius: 14px;
            background: var(--admin-card-bg, #191c26);
            overflow-wrap: anywhere;
        }

        .message-item.unread {
            border-left: 4px solid #6868e8;
        }

        .message-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }

        .message-sender h3 {
            margin: 0 0 5px;
        }

        .message-sender p {
            margin: 0;
            font-size: 14px;
            color: var(--admin-muted, #a0a4b5);
        }

        .message-status {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .message-status.unread {
            background: rgba(104, 104, 232, 0.15);
            color: #8585ff;
        }

        .message-status.read {
            background: rgba(128, 128, 128, 0.15);
            color: #999;
        }

        .message-subject {
            font-size: 18px;
            font-weight: 600;
            margin: 18px 0 12px;
        }

        .message-date {
            font-size: 13px;
            color: var(--admin-muted, #a0a4b5);
        }

        .message-content {
            padding: 16px;
            margin: 12px 0 18px;
            border-radius: 10px;
            background: rgba(128, 128, 128, 0.08);
            line-height: 1.7;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .message-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .message-actions form {
            margin: 0;
        }

        .message-actions button {
            margin: 0;
        }

        .message-delete-button {
            background: #b42318 !important;
        }

        .messages-empty {
            text-align: center;
            padding: 45px 20px;
        }

        .messages-empty h3 {
            margin-bottom: 8px;
        }

        @media (max-width: 600px) {
            .message-top {
                flex-direction: column;
            }

            .message-actions {
                flex-direction: column;
            }

            .message-actions form,
            .message-actions button {
                width: 100%;
            }
        }
    </style>
</head>

<body class="admin-body">

<main class="admin-page">

    <header class="admin-header">
        <div>
            <h1>Messages</h1>
            <p>View and manage messages submitted through your contact form.</p>
        </div>

        <button type="button" class="theme-toggle" aria-label="Toggle theme">
            🌙
        </button>
    </header>

    <?php if ($success): ?>
        <p class="admin-message admin-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="admin-message admin-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <section class="admin-panel">

        <div class="messages-summary">

            <div class="messages-summary-item">
                <span>Total Messages</span>
                <strong><?= count($messageList) ?></strong>
            </div>

            <div class="messages-summary-item">
                <span>Unread Messages</span>
                <strong><?= $unreadCount ?></strong>
            </div>

        </div>

        <h2>Inbox</h2>

        <?php if (empty($messageList)): ?>

            <div class="messages-empty">
                <h3>No messages yet</h3>
                <p>Messages submitted through your contact form will appear here.</p>
            </div>

        <?php else: ?>

            <div class="messages-list">

                <?php foreach ($messageList as $message): ?>

                    <article class="message-item <?= $message['is_read'] ? 'read' : 'unread' ?>">

                        <div class="message-top">

                            <div class="message-sender">
                                <h3>
                                    <?= htmlspecialchars($message['name'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>

                            <span class="message-status <?= $message['is_read'] ? 'read' : 'unread' ?>">
                                <?= $message['is_read'] ? 'Read' : 'Unread' ?>
                            </span>

                        </div>

                        <p class="message-date">
                            Received:
                            <?= htmlspecialchars($message['created_at'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <h3 class="message-subject">
                            <?= htmlspecialchars($message['subject'], ENT_QUOTES, 'UTF-8') ?>
                        </h3>

                        <div class="message-content">
                            <?= nl2br(htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>

                        <div class="message-actions">

                            <form method="POST" action="messages.php">

                                <input type="hidden" name="csrf_token"
                                    value="<?= htmlspecialchars($_SESSION['messages_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                                <input type="hidden" name="action" value="toggle">

                                <input type="hidden" name="id"
                                    value="<?= (int) $message['id'] ?>">

                                <button type="submit" class="admin-button">
                                    Mark as <?= $message['is_read'] ? 'Unread' : 'Read' ?>
                                </button>

                            </form>

                            <form method="POST" action="messages.php"
                                onsubmit="return confirm('Are you sure you want to delete this message?');">

                                <input type="hidden" name="csrf_token"
                                    value="<?= htmlspecialchars($_SESSION['messages_csrf'], ENT_QUOTES, 'UTF-8') ?>">

                                <input type="hidden" name="action" value="delete">

                                <input type="hidden" name="id"
                                    value="<?= (int) $message['id'] ?>">

                                <button type="submit" class="admin-button message-delete-button">
                                    Delete Message
                                </button>

                            </form>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>

    <div class="admin-footer-actions">
        <a href="index.php">Back to Dashboard</a>
        |
        <a href="../contact.php" target="_blank">View Contact Page</a>
    </div>

</main>

</body>
</html>
