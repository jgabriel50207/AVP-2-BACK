<?php
declare(strict_types=1);

class Pessoa {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data): bool {
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

    public function exists(string $id): bool {
        $sql = "SELECT COUNT(*) FROM pessoas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }
}