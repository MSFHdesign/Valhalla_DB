<?php
session_start();
require_once __DIR__ . '/../../db_connection.php';

// Start output buffering for at undgå "headers already sent" fejl
ob_start();

if (!isset($_SESSION['player_id']) || $_SESSION['status'] !== 'superadmin') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $character_id = $_POST['character_id'];

    // Start en transaktion
    $mysqli->begin_transaction();

    try {
        // Slet fra character_stats tabellen
        $stmt = $mysqli->prepare("DELETE FROM character_stats WHERE character_id = ?");
        $stmt->bind_param("s", $character_id);
        $stmt->execute();
        $stmt->close();

        // Slet fra characters tabellen
        $stmt = $mysqli->prepare("DELETE FROM characters WHERE character_id = ?");
        $stmt->bind_param("s", $character_id);
        $stmt->execute();
        $stmt->close();

        // Udfør transaktionen
        $mysqli->commit();

        $_SESSION['success'] = "Karakteren blev slettet succesfuldt.";
    } catch (Exception $e) {
        // Hvis der opstår en fejl, annuller transaktionen
        $mysqli->rollback();
        $_SESSION['error'] = "Fejl: " . $e->getMessage();
    }

    // Omdiriger til index.php efter sletning
    header('Location: index.php');
    exit();
}

// Ryd output buffer
ob_end_flush();
?>
