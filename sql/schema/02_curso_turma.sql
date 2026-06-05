-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Estrutura acadêmica (MER: CURSO, TURMA / pertence)

CREATE TABLE curso (
    id_curso VARCHAR(36) NOT NULL,
    nome_curso VARCHAR(255) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_curso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE turma (
    cod_turma VARCHAR(36) NOT NULL,
    id_curso VARCHAR(36) NOT NULL,
    nome_turma VARCHAR(255) NOT NULL,
    turno VARCHAR(50) NOT NULL,
    modulo VARCHAR(100) NOT NULL,
    prazo_projetos DATETIME NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (cod_turma),
    KEY idx_turma_id_curso (id_curso),
    CONSTRAINT fk_turma_curso
        FOREIGN KEY (id_curso) REFERENCES curso (id_curso)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
