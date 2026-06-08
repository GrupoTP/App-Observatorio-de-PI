-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.

-- Development seed data (demo logins: aluno@aluno, aluno2@aluno, professor@professor, admin@admin, parceiro@parceiro — password: senac123)

SET NAMES utf8mb4;

SET @pwd_salt = 'dev_seed_salt_2026';
SET @pwd_hash = SHA2(CONCAT('senac123', @pwd_salt), 256);

-- Users
INSERT INTO usuario (
    id_usuario, email_institucional, nome_civil_nome, nome_civil_sobrenome,
    senha_hash, senha_salt, ativo,
    email_pessoal, email_pessoal_cod_validacao, email_pessoal_cod_exp,
    json_curriculo,
    cpf, data_nascimento, identidade_rg,
    telefone1, telefone1_whatsapp, telefone2, telefone2_whatsapp,
    cep, endereco, bairro, cidade, estado, pais,
    data_criacao
) VALUES
(
    'u-aluno-001', 'aluno@aluno', 'João', 'Silva',
    @pwd_hash, @pwd_salt, 1,
    'joao.pessoal@email.com', '', '2099-12-31 23:59:59',
    NULL,
    '123.456.789-00', '2000-05-15', '12.345.678-9',
    '(81) 99999-0001', 1, '(81) 99999-0002', 0,
    '50000-000', 'Av. Boa Viagem, 1234, Apto 101', 'Boa Viagem', 'Recife', 'PE', 'Brasil',
    '2025-03-01 08:00:00'
),
(
    'u-prof-001', 'professor@professor', 'Maria', 'Santos',
    @pwd_hash, @pwd_salt, 1,
    'maria.pessoal@email.com', '', '2099-12-31 23:59:59',
    NULL,
    '987.654.321-00', '1985-08-20', NULL,
    '(81) 98888-1001', 1, NULL, 0,
    NULL, NULL, NULL, 'Recife', 'PE', 'Brasil',
    '2025-03-01 08:00:00'
),
(
    'u-admin-001', 'admin@admin', 'Carlos', 'Roberto',
    @pwd_hash, @pwd_salt, 1,
    'carlos.pessoal@email.com', '', '2099-12-31 23:59:59',
    NULL,
    '111.222.333-44', '1978-12-01', NULL,
    '(81) 97777-2001', 0, NULL, 0,
    NULL, NULL, NULL, 'Recife', 'PE', 'Brasil',
    '2025-03-01 08:00:00'
),
(
    'u-aluno-002', 'aluno2@aluno', 'Lucas', 'Ferreira',
    @pwd_hash, @pwd_salt, 1,
    'lucas@email.com', '', '2099-12-31 23:59:59',
    NULL,
    '222.333.444-55', '2001-11-30', NULL,
    '(81) 96666-3001', 0, NULL, 0,
    NULL, NULL, NULL, 'Recife', 'PE', 'Brasil',
    '2025-03-01 08:00:00'
);

-- Parceiro demo user
INSERT INTO usuario (
    id_usuario, email_institucional, nome_civil_nome, nome_civil_sobrenome,
    senha_hash, senha_salt, ativo,
    email_pessoal, email_pessoal_cod_validacao, email_pessoal_cod_exp,
    json_curriculo,
    cpf, data_nascimento, identidade_rg,
    telefone1, telefone1_whatsapp, telefone2, telefone2_whatsapp,
    cep, endereco, bairro, cidade, estado, pais,
    data_criacao
) VALUES (
    'u-parceiro-001', 'parceiro@parceiro', 'Empresa', 'Parceira',
    @pwd_hash, @pwd_salt, 1,
    'parceiro@empresa.com', '', '2099-12-31 23:59:59',
    NULL,
    NULL, NULL, NULL,
    NULL, 0, NULL, 0,
    NULL, NULL, NULL, 'Recife', 'PE', 'Brasil',
    '2025-03-01 08:00:00'
);

INSERT INTO parceiro (id_usuario, empresa) VALUES ('u-parceiro-001', 'Empresa Parceira Demonstração');

INSERT INTO aluno (id_usuario, portfolio_publico, notificacoes) VALUES
('u-aluno-001', 1, 'email'),
('u-aluno-002', 0, NULL);

