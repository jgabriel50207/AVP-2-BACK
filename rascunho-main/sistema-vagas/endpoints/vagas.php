<?php
header('Content-Type: application/json');

require_once '../utils/validators.php';
require_once '../model/Vaga.php';

try {
    $vagaModel = new Vaga();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao conectar ao banco de dados']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit;
}

$requiredFields = ['id', 'empresa', 'titulo', 'localizacao', 'nivel'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(422);
        exit;
    }
}

if (
    !isValidUUID($data['id']) ||
    !isValidLocalizacao($data['localizacao']) ||
    !isValidNivel($data['nivel'])
) {
    http_response_code(422);
    exit;
}

try {
    $vagaModel->create($data);
    http_response_code(201);
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        http_response_code(422);
    } else {
        http_response_code(500);
    }
}
?>
