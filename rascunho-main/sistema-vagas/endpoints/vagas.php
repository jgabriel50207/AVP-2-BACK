<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");
// Outros headers...

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Vaga.php';
require_once __DIR__ . '/../utils/validators.php';

function enviarResposta($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    $requiredFields = ['id', 'empresa', 'titulo', 'localizacao', 'nivel'];
    foreach ($requiredFields as $field) {
        if (empty($dados[$field])) {
            enviarResposta(422, ['mensagem' => "O campo '{$field}' é obrigatório."]);
        }
    }

    if (!isValidUUID($dados['id']) || !isValidLocalizacao($dados['localizacao']) || !isValidNivel($dados['nivel'])) {
        enviarResposta(422, ['mensagem' => 'Dados de validação inválidos (UUID, Localização ou Nível).']);
    }
    
    try {
        $vaga = new Vaga();
        if ($vaga->exists($dados['id'])) {
            enviarResposta(409, ['mensagem' => 'Já existe uma vaga com este ID.']);
        }
        
        if ($vaga->create($dados)) {
            enviarResposta(201, ['mensagem' => 'Vaga criada com sucesso.']);
        } else {
            enviarResposta(500, ['mensagem' => 'Não foi possível criar a vaga.']);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        enviarResposta(503, ['mensagem' => 'Serviço indisponível.']);
    }

} else {
    enviarResposta(405, ['mensagem' => 'Método não permitido.']);
}