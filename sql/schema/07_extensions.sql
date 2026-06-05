-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Extensions for prototype parity (PI groups, rubric templates)

ALTER TABLE projeto ADD COLUMN nome_grupo VARCHAR(120) NULL AFTER titulo;

CREATE TABLE rubrica_criterio (
    id_criterio VARCHAR(36) NOT NULL,
    cod_turma VARCHAR(36) NULL,
    nome VARCHAR(100) NOT NULL,
    peso DECIMAL(5, 2) NOT NULL DEFAULT 1.00,
    ordem SMALLINT NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_criterio),
    KEY idx_rubrica_criterio_cod_turma (cod_turma),
    CONSTRAINT fk_rubrica_criterio_turma
        FOREIGN KEY (cod_turma) REFERENCES turma (cod_turma)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
