<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth\SessionAuth;
use App\Controllers\Controller;
use App\Database;
use App\Http\Request;
use App\Repositories\FeedbackRepository;
use App\Repositories\ProjetoRepository;
use App\Repositories\RubricaRepository;
use App\Repositories\TurmaRepository;
use App\Repositories\UsuarioRepository;
use App\Services\ProjetoService;
use App\Support\Flash;
use App\Support\Uuid;
use PDO;

final class PiController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $search = $request->query('q');
        $status = $request->query('status', 'todos');
        $course = $request->query('course', 'todos');

        $repo = new ProjetoRepository();
        $this->render('admin/pi', [
            'headerTitle' => 'Gerenciar PI',
            'pageTitle' => 'Gerenciar PI',
            'groups' => $repo->listPiGroups(
                $status === 'todos' ? null : $status,
                $search,
                $course === 'todos' ? null : $course
            ),
            'courses' => array_merge(['todos'], (new TurmaRepository())->distinctCourses()),
            'search' => $search ?? '',
            'status' => $status,
            'course' => $course,
        ]);
    }

    public function create(Request $request, array $params = []): void
    {
        $this->render('admin/pi-form', [
            'headerTitle' => 'Criar Grupo PI',
            'pageTitle' => 'Criar Grupo PI',
            'project' => null,
            'alunos' => (new UsuarioRepository())->listAlunos(),
            'turmas' => $this->turmas(),
            'action' => '/admin/pi/novo',
        ]);
    }

    public function store(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);
        $this->saveGroup($request, null);
        redirect('/admin/pi');
    }

    public function show(Request $request, array $params): void
    {
        $id = $params['id'] ?? '';
        $project = (new ProjetoRepository())->findById($id);
        if ($project === null) {
            Flash::error('Grupo PI não encontrado.');
            redirect('/admin/pi');
        }

        $members = (new ProjetoRepository())->coauthors($id);
        $submitter = (new UsuarioRepository())->findById($project['id_usuario_submissor']);
        $feedbackRepo = new FeedbackRepository();
        $feedback = $feedbackRepo->findByProject($id);

        $rubricaScores = [];
        $rubricaCriteria = [];
        if ($feedback !== null) {
            $rubricaScores = $feedbackRepo->rubricaForFeedback((int) $feedback['id_feedback']);
            $rawCriteria = (new RubricaRepository())->allActive($project['cod_turma']);
            foreach ($rawCriteria as $c) {
                $rubricaCriteria[$c['id_criterio']] = $c['nome'];
            }
        }

        $this->render('admin/pi-detalhes', [
            'headerTitle' => 'Detalhes PI',
            'pageTitle' => $project['nome_grupo'] ?? $project['titulo'],
            'project' => $project,
            'submitter' => $submitter,
            'members' => $members,
            'feedback' => $feedback,
            'rubricaScores' => $rubricaScores,
            'rubricaCriteria' => $rubricaCriteria,
        ]);
    }

    public function edit(Request $request, array $params): void
    {
        $id = $params['id'] ?? '';
        $project = (new ProjetoRepository())->findById($id);
        if ($project === null) {
            redirect('/admin/pi');
        }

        $coauthorIds = array_column((new ProjetoRepository())->coauthors($id), 'id_usuario');

        $this->render('admin/pi-form', [
            'headerTitle' => 'Editar Grupo PI',
            'pageTitle' => 'Editar Grupo PI',
            'project' => $project,
            'coauthorIds' => $coauthorIds,
            'alunos' => (new UsuarioRepository())->listAlunos(),
            'turmas' => $this->turmas(),
            'action' => '/admin/pi/' . $id . '/editar',
        ]);
    }

    public function update(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $this->saveGroup($request, $params['id'] ?? null);
        redirect('/admin/pi');
    }

    public function evaluate(Request $request, array $params): void
    {
        $id = $params['id'] ?? '';
        $project = (new ProjetoRepository())->findById($id);
        if ($project === null) {
            redirect('/admin/pi');
        }

        $criteria = (new RubricaRepository())->allActive($project['cod_turma']);
        $alunos = array_values(array_filter(array_merge(
            [(new UsuarioRepository())->findById($project['id_usuario_submissor'])],
            (new ProjetoRepository())->coauthors($id)
        )));

        $feedbackRepo = new FeedbackRepository();
        $existingFeedback = $feedbackRepo->findByProject($id);
        $existingConceito = null;
        if ($existingFeedback !== null) {
            $scores = array_map(
                fn($r) => (float) $r['conceito'],
                $feedbackRepo->rubricaForFeedback((int) $existingFeedback['id_feedback'])
            );
            if ($scores !== []) {
                $avg = array_sum($scores) / count($scores);
                $existingConceito = nota_para_conceito($avg);
            }
        }

        $this->render('admin/avaliar-pi', [
            'headerTitle' => 'Avaliar PI',
            'pageTitle' => 'Avaliar PI',
            'project' => $project,
            'criteria' => $criteria,
            'alunos' => $alunos,
            'existingFeedback' => $existingFeedback,
            'existingConceito' => $existingConceito,
            'tipoInicial' => $request->query('type', 'grupo'),
        ]);
    }

    public function evaluateStore(Request $request, array $params): void
    {
        $this->requireCsrf($request);
        $id = $params['id'] ?? '';
        $professorId = SessionAuth::userId() ?? '';

        $scores = [];
        foreach ($_POST as $key => $value) {
            if (!str_starts_with($key, 'criterio_')) {
                continue;
            }

            $criterio = substr($key, 9);
            $scores[$criterio] = (string) conceito_codigo_para_nota_interna((string) $value);
        }

        try {
            (new FeedbackRepository())->create(
                $id,
                $professorId,
                $request->input('descricao', '') ?? '',
                $scores
            );
            Flash::success('Avaliação de PI registrada.');
        } catch (\Throwable $e) {
            Flash::error($e->getMessage());
        }

        redirect('/admin/pi');
    }

    public function rubrica(Request $request, array $params = []): void
    {
        $turma = (new TurmaRepository())->activeTurmaForAluno(SessionAuth::userId() ?? '')
            ?? $this->turmas()[0] ?? null;
        $cod = $request->query('turma') ?? ($turma['cod_turma'] ?? 'turma-ads-m2');
        $criteria = (new RubricaRepository())->allActive($cod);

        $this->render('admin/rubrica', [
            'headerTitle' => 'Configurar Rubrica',
            'pageTitle' => 'Configurar Rubrica',
            'criteria' => $criteria,
            'turmas' => $this->turmas(),
            'codTurma' => $cod,
        ]);
    }

    public function rubricaStore(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);
        $cod = $request->input('cod_turma', 'turma-ads-m2') ?? 'turma-ads-m2';
        $names = $_POST['nome'] ?? [];
        $pesos = $_POST['peso'] ?? [];

        $criteria = [];
        if (is_array($names)) {
            foreach ($names as $i => $nome) {
                if (trim((string) $nome) === '') {
                    continue;
                }
                $criteria[] = [
                    'nome' => trim((string) $nome),
                    'peso' => (float) ($pesos[$i] ?? 1),
                    'ordem' => $i + 1,
                ];
            }
        }

        (new RubricaRepository())->replaceForTurma($cod, $criteria);
        Flash::success('Rubrica atualizada.');
        redirect('/admin/pi/rubrica?turma=' . urlencode($cod));
    }

    private function saveGroup(Request $request, ?string $projectId): void
    {
        $submitter = $request->input('id_submissor', '') ?? '';
        $members = $_POST['membros'] ?? [];
        if (!is_array($members)) {
            $members = [];
        }
        $members[] = $submitter;

        try {
            (new ProjetoService())->savePiGroup([
                'cod_turma' => $request->input('cod_turma', '') ?? '',
                'titulo' => $request->input('titulo', '') ?? '',
                'nome_grupo' => $request->input('nome_grupo', '') ?? '',
                'descricao' => $request->input('descricao', '') ?? '',
                'link_repo_git' => $request->input('link_repo_git', '') ?? '',
                'tecnologias' => $request->input('tecnologias', '') ?? '',
                'situacao_projeto' => $request->input('situacao_projeto', 'em-andamento') ?? 'em-andamento',
                'prazo_especial' => $request->input('prazo_especial'),
            ], $projectId, $submitter, array_unique($members));
            Flash::success('Grupo PI salvo.');
        } catch (\Throwable $e) {
            Flash::error($e->getMessage());
        }
    }

    /** @return list<array<string, mixed>> */
    private function turmas(): array
    {
        $stmt = Database::connection()->query(
            'SELECT t.*, c.nome_curso FROM turma t INNER JOIN curso c ON c.id_curso = t.id_curso WHERE t.ativo = 1'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
