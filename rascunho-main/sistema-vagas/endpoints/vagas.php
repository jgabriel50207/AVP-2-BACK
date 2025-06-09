<?php
declare(strict_types=1);

// Headers
header("Content-Type: application/json; charset=UTF-8");


// Dependências
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Vaga.php';
require_once __DIR__ . '/../utils/validators.php';

/**
 
 * VERSÃO AJUSTADA: Agora envia apenas o código de status, sem corpo.
 */
function enviarResposta(int $statusCode): void 
{
    http_response_code($statusCode);
    // A linha do 'echo' foi removida para não enviar corpo na resposta.
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $dados = json_decode(file_get_contents("php://input"), true);

    // Validação de JSON malformado
    if (json_last_error() !== JSON_ERROR_NONE) {
        enviarResposta(400); // Retorna 400 sem corpo
    }

    // Validação de campos obrigatórios
    $requiredFields = ['id', 'empresa', 'titulo', 'localizacao', 'nivel'];
    foreach ($requiredFields as $field) {
        if (empty($dados[$field]) && (!isset($dados[$field]) || $dados[$field] !== 0)) {
            enviarResposta(422); // Retorna 422 sem corpo
        }
    }

    // Validação de formato dos dados
    if (!isValidUUID($dados['id']) || !isValidLocalizacao($dados['localizacao']) || !isValidNivel($dados['nivel'])) {
        enviarResposta(422); // Retorna 422 sem corpo
    }
    
    // Lógica Principal
    try {
        $vaga = new Vaga();
        if ($vaga->exists($dados['id'])) {
            enviarResposta(422); // Unicidade do ID é um critério, então 422
        }
        
        if ($vaga->create($dados)) {
            enviarResposta(201); // Retorna 201 sem corpo
        } else {
            // Se create() falhar sem uma exceção, é um erro inesperado.
            // A especificação não cobre erros 500, mas é bom ter.
            http_response_code(500); 
            exit();
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        // A especificação não cobre erros de banco de dados (503),
        // então aqui poderíamos retornar 422 ou simplesmente um 500.
        http_response_code(500);
        exit();
    }

} else {
    // A especificação não cobre o erro 405, mas é o correto para método inválido.
    http_response_code(405);
    exit();
}