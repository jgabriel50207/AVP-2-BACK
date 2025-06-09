<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../model/vaga.php';
require_once __DIR__ . '/../model/candidatura.php';
require_once __DIR__ . '/../utils/validators.php';
require_once __DIR__ . '/../utils/scorecalculator.php';

function enviarResposta(int $statusCode): void 
{
    http_response_code($statusCode);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        enviarResposta(400);
    }

    $requiredFields = ['id', 'id_pessoa', 'id_vaga'];
    foreach ($requiredFields as $field) {
        if (empty($dados[$field])) {
            enviarResposta(422); 
        }
    }

    if (!isValidUUID($dados['id']) || !isValidUUID($dados['id_pessoa']) || !isValidUUID($dados['id_vaga'])) {
        enviarResposta(422);
    }

    try {
        $pessoaModel = new Pessoa();
        $vagaModel = new Vaga();
        $candidaturaModel = new Candidatura();

        $vaga = $vagaModel->get($dados['id_vaga']);
        $pessoa = $pessoaModel->get($dados['id_pessoa']);
        
        if (!$vaga || !$pessoa) {
            enviarResposta(404);
        }
        
        if ($candidaturaModel->exists($dados['id_pessoa'], $dados['id_vaga'])) {
            enviarResposta(422); 
        }

        $calculadora = new ScoreCalculator();
        $scoreFinal = $calculadora->calcularScore(
            (int)$vaga['nivel'],
            (int)$pessoa['nivel'],
            $vaga['localizacao'],
            $pessoa['localizacao']
        );

        if ($scoreFinal === null) {
            http_response_code(500);
            exit();
        }

        $dados['score'] = $scoreFinal;

        if ($candidaturaModel->create($dados)) {
            enviarResposta(201);
        } else {
            http_response_code(500);
            exit();
        }

    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(503);
        exit();
    }
} else {
    http_response_code(405);
    exit();
}