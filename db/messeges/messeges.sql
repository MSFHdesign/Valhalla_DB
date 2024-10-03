CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY, -- Unik ID for hver besked
    sender_id CHAR(36) NOT NULL, -- ID på afsenderen
    recipient_id CHAR(36) DEFAULT NULL, -- ID på modtageren, NULL hvis beskeden er til alle
    message TEXT NOT NULL, -- Beskedens indhold
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Tidsstempel for hvornår beskeden blev oprettet
    FOREIGN KEY (sender_id) REFERENCES players(player_id), -- Fremmednøgle referencer til players tabellen
    FOREIGN KEY (recipient_id) REFERENCES players(player_id) -- Fremmednøgle referencer til players tabellen
);
