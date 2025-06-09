<?php
declare(strict_types=1);

// Headers para permitir o acesso e definir o tipo de conteúdo como JSON
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

// Dependências
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Candidatura.php';
require_once __DIR__ . '/../utils/validators.php';

function enviarResposta(int $statusCode, array $data): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Verifica se o método da requisição é GET
$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'GET') {
    enviarResposta(405, ['mensagem' => 'Método não permitido. Utilize GET.']);
}

// 1. Validação da Entrada
// Pega o id_vaga da URL (ex: ?id_vaga=...)
$id_vaga = $_GET['id_vaga'] ?? null;

if ($id_vaga === null) {
    enviarResposta(400, ['mensagem' => 'O parâmetro id_vaga é obrigatório.']);
}

if (!isValidUUID($id_vaga)) {
    enviarResposta(422, ['mensagem' => 'O id_vaga fornecido não é um UUID válido.']);
}

// 2. Lógica Principal
try {
    $candidaturaModel = new Candidatura();
    
    // Chama o novo método do model para buscar e ordenar os candidatos
    $ranking = $candidaturaModel->getRankingByVaga($id_vaga);

    // 3. Envio da Resposta
    // Retorna 200 OK com a lista de candidatos (pode ser uma lista vazia)
    enviarResposta(200, $ranking);

} catch (PDOException $e) {
    // Em caso de erro de banco de dados
    error_log("Erro de BD no ranking: " . $e->getMessage());
    enviarResposta(503, ['mensagem' => 'Serviço indisponível (erro no banco de dados).']);
}