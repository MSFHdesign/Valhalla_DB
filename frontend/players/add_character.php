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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $character_name = $_POST['character_name'];
    $class = $_POST['class'];
    $player_id = $_SESSION['player_id']; // Brug spillerens session ID

    // Generer UUID for karakteren
    $character_id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );

    // Start en transaktion
    $mysqli->begin_transaction();

    try {
        // Indsæt ny karakter i characters tabellen
        $stmt = $mysqli->prepare("INSERT INTO characters (character_id, player_id, character_name, class) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("ssss", $character_id, $player_id, $character_name, $class);
        if (!$stmt->execute()) {
            if ($stmt->errno === 1062) { // Duplicate entry
                throw new Exception("Karakterens navn er allerede taget.");
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        }
        $stmt->close();

        // Indsæt standard stats for den nye karakter i character_stats tabellen
        $stmt = $mysqli->prepare("INSERT INTO character_stats (character_id) VALUES (?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("s", $character_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Udfør transaktionen
        $mysqli->commit();

        $success = "Ny karakter tilføjet succesfuldt.";
    } catch (Exception $e) {
        // Hvis der opstår en fejl, annuller transaktionen
        $mysqli->rollback();
        $error = "Fejl: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Tilføj Karakter</title>
</head>
<body>
    <h2>Tilføj Karakter (debugging)</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="character_name">Karakter Navn:</label>
        <input type="text" id="character_name" name="character_name" required><br>
        <label for="class">Klasse:</label>
        <select id="class" name="class" required>
            <option value="Thor">Thor</option>
            <option value="Forseti">Forseti</option>
            <option value="Loki">Loki</option>
            <option value="Freyja">Freyja</option>
            <option value="Skadi">Skadi</option>
        </select><br>
        <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Tilføj Karakter</button>
    </form>
    <br>

</body>
</html>
