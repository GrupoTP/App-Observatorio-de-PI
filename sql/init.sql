-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Observatório de Projetos Integradores — full schema bootstrap
-- Executed on first MySQL container startup (docker-entrypoint-initdb.d)

SOURCE /docker-entrypoint-initdb.d/schema/01_usuario.sql;
SOURCE /docker-entrypoint-initdb.d/schema/02_curso_turma.sql;
SOURCE /docker-entrypoint-initdb.d/schema/03_matricula_alocacao.sql;
SOURCE /docker-entrypoint-initdb.d/schema/04_projeto.sql;
SOURCE /docker-entrypoint-initdb.d/schema/05_feedback_anexo.sql;
SOURCE /docker-entrypoint-initdb.d/schema/06_relacionamentos_nn.sql;
SOURCE /docker-entrypoint-initdb.d/schema/07_extensions.sql;
SOURCE /docker-entrypoint-initdb.d/seeds/dev.sql;
