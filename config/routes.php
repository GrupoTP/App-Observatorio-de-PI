<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

use App\Controllers\AnexoController;
use App\Controllers\Admin\AlunosController;
use App\Controllers\Admin\DashboardAdminController;
use App\Controllers\Admin\PiController;
use App\Controllers\Admin\ProjetosAdminController;
use App\Controllers\ConfiguracoesController;
use App\Controllers\CurriculoController;
use App\Controllers\DashboardController;
use App\Controllers\FeedbacksController;
use App\Controllers\LoginController;
use App\Controllers\ParceiroController;
use App\Controllers\PortfolioController;
use App\Controllers\PrazosController;
use App\Controllers\ProjetosController;
use App\Controllers\SubmeterController;
use App\Router;

/** @var Router $router */

$router->get('/', static fn () => redirect('/login'));

$login = new LoginController();
$router->get('/login', [$login, 'show'], ['guest']);
$router->post('/login', [$login, 'login'], ['guest']);
$router->post('/logout', [$login, 'logout'], ['auth']);

$dash = new DashboardController();
$router->get('/dashboard', [$dash, 'index'], ['auth', 'aluno']);

$projetos = new ProjetosController();
$router->get('/projetos', [$projetos, 'index'], ['auth', 'aluno']);
$router->get('/projetos/{id}', [$projetos, 'show'], ['auth', 'aluno']);
$router->get('/projetos/{id}/editar', [$projetos, 'edit'], ['auth', 'aluno']);
$router->post('/projetos/{id}/editar', [$projetos, 'update'], ['auth', 'aluno']);
$router->post('/projetos/{id}/excluir', [$projetos, 'destroy'], ['auth', 'aluno']);

$submeter = new SubmeterController();
$router->get('/submeter', [$submeter, 'create'], ['auth', 'aluno']);
$router->post('/submeter', [$submeter, 'store'], ['auth', 'aluno']);

$anexo = new AnexoController();
$router->get('/anexos/{id}/download', [$anexo, 'download'], ['auth']);
$router->get('/anexos/{id}/miniatura', [$anexo, 'thumbnail'], ['auth']);

$router->get('/portfolio', [new PortfolioController(), 'index'], ['auth', 'aluno']);
$router->get('/feedbacks', [new FeedbacksController(), 'index'], ['auth', 'aluno']);
$router->get('/prazos', [new PrazosController(), 'index'], ['auth', 'aluno']);

$curriculo = new CurriculoController();
$router->get('/curriculo', [$curriculo, 'index'], ['auth']);
$router->post('/curriculo', [$curriculo, 'store'], ['auth']);
$router->get('/portfolio/curriculo', [$curriculo, 'index'], ['auth']);

$configController = new ConfiguracoesController();
$router->get('/configuracoes', [$configController, 'index'], ['auth']);
$router->post('/configuracoes', [$configController, 'update'], ['auth']);

$parceiro = new ParceiroController();
$router->get('/parceiro', [$parceiro, 'index'], ['auth', 'parceiro']);
$router->get('/parceiro/projetos', [$parceiro, 'index'], ['auth', 'parceiro']);
$router->get('/parceiro/projetos/{id}', [$parceiro, 'show'], ['auth', 'parceiro']);

$adminDash = new DashboardAdminController();
$router->get('/admin/dashboard', [$adminDash, 'index'], ['auth', 'staff']);

$adminProj = new ProjetosAdminController();
$router->get('/admin/projetos', [$adminProj, 'index'], ['auth', 'admin_only']);
$router->get('/admin/projetos/{id}/avaliar', [$adminProj, 'evaluate'], ['auth', 'admin_only']);
$router->post('/admin/projetos/{id}/avaliar', [$adminProj, 'evaluateStore'], ['auth', 'admin_only']);

$alunos = new AlunosController();
$router->get('/admin/alunos', [$alunos, 'index'], ['auth', 'staff']);
$router->get('/admin/alunos/novo', [$alunos, 'create'], ['auth', 'admin_only']);
$router->post('/admin/alunos/novo', [$alunos, 'store'], ['auth', 'admin_only']);
$router->get('/admin/alunos/{id}', [$alunos, 'show'], ['auth', 'admin_only']);

$pi = new PiController();
$router->get('/admin/pi', [$pi, 'index'], ['auth', 'staff']);
$router->get('/admin/pi/novo', [$pi, 'create'], ['auth', 'staff']);
$router->post('/admin/pi/novo', [$pi, 'store'], ['auth', 'staff']);
$router->get('/admin/pi/rubrica', [$pi, 'rubrica'], ['auth', 'staff']);
$router->post('/admin/pi/rubrica', [$pi, 'rubricaStore'], ['auth', 'staff']);
$router->get('/admin/pi/{id}', [$pi, 'show'], ['auth', 'staff']);
$router->get('/admin/pi/{id}/editar', [$pi, 'edit'], ['auth', 'staff']);
$router->post('/admin/pi/{id}/editar', [$pi, 'update'], ['auth', 'staff']);
$router->get('/admin/pi/{id}/avaliar', [$pi, 'evaluate'], ['auth', 'staff']);
$router->post('/admin/pi/{id}/avaliar', [$pi, 'evaluateStore'], ['auth', 'staff']);
