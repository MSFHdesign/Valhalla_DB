CREATE TABLE IF NOT EXISTS debug_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY, -- Unik ID for hver logpost
    player_id CHAR(36) NOT NULL, -- Spillerens ID
    message TEXT NOT NULL, -- Debug beskeden
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Tidsstempel for hvornår loggen blev oprettet
    resolved BOOLEAN NOT NULL DEFAULT FALSE, -- Om problemet er løst
    ticket_number INT NOT NULL, -- Ticket nummer
    location VARCHAR(255) NOT NULL, -- Hvor problemet opstod
    UNIQUE (ticket_number)
);


ALTER TABLE debug_log
    ADD FOREIGN KEY (player_id) REFERENCES players(player_id); -- Fremmednøgle referencer til players tabellen