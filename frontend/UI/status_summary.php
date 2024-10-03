<?php
session_start();


if (!isset($_SESSION['player_id']) || $_SESSION['status'] !== 'superadmin') {
    header('Location: index.php');
    exit();
}

// Hent antal brugere i hver statuskategori
$status_query = "
    SELECT 
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) AS inactive,
        SUM(CASE WHEN status = 'superadmin' THEN 1 ELSE 0 END) AS superadmin,
        SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) AS blocked,
        SUM(CASE WHEN online = 1 THEN 1 ELSE 0 END) AS online,
        COUNT(*) AS total
    FROM players
";
$status_result = $mysqli->query($status_query);
$status_counts = $status_result->fetch_assoc();

// Hent antal brugere i hver klasse
$class_query = "
    SELECT 
        SUM(CASE WHEN class = 'Thor' THEN 1 ELSE 0 END) AS Thor,
        SUM(CASE WHEN class = 'Forseti' THEN 1 ELSE 0 END) AS Forseti,
        SUM(CASE WHEN class = 'Loki' THEN 1 ELSE 0 END) AS Loki,
        SUM(CASE WHEN class = 'Freyja' THEN 1 ELSE 0 END) AS Freyja,
        SUM(CASE WHEN class = 'Skadi' THEN 1 ELSE 0 END) AS Skadi,
        COUNT(*) AS total
    FROM characters
";
$class_result = $mysqli->query($class_query);
$class_counts = $class_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statusoversigt</title>
    <style>
        .status-summary {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            gap: 20px;
        }
        .status-box {
            flex: 1 1 200px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }
        .active-box { background-color: #28a745; }
        .inactive-box { background-color: #dc3545; }
        .superadmin-box { background-color: #ffc107; color: #212529; }
        .blocked-box { background-color: #343a40; }
        .online-box { background-color: #17a2b8; } /* Online status in blue */
        .class-box {
            flex: 1 1 200px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .thor-box { background-color: #fdd835; color: #212529; } /* Lightning yellow with dark text */
        .forseti-box { background-color: #e0e0e0; color: #212529; } /* Light and justice grey with dark text */
        .loki-box { background-color: #388e3c; color: #fff; } /* Chaos green with white text */
        .freyja-box { background-color: #ec407a; color: #fff; } /* Love and war pink with white text */
        .skadi-box { background-color: #42a5f5; color: #fff; } /* Frost blue with white text */
    </style>
</head>
<body>
    <div class="box">
    <h3>Players Online</h3>
        <div class="status-summary">
            <div class="status-box online-box">
                Online: <?php echo $status_counts['online']; ?>
            </div>
        </div>
        <h3>Statusoversigt (Total: <?php echo $status_counts['total']; ?>)</h3>
        <div class="status-summary">
            <div class="status-box active-box">
                Aktive: <?php echo $status_counts['active']; ?>
            </div>
            <div class="status-box inactive-box">
                Inaktive: <?php echo $status_counts['inactive']; ?>
            </div>
            <div class="status-box superadmin-box">
                Superadmins: <?php echo $status_counts['superadmin']; ?>
            </div>
            <div class="status-box blocked-box">
                Blokerede: <?php echo $status_counts['blocked']; ?>
            </div>
        </div>
        <h3>Klasseoversigt (Total: <?php echo $class_counts['total']; ?>)</h3>
        <div class="status-summary">
            <div class="status-box class-box thor-box">
                Thor: <?php echo $class_counts['Thor']; ?>
            </div>
            <div class="status-box class-box forseti-box">
                Forseti: <?php echo $class_counts['Forseti']; ?>
            </div>
            <div class="status-box class-box loki-box">
                Loki: <?php echo $class_counts['Loki']; ?>
            </div>
            <div class="status-box class-box freyja-box">
                Freyja: <?php echo $class_counts['Freyja']; ?>
            </div>
            <div class="status-box class-box skadi-box">
                Skadi: <?php echo $class_counts['Skadi']; ?>
            </div>
        </div>
       
    </div>
</body>
</html>
