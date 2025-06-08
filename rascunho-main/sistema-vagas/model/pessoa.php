<?php
// ARQUIVO: model/pessoa.php

class Pessoa {
    private $db;

    public function __construct() {
    // Chama diretamente o seu método estático "conectar"
    $this->db = Database::conectar();
}

    public function create($data) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO pessoas (id, nome, profissao, localizacao, nivel) VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $data['id'],
                $data['nome'],
                $data['profissao'],
                strtoupper($data['localizacao']),
                $data['nivel']
            ]);
        } catch (PDOException $e) {
            // Lançar a exceção permite que o bloco try/catch no endpoint a capture
            throw $e;
        }
    }

    public function exists($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM pessoas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
} // <-- O ARQUIVO TERMINA AQUI. NADA MAIS.
?>