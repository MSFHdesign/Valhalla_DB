<?php
session_start();


if (!isset($_SESSION['player_id'])) {
    header('Location: index.php');
    exit();
}

// Antal poster pr. side
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // Default er 10 poster pr. side
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Default er side 1
$offset = ($page - 1) * $limit;

// Sorteringsparametre
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'username'; // Default sortering er på brugernavn
$sort_order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'desc' : 'asc'; // Default sorteringsrækkefølge er stigende

// Skift sorteringsrækkefølge
$next_sort_order = $sort_order == 'asc' ? 'desc' : 'asc';

// Hent det totale antal spillere
$total_query = "SELECT COUNT(*) as total FROM players";
$total_result = $mysqli->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_players = $total_row['total'];
$total_pages = ceil($total_players / $limit);

// Hent spillernes liste og metadata med sortering
$query = "
    SELECT p.player_id, p.username, p.email, p.status, p.online, m.created_at, m.updated_at, m.last_login, 
           IFNULL(SUM(s.play_time), 0) as total_play_time,
           TIMESTAMPDIFF(SECOND, MAX(s.session_end), NOW()) as time_since_last_session
    FROM players p
    LEFT JOIN player_metadata m ON p.player_id = m.player_id
    LEFT JOIN player_sessions s ON p.player_id = s.player_id
    GROUP BY p.player_id, p.username, p.email, p.status, p.online, m.created_at, m.updated_at, m.last_login
    ORDER BY $sort_column $sort_order
    LIMIT $limit OFFSET $offset
";
$result = $mysqli->query($query);

// Funktion til at formatere sekunder til timer, minutter og sekunder
function format_time($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

?>

<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spillerliste</title>
    <style>
        .status-circle {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .online { background-color: green; }
        .offline { background-color: red; }
    </style>
</head>
<body>
    <h2>Spillerliste</h2>
    <?php if ($_SESSION['status'] === 'superadmin'): ?>
        <form method="get">
            <label for="limit">Antal poster pr. side:</label>
            <select id="limit" name="limit" onchange="this.form.submit()">
                <option value="5" <?php if ($limit == 5) echo 'selected'; ?>>5</option>
                <option value="10" <?php if ($limit == 10) echo 'selected'; ?>>10</option>
                <option value="25" <?php if ($limit == 25) echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($limit == 50) echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($limit == 100) echo 'selected'; ?>>100</option>
            </select>
            <input type="hidden" name="sort" value="<?php echo $sort_column; ?>">
            <input type="hidden" name="order" value="<?php echo $sort_order; ?>">
        </form>
    
        <table border="1">
            <thead>
                <tr>
                    <th><a href="?limit=<?php echo $limit; ?>&sort=player_id&order=<?php echo $next_sort_order; ?>">ID</a></th>
                    <th><a href="?limit=<?php echo $limit; ?>&sort=username&order=<?php echo $next_sort_order; ?>">Brugernavn</a></th>
                    <th><a href="?limit=<?php echo $limit; ?>&sort=email&order=<?php echo $next_sort_order; ?>">Email</a></th>
                    <th><a href="?limit=<?php echo $limit; ?>&sort=status&order=<?php echo $next_sort_order; ?>">Status</a></th>
                    <th>Online</th>
                    <th><a href="?limit=<?php echo $limit; ?>&sort=created_at&order=<?php echo $next_sort_order; ?>">Oprettet</a></th>
                    <th><a href="?limit=<?php echo $limit; ?>&sort=last_login&order=<?php echo $next_sort_order; ?>">Sidst Logget Ind</a></th>
                    <th>Total Spilletid</th>
                    <th>Tid Siden Sidste Session</th>
                    <th>Karakterer</th>
                    <th>Handlinger</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['player_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td style="<?php
                            if ($row['status'] == 'active') {
                                echo 'background-color: green; color: black;';
                            } elseif ($row['status'] == 'superadmin') {
                                echo 'background-color: yellow; color: black;';
                            } elseif ($row['status'] == 'inactive') {
                                echo 'background-color: red; color: white;';
                            } elseif ($row['status'] == 'blocked') {
                                echo 'background-color: black; color: white;';
                            }
                        ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <span class="status-circle <?php echo $row['online'] ? 'online' : 'offline'; ?>"></span>
                        </td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_login']); ?></td>
                        <td><?php echo format_time($row['total_play_time']); ?></td>
                        <td><?php echo format_time($row['time_since_last_session']); ?></td>
                        <td>
                            <?php
                            $player_id = $row['player_id'];
                            $characters_query = "
                                SELECT c.character_id, c.character_name, c.class, s.level
                                FROM characters c
                                LEFT JOIN character_stats s ON c.character_id = s.character_id
                                WHERE c.player_id = '$player_id'
                            ";
                            $characters_result = $mysqli->query($characters_query);
                            if ($characters_result->num_rows > 0) {
                                while ($character = $characters_result->fetch_assoc()) {
                                    echo "Navn: " . htmlspecialchars($character['character_name']) . 
                                         " Klasse: " . htmlspecialchars($character['class']) . 
                                         " Level: " . htmlspecialchars($character['level']) . 
                                      
                                         "<form action='frontend/players/delete_character.php' method='post' onsubmit='return confirm(\"Er du sikker på, at du vil slette denne karakter?\");' style='display:inline;'>
                                            <input type='hidden' name='character_id' value='" . htmlspecialchars($character['character_id']) . "'>
                                            <button type='submit' style='background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer;'>Slet</button>
                                          </form>
                                          <br>";
                                }
                            } else {
                                echo "Ingen karakterer fundet";
                            }
                            ?>
                        </td>
                        <td>
                            <form action="frontend/players/delete_player.php" method="post" onsubmit="return confirm('Er du sikker på, at du vil slette denne spiller?');" style="display:inline;">
                                <input type="hidden" name="player_id" value="<?php echo htmlspecialchars($row['player_id']); ?>">
                                <button type="submit" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer;">Slet</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?limit=<?php echo $limit; ?>&page=<?php echo $i; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>" style="margin: 0 5px;">side <?php echo $i; ?> af <?php echo $total_pages; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</body>
</html>
