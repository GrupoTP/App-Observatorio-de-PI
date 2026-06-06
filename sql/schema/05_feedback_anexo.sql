-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Feedback de avaliação e anexos (MER: FEEDBACK / avalia, ANEXO / contem, envia)

CREATE TABLE feedback (
    id_feedback BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_projeto VARCHAR(36) NOT NULL,
    id_usuario VARCHAR(36) NOT NULL,
    descricao TEXT NOT NULL,
    data DATETIME NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_feedback),
    KEY idx_feedback_id_projeto (id_projeto),
    KEY idx_feedback_id_usuario (id_usuario),
    CONSTRAINT fk_feedback_projeto
        FOREIGN KEY (id_projeto) REFERENCES projeto (id_projeto)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_feedback_professor
        FOREIGN KEY (id_usuario) REFERENCES professor (id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE feedback_rubrica (
    id_feedback BIGINT UNSIGNED NOT NULL,
    criterio VARCHAR(100) NOT NULL,
    conceito VARCHAR(50) NOT NULL,
    PRIMARY KEY (id_feedback, criterio),
    CONSTRAINT fk_feedback_rubrica_feedback
        FOREIGN KEY (id_feedback) REFERENCES feedback (id_feedback)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE anexo (
    id_anexo VARCHAR(36) NOT NULL,
    id_projeto VARCHAR(36) NOT NULL,
    id_usuario VARCHAR(36) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    data_envio DATETIME NOT NULL,
    bytes VARCHAR(512) NOT NULL,
    descricao TEXT NOT NULL,
    miniatura VARCHAR(512) NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_anexo),
    KEY idx_anexo_id_projeto (id_projeto),
    KEY idx_anexo_id_usuario (id_usuario),
    CONSTRAINT fk_anexo_projeto
        FOREIGN KEY (id_projeto) REFERENCES projeto (id_projeto)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_anexo_aluno
        FOREIGN KEY (id_usuario) REFERENCES aluno (id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
