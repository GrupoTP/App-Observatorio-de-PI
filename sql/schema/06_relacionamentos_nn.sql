-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Associações N:N (MER: coordena, visualiza)

CREATE TABLE coordenador_curso (
    id_usuario VARCHAR(36) NOT NULL,
    id_curso VARCHAR(36) NOT NULL,
    PRIMARY KEY (id_usuario, id_curso),
    KEY idx_coordenador_curso_id_curso (id_curso),
    CONSTRAINT fk_coordenador_curso_coordenador
        FOREIGN KEY (id_usuario) REFERENCES coordenador (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_coordenador_curso_curso
        FOREIGN KEY (id_curso) REFERENCES curso (id_curso)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE parceiro_curso (
    id_usuario VARCHAR(36) NOT NULL,
    id_curso VARCHAR(36) NOT NULL,
    PRIMARY KEY (id_usuario, id_curso),
    KEY idx_parceiro_curso_id_curso (id_curso),
    CONSTRAINT fk_parceiro_curso_parceiro
        FOREIGN KEY (id_usuario) REFERENCES parceiro (id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_parceiro_curso_curso
        FOREIGN KEY (id_curso) REFERENCES curso (id_curso)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
