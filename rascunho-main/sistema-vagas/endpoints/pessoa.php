<?php
// ARQUIVO: endpoints/pessoas.php

/**
 * Configuração dos Cabeçalhos (Headers) da API
 */
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/**
 * Inclusão de Dependências (Model e Config do Banco)
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Pessoa.php';

/**
 * Função Auxiliar para Enviar Respostas
 */
function enviarResposta($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

/**
 * Função para Lidar com Requisições POST (Criação)
 */
function handlePostRequest() {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        empty($dados['id']) || empty($dados['nome']) || empty($dados['profissao']) ||
        empty($dados['localizacao']) || !isset($dados['nivel'])
    ) {
        enviarResposta(422, ['mensagem' => 'Dados incompletos ou inválidos.']);
    }

    try {
        $pessoa = new Pessoa();

        if ($pessoa->exists($dados['id'])) {
            enviarResposta(409, ['mensagem' => 'Conflito. Já existe uma pessoa com este ID.']);
        }

        if ($pessoa->create($dados)) {
            enviarResposta(201, ['mensagem' => 'Pessoa criada com sucesso.']);
        } else {
            enviarResposta(500, ['mensagem' => 'Não foi possível criar a pessoa.']);
        }
    } catch (PDOException $e) {
        enviarResposta(503, ['mensagem' => 'Serviço indisponível. Erro no banco de dados.']);
    } catch (Exception $e) {
        enviarResposta(500, ['mensagem' => 'Ocorreu um erro interno no servidor.']);
    }
}

/**
 * Roteador Principal da Requisição
 */
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        handlePostRequest();
        break;
    
    default:
        enviarResposta(405, ['mensagem' => 'Método não permitido.']);
        break;
}
?>