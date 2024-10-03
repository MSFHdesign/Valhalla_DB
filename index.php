<?php
session_start();
require_once __DIR__ . '/db_connection.php';
include_once __DIR__ . '/UUID.php';
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="labels.css">
    <title>Dashboard</title>
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }
        .box {
            flex: 1 1 calc(33.333% - 32px);
            border: 1px solid #ccc;
            padding: 16px;
            box-sizing: border-box;
        }
        @media (max-width: 768px) {
            .box {
                flex: 1 1 calc(50% - 32px);
            }
        }
        @media (max-width: 480px) {
            .box {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['player_id'])): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 10px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #333; font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 24px; margin: 0;">
                Velkommen, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </h2>
            <p style="margin: 0;">
                <a href="/frontend/UI/logout.php" style="color: #007BFF; text-decoration: none; font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 16px;">Log ud</a>
            </p>
        </div>

        <div class="container">
            <?php if ($_SESSION['status'] === 'superadmin'): ?>
                <div class="box"><?php include 'frontend/players/add_player.php'; ?></div>
                <div class="box"><?php include 'frontend/players/add_character.php'; ?></div>
                <div class="box"><?php include 'frontend/logs/view_logs.php'; ?></div>
                <div class="box"><?php include 'frontend/logs/add_log.php'; ?></div>
            <?php endif; ?>
            <div class="box"><?php include 'frontend/messages/view_messages.php'; ?></div>
            <div class="box"><?php include 'frontend/messages/send_message.php'; ?></div>
        </div>
        <?php include 'frontend/UI/status_summary.php'; ?>
        <div class="box"><?php include 'frontend/players/player_list.php'; ?></div>
    <?php else: ?>
        <?php include './frontend/UI/login.php'; ?>
    <?php endif; ?>
</body>
</html>
