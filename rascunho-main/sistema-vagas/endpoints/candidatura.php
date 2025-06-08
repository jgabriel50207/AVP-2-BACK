<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");
// Outros headers...

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Pessoa.php';
require_once __DIR__ . '/../model/Vaga.php';
require_once __DIR__ . '/../model/Candidatura.php';
require_once __DIR__ . '/../utils/validators.php';

function enviarResposta($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    $requiredFields = ['id', 'id_pessoa', 'id_vaga'];
    foreach ($requiredFields as $field) {
        if (empty($dados[$field])) {
            enviarResposta(422, ['mensagem' => "O campo '{$field}' é obrigatório."]);
        }
    }

    if (!isValidUUID($dados['id']) || !isValidUUID($dados['id_pessoa']) || !isValidUUID($dados['id_vaga'])) {
        enviarResposta(422, ['mensagem' => 'Um ou mais IDs fornecidos não são UUIDs válidos.']);
    }

    try {
        $pessoaModel = new Pessoa();
        $vagaModel = new Vaga();
        $candidaturaModel = new Candidatura();

        if (!$pessoaModel->exists($dados['id_pessoa']) || !$vagaModel->exists($dados['id_vaga'])) {
            enviarResposta(404, ['mensagem' => 'Pessoa ou Vaga informada não existe.']);
        }

        if ($candidaturaModel->exists($dados['id_pessoa'], $dados['id_vaga'])) {
            enviarResposta(409, ['mensagem' => 'Esta candidatura já foi registrada.']);
        }

        if ($candidaturaModel->create($dados)) {
            enviarResposta(201, ['mensagem' => 'Candidatura registrada com sucesso.']);
        } else {
            enviarResposta(500, ['mensagem' => 'Não foi possível registrar a candidatura.']);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        enviarResposta(503, ['mensagem' => 'Serviço indisponível.']);
    }
} else {
    enviarResposta(405, ['mensagem' => 'Método não permitido.']);
}