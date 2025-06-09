<?php
declare(strict_types=1);

// Headers
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");


require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Candidatura.php';
require_once __DIR__ . '/../model/Vaga.php'; // Adicionado
require_once __DIR__ . '/../utils/validators.php';

function enviarRespostaComDados(int $statusCode, array $data): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Verifica se o método da requisição é GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Método não permitido
    exit();
}

// 1. Validação da Entrada
$id_vaga = $_GET['id_vaga'] ?? null;

if ($id_vaga === null) {
    http_response_code(400); // Bad Request (faltou o parâmetro)
    exit();
}

if (!isValidUUID($id_vaga)) {
    http_response_code(422); // Unprocessable Entity (formato inválido)
    exit();
}

// 2. Lógica Principal
try {
    // --- NOVA VALIDAÇÃO: VERIFICA SE A VAGA EXISTE ---
    $vagaModel = new Vaga();
    if (!$vagaModel->exists($id_vaga)) {
        // Se a vaga não for encontrada, retorna 404 sem corpo, conforme a regra.
        http_response_code(404);
        exit();
    }
    // ---------------------------------------------------

    // Se a vaga existe, busca o ranking de candidatos.
    $candidaturaModel = new Candidatura();
    $ranking = $candidaturaModel->getRankingByVaga($id_vaga);

    // 3. Envio da Resposta de Sucesso
    // Retorna 200 OK com a lista de candidatos (pode ser uma lista vazia se não houver candidatos)
    enviarRespostaComDados(200, $ranking);

} catch (PDOException $e) {
    // Em caso de erro de banco de dados
    error_log("Erro de BD no ranking: " . $e->getMessage());
    http_response_code(503); // Service Unavailable
    exit();
}