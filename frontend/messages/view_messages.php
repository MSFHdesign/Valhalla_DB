<?php
session_start();


if (!isset($_SESSION['player_id'])) {
    header('Location: index.php');
    exit();
}

$player_id = $_SESSION['player_id'];

// Hent beskeder til brugeren
$messages_query = "
    SELECT m.message_id, m.message, m.created_at, p.username AS sender_username, m.sender_id
    FROM messages m
    LEFT JOIN players p ON m.sender_id = p.player_id
    WHERE m.recipient_id IS NULL OR m.recipient_id = ?
    ORDER BY m.created_at DESC
";
$stmt = $mysqli->prepare($messages_query);
$stmt->bind_param("s", $player_id);
$stmt->execute();
$messages_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dine Beskeder</title>
    <style>
        .message {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .actions {
            margin-top: 10px;
        }
        .reply-form {
            display: none;
            margin-top: 10px;
        }
    </style>
    <script>
        function toggleReplyForm(id) {
            var form = document.getElementById('reply-form-' + id);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h2>Dine Beskeder</h2>
    <?php if ($messages_result->num_rows > 0): ?>
        <ul>
            <?php while ($row = $messages_result->fetch_assoc()): ?>
                <li class="message">
                    <strong>Fra: <?php echo htmlspecialchars($row['sender_username']); ?></strong><br>
                    <em><?php echo htmlspecialchars($row['created_at']); ?></em><br>
                    <p><?php echo htmlspecialchars($row['message']); ?></p>
                    <div class="actions">
                        <form method="post" action="frontend/messages/delete_message.php" style="display:inline;" onsubmit="return confirm('Er du sikker på, at du vil slette denne besked?');">
                            <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($row['message_id']); ?>">
                            <button type="submit" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer;">Slet</button>
                        </form>
                        <button onclick="toggleReplyForm(<?php echo $row['message_id']; ?>)" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Besvar</button>
                    </div>
                    <div id="reply-form-<?php echo $row['message_id']; ?>" class="reply-form">
                        <form method="post" action="frontend/messages/send_reply.php">
                            <input type="hidden" name="recipient_id" value="<?php echo htmlspecialchars($row['sender_id']); ?>">
                            <label for="message-<?php echo $row['message_id']; ?>">Besked:</label>
                            <textarea id="message-<?php echo $row['message_id']; ?>" name="message" required></textarea><br>
                            <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Send Besked</button>
                        </form>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Ingen beskeder.</p>
    <?php endif; ?>
</body>
</html>
