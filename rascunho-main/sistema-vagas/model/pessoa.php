<?php
declare(strict_types=1);

// model/Pessoa.php

require_once __DIR__ . '/../config/database.php';

/**
 * Classe Pessoa
 *
 * Gerencia todas as operações de banco de dados para a tabela 'pessoas'.
 */
class Pessoa
{
    /**
     * @var PDO A instância da conexão PDO com o banco de dados.
     */
    private PDO $db;

    /**
     * Construtor da classe.
     * Obtém a conexão com o banco e ativa o modo de erro para exceções.
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Cria um novo registro de pessoa no banco de dados.
     *
     * @param array $data Dados da pessoa a serem inseridos.
     * @return bool Retorna true em caso de sucesso.
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO pessoas (id, nome, profissao, localizacao, nivel) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['id'],
            $data['nome'],
            $data['profissao'],
            strtoupper($data['localizacao']),
            $data['nivel']
        ]);
    }

    /**
     * Verifica se uma pessoa com um determinado ID existe.
     *
     * @param string $id O UUID da pessoa.
     * @return bool Retorna true se a pessoa existir, false caso contrário.
     */
    public function exists(string $id): bool
    {
        $sql = "SELECT COUNT(*) FROM pessoas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return (bool) $stmt->fetchColumn();
    }
    
    /**
     * Busca os dados de uma pessoa específica pelo ID.
     * ESTE É O MÉTODO QUE ESTAVA FALTANDO.
     *
     * @param string $id O UUID da pessoa.
     * @return array|null Retorna um array com os dados da pessoa ou null se não encontrada.
     */
    public function get(string $id): ?array
    {
        $sql = "SELECT * FROM pessoas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }
}