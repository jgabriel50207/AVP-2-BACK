<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");
// Outros headers, como Access-Control-Allow-Origin, podem ser adicionados aqui se necessário.

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Pessoa.php';
require_once __DIR__ . '/../utils/validators.php'; // Adicionamos para validações futuras

/**
 * Função auxiliar para padronizar o envio de respostas JSON.
 */
function enviarResposta(int $statusCode, array $data): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    // Validação de JSON malformado
    if (json_last_error() !== JSON_ERROR_NONE) {
        enviarResposta(400, ['mensagem' => 'Corpo da requisição contém JSON inválido.']);
    }

    // --- VALIDAÇÃO PADRONIZADA ---
    $requiredFields = ['id', 'nome', 'profissao', 'localizacao', 'nivel'];
    foreach ($requiredFields as $field) {
        // Usamos !isset para pegar campos nulos e empty para campos vazios.
        // O check de !isset é importante para o campo 'nivel' que pode ser 0 em outros cenários.
        if (!isset($dados[$field]) || $dados[$field] === '') {
            enviarResposta(422, ['mensagem' => "O campo obrigatório '{$field}' está faltando ou vazio."]);
        }
    }
    
    // Validação de formato (opcional, mas recomendado)
    if (!isValidUUID($dados['id'])) {
         enviarResposta(422, ['mensagem' => 'O formato do campo ID é um UUID inválido.']);
    }

    // Lógica principal
    try {
        $pessoa = new Pessoa();
        if ($pessoa->exists($dados['id'])) {
            enviarResposta(409, ['mensagem' => 'Já existe uma pessoa com este ID.']);
        }

        if ($pessoa->create($dados)) {
            enviarResposta(201, ['mensagem' => 'Pessoa criada com sucesso.']);
        } else {
            enviarResposta(500, ['mensagem' => 'Não foi possível criar a pessoa.']);
        }
    } catch (PDOException $e) {
        error_log("Erro no BD ao criar pessoa: " . $e->getMessage());
        enviarResposta(503, ['mensagem' => 'Serviço indisponível (erro no banco de dados).']);
    }
} else {
    enviarResposta(405, ['mensagem' => 'Método não permitido.']);
}