<?php
// model/vaga.php

require_once __DIR__ . '/../config/database.php';

class Vaga {
    private $db;

    public function __construct() {
        $this->db = Database::conectar(); // Conexão correta e armazenada em $this->db
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO vagas (id, empresa, titulo, descricao, localizacao, nivel)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['id'],
            $data['empresa'],
            $data['titulo'],
            $data['descricao'] ?? null,
            strtoupper($data['localizacao']),
            $data['nivel']
        ]);
    }

    public function exists($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM vagas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM vagas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
