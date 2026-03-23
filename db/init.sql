CREATE TABLE IF NOT EXISTS attack_logs (
    id          SERIAL PRIMARY KEY,
    attack_type VARCHAR(50)  NOT NULL,
    payload     TEXT         NOT NULL,
    ip_address  VARCHAR(45),
    uri         TEXT,
    blocked     BOOLEAN      DEFAULT TRUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);