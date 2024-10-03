CREATE TABLE IF NOT EXISTS player_inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY, -- Unik ID for hver inventory post
    player_id CHAR(36) NOT NULL, -- Spillerens UUID
    item_id INT NOT NULL, -- ID for itemet
    quantity INT NOT NULL, -- Mængde af itemet
    FOREIGN KEY (player_id) REFERENCES players(player_id) -- Fremmednøgle referencer til players tabellen
);
