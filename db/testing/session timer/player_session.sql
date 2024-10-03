CREATE TABLE IF NOT EXISTS player_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY, -- Unik ID for hver session
    player_id CHAR(36) NOT NULL, -- Spillerens UUID
    session_start TIMESTAMP NOT NULL, -- Tidsstempel for session start
    session_end TIMESTAMP NULL, -- Tidsstempel for session slut
    FOREIGN KEY (player_id) REFERENCES players(player_id) -- Fremmednøgle referencer til players tabellen
);
