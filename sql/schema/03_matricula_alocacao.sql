-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Matrícula e alocação docente (MER: MATRICULA / participa, ALOCACAO / leciona)

CREATE TABLE matricula (
    cod_matricula VARCHAR(36) NOT NULL,
    id_usuario VARCHAR(36) NOT NULL,
    cod_turma VARCHAR(36) NOT NULL,
    ativo TINYINT(1) NULL,
    PRIMARY KEY (cod_matricula),
    UNIQUE KEY uk_matricula_aluno_turma (id_usuario, cod_turma),
    KEY idx_matricula_cod_turma (cod_turma),
    CONSTRAINT fk_matricula_aluno
        FOREIGN KEY (id_usuario) REFERENCES aluno (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_matricula_turma
        FOREIGN KEY (cod_turma) REFERENCES turma (cod_turma)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE alocacao (
    id_alocacao VARCHAR(36) NOT NULL,
    id_usuario VARCHAR(36) NOT NULL,
    cod_turma VARCHAR(36) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_alocacao),
    UNIQUE KEY uk_alocacao_professor_turma (id_usuario, cod_turma),
    KEY idx_alocacao_cod_turma (cod_turma),
    CONSTRAINT fk_alocacao_professor
        FOREIGN KEY (id_usuario) REFERENCES professor (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_alocacao_turma
        FOREIGN KEY (cod_turma) REFERENCES turma (cod_turma)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
