<?php
declare(strict_types=1);

// Headers
header("Content-Type: application/json; charset=UTF-8");

// Dependências
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/pessoa.php';
require_once __DIR__ . '/../utils/validators.php';

/**
 * Função auxiliar para enviar respostas HTTP apenas com o código de status.
 */
function enviarResposta(int $statusCode): void 
{
    http_response_code($statusCode);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    // Validação de JSON malformado
    if (json_last_error() !== JSON_ERROR_NONE) {
        enviarResposta(400);
    }

    // Validação de campos obrigatórios
    $requiredFields = ['id', 'nome', 'profissao', 'localizacao', 'nivel'];
    foreach ($requiredFields as $field) {
        if (!isset($dados[$field]) || $dados[$field] === '') {
            enviarResposta(422);
        }
    }
    
    
    // Validação de formato dos dados, incluindo nível e localização
    if (
        !isValidUUID($dados['id']) || 
        !isValidLocalizacao($dados['localizacao']) || 
        !isValidNivel($dados['nivel'])
    ) {
         enviarResposta(422);
    }

    // Lógica Principal
    try {
        $pessoa = new Pessoa();

        if ($pessoa->exists($dados['id'])) {
            enviarResposta(422); // Conforme a regra, duplicidade é 422
        }

        if ($pessoa->create($dados)) {
            enviarResposta(201); // Sucesso
        } else {
            http_response_code(500); 
            exit();
        }
    } catch (PDOException $e) {
        error_log("Erro no BD ao criar pessoa: " . $e->getMessage());
        http_response_code(500);
        exit();
    }
} else {
    http_response_code(405);
    exit();
}