INSERT INTO professor (id_usuario) VALUES ('u-prof-001'), ('u-admin-001');
INSERT INTO coordenador (id_usuario) VALUES ('u-admin-001');

-- Academic structure
INSERT INTO curso (id_curso, nome_curso, ativo) VALUES
('curso-ads-001', 'Análise e Desenvolvimento de Sistemas', 1),
('curso-gamedev-001', 'Desenvolvimento de Jogos Digitais', 1);

INSERT INTO turma (cod_turma, id_curso, nome_turma, turno, modulo, prazo_projetos, ativo) VALUES
('turma-ads-m2', 'curso-ads-001', 'ADS 2026.1 — Turma A', 'Noturno', '2º Módulo', '2026-06-30 23:59:59', 1),
('turma-gamedev-m2', 'curso-gamedev-001', 'Game Dev 2026.1 — Turma A', 'Noturno', '2º Módulo', '2026-07-15 23:59:59', 1);

INSERT INTO matricula (cod_matricula, id_usuario, cod_turma, ativo) VALUES
('mat-001', 'u-aluno-001', 'turma-ads-m2', 1),
('mat-002', 'u-aluno-002', 'turma-ads-m2', 1),
('mat-003', 'u-aluno-002', 'turma-gamedev-m2', 1);

INSERT INTO alocacao (id_alocacao, id_usuario, cod_turma, ativo) VALUES
('aloc-001', 'u-prof-001', 'turma-ads-m2', 1),
('aloc-002', 'u-admin-001', 'turma-ads-m2', 1);

INSERT INTO coordenador_curso (id_usuario, id_curso) VALUES
('u-admin-001', 'curso-ads-001');

-- Rubric criteria (global for course)
INSERT INTO rubrica_criterio (id_criterio, cod_turma, nome, peso, ordem, ativo) VALUES
('rub-func', 'turma-ads-m2', 'Funcionalidade', 1.00, 1, 1),
('rub-doc', 'turma-ads-m2', 'Documentação', 1.00, 2, 1),
('rub-cria', 'turma-ads-m2', 'Criatividade', 1.00, 3, 1),
('rub-gd-func', 'turma-gamedev-m2', 'Funcionalidade', 1.00, 1, 1),
('rub-gd-doc', 'turma-gamedev-m2', 'Documentação', 1.00, 2, 1),
('rub-gd-cria', 'turma-gamedev-m2', 'Criatividade', 1.00, 3, 1);

-- Projects
INSERT INTO projeto (id_projeto, id_usuario_submissor, cod_turma, id_usuario_coordenador_revisor, titulo, nome_grupo, descricao, link_repo_git, tecnologias, publico, situacao_projeto, prazo_especial, ativo) VALUES
('proj-001', 'u-aluno-001', 'turma-ads-m2', NULL, 'Sistema de Gestão Escolar', 'Grupo Alpha', 'Plataforma web para gestão acadêmica com módulos de matrícula e notas.', 'https://github.com/example/sge', 'PHP, MySQL, Bootstrap', 1, 'enviado', '2026-06-30 23:59:59', 1),
('proj-002', 'u-aluno-001', 'turma-ads-m2', NULL, 'App de Delivery Sustentável', NULL, 'Aplicativo de entregas com foco em embalagens recicláveis.', 'https://github.com/example/delivery', 'React Native, Node.js', 1, 'avaliado', '2026-06-30 23:59:59', 1),
('proj-003', 'u-aluno-002', 'turma-ads-m2', NULL, 'E-commerce Artesanal', 'Grupo Beta', 'Loja virtual para artesãos locais.', 'https://github.com/example/artesanal', 'Vue.js, Laravel', 1, 'em-correcao', '2026-06-30 23:59:59', 1);

INSERT INTO projeto_aluno_credito (id_projeto, id_usuario) VALUES
('proj-001', 'u-aluno-002');

-- Feedback for evaluated project
INSERT INTO feedback (id_feedback, id_projeto, id_usuario, descricao, data, ativo) VALUES
(1, 'proj-002', 'u-prof-001', 'Excelente trabalho! O projeto demonstra boa organização e documentação completa.', '2026-04-10 14:30:00', 1);

INSERT INTO feedback_rubrica (id_feedback, criterio, conceito) VALUES
(1, 'Funcionalidade', '9'),
(1, 'Documentação', '8'),
(1, 'Criatividade', '9');
