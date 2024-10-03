<?php



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $status = $_POST['status'];

    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM players WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $error = "Brugernavn eller email er allerede taget.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $player_id = generateUUID();

        // Start transaktion
        $mysqli->begin_transaction();

        try {
            $stmt = $mysqli->prepare("INSERT INTO players (player_id, email, username, password, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $player_id, $email, $username, $hashed_password, $status);
            $stmt->execute();
            $stmt->close();

            // Indsæt metadata
            $stmt = $mysqli->prepare("INSERT INTO player_metadata (player_id) VALUES (?)");
            $stmt->bind_param("s", $player_id);
            $stmt->execute();
            $stmt->close();

            // Commit transaktionen
            $mysqli->commit();
            $success = "Ny spiller tilføjet succesfuldt.";
        } catch (Exception $e) {
            // Rollback transaktionen ved fejl
            $mysqli->rollback();
            $error = "Fejl: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tilføj Spiller</title>
</head>
<body>
    <h2>Tilføj Spiller (debugging)</h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form action="index.php" method="post">
        <input type="hidden" name="form_type" value="add_player">
        <label for="username">Brugernavn:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="superadmin">Superadmin</option>
            <option value="blocked">Blocked</option>
        </select><br>
        <button type="submit" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Tilføj Spiller</button>
    </form>
</body>
</html>
