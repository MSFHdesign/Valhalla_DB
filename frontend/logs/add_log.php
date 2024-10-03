<?php
session_start();


// Tjek om brugeren er logget ind
if (!isset($_SESSION['player_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Håndter formularindsendelse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_id = $_SESSION['player_id'];
    $message = $_POST['message'];
    $location = $_POST['location'];

    // Generer et unikt ticket-nummer
    $ticket_number_query = "SELECT MAX(ticket_number) AS max_ticket_number FROM debug_log";
    $result = $mysqli->query($ticket_number_query);
    $row = $result->fetch_assoc();
    $ticket_number = $row['max_ticket_number'] + 1;

    // Forbered og udfør SQL-spørgsmål
    $stmt = $mysqli->prepare("INSERT INTO debug_log (player_id, message, ticket_number, location) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $error = "Prepare failed: " . $mysqli->error;
    } else {
        $stmt->bind_param("ssis", $player_id, $message, $ticket_number, $location);

        if ($stmt->execute()) {
            $success = "Loggen er tilføjet.";
        } else {
            $error = "Fejl: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Tilføj Debug Log</title>
</head>
<body>
    <h2>Tilføj Debug Log (debugging)</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="message">Besked:</label>
        <textarea id="message" name="message" required></textarea><br>
        <label for="location">Lokation:</label>
        <input type="text" id="location" name="location" required><br>
        <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Tilføj Log</button>
    </form>
    <br>

</body>
</html>
