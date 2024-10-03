CREATE TABLE IF NOT EXISTS players (
    player_id CHAR(36) NOT NULL UNIQUE PRIMARY KEY, -- Unik UUID for hver spiller
    email VARCHAR(255) NOT NULL UNIQUE, -- Spillerens email, som skal være unik
    username VARCHAR(255) NOT NULL UNIQUE, -- Spillerens brugernavn, som skal være unik
    password VARCHAR(255) NOT NULL, -- Spillerens password
    status ENUM('active', 'inactive', 'superadmin', 'blocked') DEFAULT 'inactive' -- Spillerens status
    online BOOLEAN DEFAULT FALSE
);


