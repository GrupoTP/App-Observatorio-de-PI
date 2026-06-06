<!-- Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved. -->

# Observatório de Projetos Integradores (OPI)

Aplicação fullstack em PHP 8 + MySQL 8 + Bootstrap 5, espelhando o protótipo React em `../prototipo/`.

## Requisitos

- Docker e Docker Compose

## Subir o ambiente

```bash
cd app
docker compose up -d --build
```

Na **primeira** execução, o MySQL aplica automaticamente o schema (`sql/schema/01`–`07`) e os dados de demonstração (`sql/seeds/dev.sql`).

Aplicação: **http://localhost:8080** (porta configurável via `APP_PORT` no `.env`).

## Logins de demonstração

| E-mail | Perfil | Senha |
|--------|--------|-------|
| `aluno@aluno` | Aluno (ADS) | `senac123` |
| `aluno2@aluno` | Aluno (ADS + Game Dev) | `senac123` |
| `professor@professor` | Professor | `senac123` |
| `admin@admin` | Administrador (+ Professor) | `senac123` |

Usuários com mais de um perfil passam pela tela de seleção após o login.

## Estrutura

- `public/index.php` — front controller
- `config/routes.php` — rotas HTTP
- `src/` — controllers, repositories, services, auth
- `views/` — templates PHP (aluno, admin, auth)
- `sql/schema/` — modelo físico do MER + extensões
- `sql/seeds/dev.sql` — dados iniciais

## Recriar o banco

```bash
docker compose down -v
docker compose up -d
```

## Checklist de paridade (manual)

Compare com o protótipo em `http://localhost:5173/`:

- [ ] Layout header azul, menu volante, footer LGPD
- [ ] Cores Senac (amarelo primário com texto preto)
- [ ] Login + seleção de perfil (`admin@admin` tem Admin e Professor)
- [ ] CRUD de projetos via POST (sem Ajax)
- [ ] Área admin vs professor (`admin_only` nas rotas de projetos/cadastro)

## Paridade com o protótipo

Rotas equivalentes:

- Aluno: `/dashboard`, `/projetos`, `/submeter`, `/portfolio`, `/curriculo`, `/feedbacks`, `/prazos`, `/configuracoes`
- Admin/Professor: `/admin/dashboard`, `/admin/projetos`, `/admin/alunos`, `/admin/pi`, etc.

## Uploads

Anexos ficam em disco (`storage/attachments/`, volume Docker `attachment_data`). O banco guarda apenas os caminhos relativos em `anexo.bytes` e `anexo.miniatura`. O download passa por `/anexos/{id}/download` e `/anexos/{id}/miniatura`, com checagem de permissão antes de servir o arquivo.

Limite padrão: 1 GiB por arquivo (`UPLOAD_MAX_BYTES` no `.env`; PHP em `docker/php/uploads.ini`).
