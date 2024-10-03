CREATE TABLE IF NOT EXISTS characters (
    character_id CHAR(36) NOT NULL UNIQUE PRIMARY KEY, -- Unik UUID for hver karakter
    player_id CHAR(36) NOT NULL, -- Spillerens UUID
    character_name VARCHAR(255) UNIQUE NOT NULL, -- Karakterens navn
    class ENUM('Thor', 'Forseti', 'Loki','Freyja','Skadi') NOT NULL, -- Karakterens klasse, tilpas efter behov
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Tidsstempel for hvornår karakteren blev oprettet
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Tidsstempel for sidste opdatering af karakterens oplysninger
    FOREIGN KEY (player_id) REFERENCES players(player_id) -- Fremmednøgle referencer til players tabellen
);
