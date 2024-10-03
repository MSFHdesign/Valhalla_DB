<?php
session_start();
require_once __DIR__ . '/../../db_connection.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['player_id'];
    $recipient_id = $_POST['recipient_id'];
    $message = $_POST['message'];

    // Forbered og udfør SQL-spørgsmål
    $stmt = $mysqli->prepare("INSERT INTO messages (sender_id, recipient_id, message) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $sender_id, $recipient_id, $message);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: ../../index.php');
exit();
?>
