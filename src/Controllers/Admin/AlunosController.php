<?php


/*
 * Copyright © 2026, Polyana Fontes; Thayná Batista da Silva — Integrative Projects Observatory All rights reserved.
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Database;
use App\Http\Request;
use App\Repositories\ProjetoRepository;
use App\Repositories\RubricaRepository;
use App\Repositories\TurmaRepository;
use App\Repositories\UsuarioRepository;
use App\Support\Flash;
use App\Support\Password;
use App\Support\Uuid;

final class AlunosController extends Controller
{
    public function index(Request $request, array $params = []): void
    {
        $search  = $request->query('q');
        $cursoId = $request->query('curso');
        $repo    = new UsuarioRepository();
        $alunos  = $repo->listAlunos($search, $cursoId);

        $projetoRepo = new ProjetoRepository();
        $criteria    = (new RubricaRepository())->allActive();

        $projectsByAluno = [];
        foreach ($alunos as $a) {
            $projectsByAluno[$a['id_usuario']] = $projetoRepo->forAluno($a['id_usuario']);
        }

        $this->render('admin/alunos', [
            'headerTitle'     => 'Gerenciar Alunos',
            'pageTitle'       => 'Gerenciar Alunos',
            'alunos'          => $alunos,
            'cursos'          => $repo->listCursos(),
            'search'          => $search ?? '',
            'cursoId'         => $cursoId ?? '',
            'projectsByAluno' => $projectsByAluno,
            'criteria'        => $criteria,
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $id = $params['id'] ?? '';
        $repo = new UsuarioRepository();
        $user = $repo->findById($id);
        if ($user === null) {
            Flash::error('Aluno não encontrado.');
            redirect('/admin/alunos');
        }

        $projects = (new ProjetoRepository())->forAluno($id);
        $turma = (new TurmaRepository())->activeTurmaForAluno($id);

        $this->render('admin/detalhes-aluno', [
            'headerTitle' => 'Detalhes do Aluno',
            'pageTitle' => user_display_name($user),
            'user' => $user,
            'aluno' => $repo->getAlunoRow($id),
            'projects' => $projects,
            'turma' => $turma,
        ]);
    }

    public function create(Request $request, array $params = []): void
    {
        $this->render('admin/cadastrar-usuario', [
            'headerTitle' => 'Cadastrar Usuário',
            'pageTitle' => 'Cadastrar Usuário',
            'turmas' => $this->listTurmas(),
        ]);
    }

    public function store(Request $request, array $params = []): void
    {
        $this->requireCsrf($request);
        $role = $request->input('role', 'aluno') ?? 'aluno';
        $id = Uuid::v4();
        $hashed = Password::hash($request->input('senha', 'senac123') ?? 'senac123');

        try {
            (new UsuarioRepository())->createUsuario([
                'id_usuario' => $id,
                'email_institucional' => $request->input('email', '') ?? '',
                'nome_civil_nome' => $request->input('nome', '') ?? '',
                'nome_civil_sobrenome' => $request->input('sobrenome', '') ?? '',
                'senha_hash' => $hashed['hash'],
                'senha_salt' => $hashed['salt'],
                'email_pessoal' => $request->input('email_pessoal', '') ?? $request->input('email', ''),
            ], $role);

            $codTurma = $request->input('cod_turma');
            if ($role === 'aluno' && $codTurma) {
                Database::connection()->prepare(
                    'INSERT INTO matricula (cod_matricula, id_usuario, cod_turma, ativo) VALUES (:m, :u, :t, 1)'
                )->execute(['m' => Uuid::v4(), 'u' => $id, 't' => $codTurma]);
            }
            if ($role === 'professor' && $codTurma) {
                Database::connection()->prepare(
                    'INSERT INTO alocacao (id_alocacao, id_usuario, cod_turma, ativo) VALUES (:a, :u, :t, 1)'
                )->execute(['a' => Uuid::v4(), 'u' => $id, 't' => $codTurma]);
            }

            Flash::success('Usuário cadastrado com sucesso.');
            redirect('/admin/alunos');
        } catch (\Throwable $e) {
            Flash::error($e->getMessage());
            redirect('/admin/alunos/novo');
        }
    }

    /** @return list<array<string, mixed>> */
    private function listTurmas(): array
    {
        $stmt = Database::connection()->query(
            'SELECT t.cod_turma, t.nome_turma, c.nome_curso FROM turma t INNER JOIN curso c ON c.id_curso = t.id_curso WHERE t.ativo = 1'
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
