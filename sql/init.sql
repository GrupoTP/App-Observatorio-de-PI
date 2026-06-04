-- Initial schema for Observatório de Projetos Integradores (OPI)
-- Executed automatically on first MySQL container startup.

CREATE TABLE IF NOT EXISTS app_health (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    checked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL DEFAULT 'ok'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO app_health (status) VALUES ('initialized');
