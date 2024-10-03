<?php
session_start();


if (!isset($_SESSION['player_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Håndter formularindsendelse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['player_id'];
    $recipient_id = $_POST['recipient_id'] === 'all' ? NULL : $_POST['recipient_id'];
    $message = $_POST['message'];

    // Forbered og udfør SQL-spørgsmål
    $stmt = $mysqli->prepare("INSERT INTO messages (sender_id, recipient_id, message) VALUES (?, ?, ?)");
    if (!$stmt) {
        $error = "Prepare failed: " . $mysqli->error;
    } else {
        $stmt->bind_param("sss", $sender_id, $recipient_id, $message);

        if ($stmt->execute()) {
            $success = "Beskeden er sendt.";
        } else {
            $error = "Fejl: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Hent brugere til dropdown
$users_query = "SELECT player_id, username FROM players";
$users_result = $mysqli->query($users_query);
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Send Besked</title>
</head>
<body>
    <h2>Send Besked</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="recipient_id">Modtager:</label>
        <select id="recipient_id" name="recipient_id" required>
            <?php if ($_SESSION['status'] === 'superadmin'): ?>
                <option value="all">Alle</option>
            <?php endif; ?>
            <?php while ($row = $users_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($row['player_id']); ?>"><?php echo htmlspecialchars($row['username']); ?></option>
            <?php endwhile; ?>
        </select><br>
        <label for="message">Besked:</label>
        <textarea id="message" name="message" required></textarea><br>
        <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Send Besked</button>
    </form>
    <br>
</body>
</html>
