CREATE TABLE IF NOT EXISTS player_metadata (
    metadata_id INT AUTO_INCREMENT PRIMARY KEY, -- Unik ID for hver metadata post
    player_id CHAR(36) NOT NULL, -- Spillerens UUID
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Tidsstempel for hvornår spilleren oprettede sig
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Tidsstempel for sidste opdatering af spillerens oplysninger
    last_login TIMESTAMP NULL DEFAULT NULL, -- Tidsstempel for sidste login
    FOREIGN KEY (player_id) REFERENCES players(player_id) -- Fremmednøgle referencer til players tabellen
    total_play_time INT DEFAULT 0;
    time_since_last_session INT DEFAULT 0;
);



ALTER TABLE player_metadata
