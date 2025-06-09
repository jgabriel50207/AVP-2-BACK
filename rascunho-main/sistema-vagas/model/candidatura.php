<?php
declare(strict_types=1);

// Garante que o arquivo de conexão com o banco seja carregado.
require_once __DIR__ . '/../config/database.php';

/**
 * Classe Candidatura
 *
 * Gerencia todas as operações de banco de dados para a tabela 'candidaturas'.
 */
class Candidatura
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
        // Garante que este model sempre lance exceções em caso de erros de BD.
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Cria um novo registro de candidatura no banco de dados, incluindo o score.
     *
     * @param array $data Um array associativo contendo 'id', 'id_pessoa', 'id_vaga' e 'score'.
     * @return bool Retorna true em caso de sucesso na execução.
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO candidaturas (id, id_pessoa, id_vaga, score) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['id'],
            $data['id_pessoa'], 
            $data['id_vaga'],
            $data['score']
        ]);
    }

    /**
     * Verifica se já existe uma candidatura para uma pessoa em uma determinada vaga.
     *
     * @param string $id_pessoa O UUID da pessoa.
     * @param string $id_vaga   O UUID da vaga.
     * @return bool Retorna true se a candidatura já existir, false caso contrário.
     */
    public function exists(string $id_pessoa, string $id_vaga): bool
    {
        $sql = "SELECT COUNT(*) FROM candidaturas WHERE id_pessoa = ? AND id_vaga = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_pessoa, $id_vaga]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Busca os detalhes completos de uma candidatura específica pelo seu ID.
     *
     * @param string $id O UUID da candidatura.
     * @return array|null Retorna um array com os dados da candidatura ou null se não for encontrada.
     */
    

    /**
     * Retorna o ranking de candidatos para uma vaga, ordenados pelo score.
     *
     * @param string $id_vaga O UUID da vaga.
     * @return array Uma lista de candidatos com seus dados e score.
     */
    public function getRankingByVaga(string $id_vaga): array
    {
        $sql = "
            SELECT
                p.nome,
                p.profissao,
                p.localizacao,
                p.nivel,
                c.score
            FROM
                candidaturas c
            INNER JOIN
                pessoas p ON c.id_pessoa = p.id
            WHERE
                c.id_vaga = ?
            ORDER BY
                c.score DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_vaga]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém todos os candidatos que se aplicaram a uma vaga específica (sem ordenação por score).
     *
     * @param string $id_vaga O UUID da vaga.
     * @return array Retorna um array de candidatos.
     */
    public function getCandidatosByVaga(string $id_vaga): array
    {
        $sql = "
            SELECT p.id, p.nome, p.profissao, p.localizacao, p.nivel
            FROM candidaturas c
            INNER JOIN pessoas p ON c.id_pessoa = p.id
            WHERE c.id_vaga = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_vaga]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}