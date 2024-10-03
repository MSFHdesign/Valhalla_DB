CREATE TABLE IF NOT EXISTS character_stats (
    stat_id INT AUTO_INCREMENT PRIMARY KEY, -- Unik ID for hver stat post
    character_id CHAR(36) NOT NULL, -- Karakterens UUID
    level INT NOT NULL DEFAULT 1, -- Karakterens level
    xp INT NOT NULL DEFAULT 0, -- Karakterens erfaringpoint
    hp INT NOT NULL DEFAULT 100, -- Karakterens hit points
    mp INT NOT NULL DEFAULT 50, -- Karakterens mana points
    strength INT NOT NULL DEFAULT 10, -- Karakterens styrke
    agility INT NOT NULL DEFAULT 10, -- Karakterens smidighed
    intelligence INT NOT NULL DEFAULT 10, -- Karakterens intelligens
    FOREIGN KEY (character_id) REFERENCES characters(character_id) -- Fremmednøgle referencer til characters tabellen
);
