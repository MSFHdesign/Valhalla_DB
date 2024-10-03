<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT player_id, password, status FROM players WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($player_id, $hashed_password, $status);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['player_id'] = $player_id;
            $_SESSION['status'] = $status;
            $_SESSION['username'] = $username;

            $update_stmt = $mysqli->prepare("UPDATE player_metadata SET last_login = NOW() WHERE player_id = ?");
            $update_stmt->bind_param("s", $player_id);
            $update_stmt->execute();
            $update_stmt->close();

            $insert_session_stmt = $mysqli->prepare("INSERT INTO player_sessions (player_id, session_start) VALUES (?, NOW())");
            $insert_session_stmt->bind_param("s", $player_id);
            $insert_session_stmt->execute();
            $insert_session_stmt->close();

            $update_online_stmt = $mysqli->prepare("UPDATE players SET online = TRUE WHERE player_id = ?");
            $update_online_stmt->bind_param("s", $player_id);
            $update_online_stmt->execute();
            $update_online_stmt->close();

            header('Location: index.php');
            exit();
        } else {
            $error = "Forkert brugernavn eller password.";
        }
    } else {
        $error = "Forkert brugernavn eller password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="username">Brugernavn:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Log ind</button>
    </form>
</body>
</html>
