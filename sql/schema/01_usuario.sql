-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Entidade usuário e subtipos da especialização total (MER: USUARIO / Tipo de Usuário)

CREATE TABLE usuario (
    id_usuario VARCHAR(36) NOT NULL,
    email_institucional VARCHAR(255) NOT NULL,
    nome_civil_nome VARCHAR(120) NOT NULL,
    nome_civil_sobrenome VARCHAR(120) NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    senha_salt VARCHAR(64) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    nome_social_nome VARCHAR(120) NULL,
    nome_social_sobrenome VARCHAR(120) NULL,
    email_pessoal VARCHAR(255) NOT NULL,
    email_pessoal_validado TINYINT(1) NULL,
    email_pessoal_cod_validacao VARCHAR(64) NOT NULL,
    email_pessoal_cod_exp DATETIME NOT NULL,
    json_curriculo JSON NULL,
    PRIMARY KEY (id_usuario),
    UNIQUE KEY uk_usuario_email_institucional (email_institucional)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE aluno (
    id_usuario VARCHAR(36) NOT NULL,
    portfolio_publico TINYINT(1) NULL,
    notificacoes VARCHAR(255) NULL,
    PRIMARY KEY (id_usuario),
    CONSTRAINT fk_aluno_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE professor (
    id_usuario VARCHAR(36) NOT NULL,
    PRIMARY KEY (id_usuario),
    CONSTRAINT fk_professor_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE parceiro (
    id_usuario VARCHAR(36) NOT NULL,
    empresa VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_usuario),
    CONSTRAINT fk_parceiro_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE coordenador (
    id_usuario VARCHAR(36) NOT NULL,
    PRIMARY KEY (id_usuario),
    CONSTRAINT fk_coordenador_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE aluno_selo (
    id_usuario VARCHAR(36) NOT NULL,
    selo VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_usuario, selo),
    CONSTRAINT fk_aluno_selo_aluno
        FOREIGN KEY (id_usuario) REFERENCES aluno (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
