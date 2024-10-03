<?php
session_start();


if (!isset($_SESSION['player_id']) || $_SESSION['status'] !== 'superadmin') {
    header('Location: index.php');
    exit();
}

// Håndter formularindsendelse for at opdatere logs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resolve'])) {
        $log_id = $_POST['log_id'];
        $stmt = $mysqli->prepare("UPDATE debug_log SET resolved = TRUE WHERE log_id = ?");
        $stmt->bind_param("i", $log_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['unresolve'])) {
        $log_id = $_POST['log_id'];
        $stmt = $mysqli->prepare("UPDATE debug_log SET resolved = FALSE WHERE log_id = ?");
        $stmt->bind_param("i", $log_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['hide'])) {
        $log_id = $_POST['log_id'];
        $stmt = $mysqli->prepare("DELETE FROM debug_log WHERE log_id = ?");
        $stmt->bind_param("i", $log_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Hent alle logs der ikke er resolved
$query_unresolved = "SELECT log_id, player_id, message, created_at, resolved, ticket_number, location FROM debug_log WHERE resolved = FALSE";
$result_unresolved = $mysqli->query($query_unresolved);

// Hent alle logs der er resolved
$query_resolved = "SELECT log_id, player_id, message, created_at, resolved, ticket_number, location FROM debug_log WHERE resolved = TRUE";
$result_resolved = $mysqli->query($query_resolved);
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Logs</title>
</head>
<body>
    <h2>Uløste Debug Logs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Spiller ID</th>
                <th>Besked</th>
                <th>Tidspunkt</th>
                <th>Resolved</th>
                <th>Ticket Nummer</th>
                <th>Lokation</th>
                <th>Handlinger</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_unresolved->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['log_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['player_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['message']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo $row['resolved'] ? 'Ja' : 'Nej'; ?></td>
                    <td><?php echo htmlspecialchars($row['ticket_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td>
                        <?php if (!$row['resolved']): ?>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Er du sikker på, at du vil lukke denne ticket?');">
                                <input type="hidden" name="log_id" value="<?php echo htmlspecialchars($row['log_id']); ?>">
                                <button type="submit" name="resolve" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer;">Resolve</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Er du sikker på, at du vil slette denne?');">
                            <input type="hidden" name="log_id" value="<?php echo htmlspecialchars($row['log_id']); ?>">
                            <button type="submit" name="hide" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Løste Debug Logs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Spiller ID</th>
                <th>Besked</th>
                <th>Tidspunkt</th>
                <th>Resolved</th>
                <th>Ticket Nummer</th>
                <th>Lokation</th>
                <th>Handlinger</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_resolved->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['log_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['player_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['message']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo $row['resolved'] ? 'Ja' : 'Nej'; ?></td>
                    <td><?php echo htmlspecialchars($row['ticket_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td>
                        <?php if ($row['resolved']): ?>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Er du sikker på, at du vil åbne den igen?');">
                                <input type="hidden" name="log_id" value="<?php echo htmlspecialchars($row['log_id']); ?>">
                                <button type="submit" name="unresolve" style="background-color: #008CBA; color: white; border: none; padding: 5px 10px; cursor: pointer;">Open again</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Er du sikker på, at du vil slette denne ticket');">
                            <input type="hidden" name="log_id" value="<?php echo htmlspecialchars($row['log_id']); ?>">
                            <button type="submit" name="hide" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <br>

</body>
</html>
