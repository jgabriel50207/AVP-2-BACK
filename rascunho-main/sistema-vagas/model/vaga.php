<?php
declare(strict_types=1);

class Vaga {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data): bool {
        $sql = "INSERT INTO vagas (id, empresa, titulo, descricao, localizacao, nivel) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id'],
            $data['empresa'],
            $data['titulo'],
            $data['descricao'] ?? null,
            strtoupper($data['localizacao']),
            $data['nivel']
        ]);
    }

    public function exists(string $id): bool {
        $sql = "SELECT COUNT(*) FROM vagas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }
    
    public function get(string $id): ?array {
        $sql = "SELECT * FROM vagas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(); // PDO::FETCH_ASSOC é o padrão agora
        return $result === false ? null : $result;
    }
}