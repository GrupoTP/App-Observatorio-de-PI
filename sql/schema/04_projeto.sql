-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Projetos e coautoria (MER: PROJETO / submete, associa, revisa, credita)

CREATE TABLE projeto (
    id_projeto VARCHAR(36) NOT NULL,
    id_usuario_submissor VARCHAR(36) NOT NULL,
    cod_turma VARCHAR(36) NOT NULL,
    id_usuario_coordenador_revisor VARCHAR(36) NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    link_repo_git VARCHAR(512) NOT NULL,
    tecnologias TEXT NOT NULL,
    publico TINYINT(1) NOT NULL DEFAULT 0,
    situacao_projeto VARCHAR(50) NOT NULL,
    prazo_especial DATETIME NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_projeto),
    KEY idx_projeto_cod_turma (cod_turma),
    KEY idx_projeto_submissor (id_usuario_submissor),
    KEY idx_projeto_coordenador_revisor (id_usuario_coordenador_revisor),
    CONSTRAINT fk_projeto_aluno_submissor
        FOREIGN KEY (id_usuario_submissor) REFERENCES aluno (id_usuario)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_projeto_turma
        FOREIGN KEY (cod_turma) REFERENCES turma (cod_turma)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_projeto_coordenador_revisor
        FOREIGN KEY (id_usuario_coordenador_revisor) REFERENCES coordenador (id_usuario)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projeto_aluno_credito (
    id_projeto VARCHAR(36) NOT NULL,
    id_usuario VARCHAR(36) NOT NULL,
    PRIMARY KEY (id_projeto, id_usuario),
    KEY idx_projeto_aluno_credito_usuario (id_usuario),
    CONSTRAINT fk_projeto_aluno_credito_projeto
        FOREIGN KEY (id_projeto) REFERENCES projeto (id_projeto)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_projeto_aluno_credito_aluno
        FOREIGN KEY (id_usuario) REFERENCES aluno (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
