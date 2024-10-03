<?php
session_start();

require_once __DIR__ . '/../../db_connection.php';
if (!isset($_SESSION['player_id']) || $_SESSION['status'] !== 'superadmin') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_id = $_POST['player_id'];

    // Start en transaktion
    $mysqli->begin_transaction();

    try {
        // Slet fra player_metadata tabellen
        $stmt = $mysqli->prepare("DELETE FROM player_metadata WHERE player_id = ?");
        $stmt->bind_param("s", $player_id);
        $stmt->execute();
        $stmt->close();

        // Slet fra players tabellen
        $stmt = $mysqli->prepare("DELETE FROM players WHERE player_id = ?");
        $stmt->bind_param("s", $player_id);
        $stmt->execute();
        $stmt->close();

        // Udfør transaktionen
        $mysqli->commit();

        $_SESSION['success'] = "Spilleren blev slettet succesfuldt.";
    } catch (Exception $e) {
        // Hvis der opstår en fejl, annuller transaktionen
        $mysqli->rollback();
        $_SESSION['error'] = "Fejl: " . $e->getMessage();
    }

    header('Location: ../../index.php');
    exit();
}
?>
