<?php
declare(strict_types=1);

class Candidatura {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data): bool {
        $sql = "INSERT INTO candidaturas (id, id_pessoa, id_vaga) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['id'],
            $data['id_pessoa'],
            $data['id_vaga']
        ]);
    }

    public function exists(string $id_pessoa, string $id_vaga): bool {
        $sql = "SELECT COUNT(*) FROM candidaturas WHERE id_pessoa = ? AND id_vaga = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_pessoa, $id_vaga]);
        return (bool) $stmt->fetchColumn();
    }

    public function getCandidatosByVaga(string $id_vaga): array {
        $sql = "SELECT p.id, p.nome, p.profissao, p.localizacao, p.nivel
                FROM candidaturas c
                INNER JOIN pessoas p ON c.id_pessoa = p.id
                WHERE c.id_vaga = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_vaga]);
        return $stmt->fetchAll(); // PDO::FETCH_ASSOC é o padrão agora
    }
}