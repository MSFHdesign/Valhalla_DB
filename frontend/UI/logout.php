<?php
session_start();
require_once __DIR__ . '../../../db_connection.php';

if (isset($_SESSION['player_id'])) {
    $player_id = $_SESSION['player_id'];

    // End the current session
    $session_end_query = "
        UPDATE player_sessions 
        SET session_end = CURRENT_TIMESTAMP, 
            play_time = TIMESTAMPDIFF(SECOND, session_start, CURRENT_TIMESTAMP)
        WHERE player_id = ? AND session_end IS NULL
    ";
    $stmt = $mysqli->prepare($session_end_query);
    $stmt->bind_param("s", $player_id);
    $stmt->execute();
    $stmt->close();

    // Update total play time
    $update_total_play_time_query = "
        UPDATE player_metadata m
        JOIN (
            SELECT player_id, SUM(play_time) as total_play_time
            FROM player_sessions
            GROUP BY player_id
        ) s ON m.player_id = s.player_id
        SET m.total_play_time = s.total_play_time
        WHERE m.player_id = ?
    ";
    $stmt = $mysqli->prepare($update_total_play_time_query);
    $stmt->bind_param("s", $player_id);
    $stmt->execute();
    $stmt->close();

    // Update player online status
    $update_online_status_query = "UPDATE players SET online = FALSE WHERE player_id = ?";
    $stmt = $mysqli->prepare($update_online_status_query);
    $stmt->bind_param("s", $player_id);
    $stmt->execute();
    $stmt->close();
}

session_unset();
session_destroy();
header('Location: /../../../index.php');
exit();
?>
