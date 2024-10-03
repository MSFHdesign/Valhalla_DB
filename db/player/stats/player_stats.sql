CREATE TABLE IF NOT EXISTS player_stats (
    stat_id INT AUTO_INCREMENT PRIMARY KEY, -- Unik ID for hver stat post
    player_id CHAR(36) NOT NULL, -- Spillerens UUID
    level INT NOT NULL, -- Spillerens nuværende level
    hp INT NOT NULL, -- Spillerens HP
    crit INT NOT NULL, -- Spillerens Crit
    FOREIGN KEY (player_id) REFERENCES players(player_id) -- Fremmednøgle referencer til players tabellen
);
