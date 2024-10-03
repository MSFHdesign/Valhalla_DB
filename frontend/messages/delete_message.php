<?php
session_start();
require_once __DIR__ . '/../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_id = $_POST['message_id'];

    // Slet besked fra databasen
    $stmt = $mysqli->prepare("DELETE FROM messages WHERE message_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: ../../index.php');
exit();
?>
