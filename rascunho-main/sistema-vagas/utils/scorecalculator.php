<?php
declare(strict_types=1);

/**
 * Classe para calcular o score de um candidato para uma vaga,
 * seguindo as especificações da API.
 */
class ScoreCalculator
{
    /**
     * @var array<string, array<string, int>> Grafo representando as localidades e distâncias.
     */
    private array $graph;

    public function __construct()
    {
        // Representação do grafo do documento em uma lista de adjacência.
        $this->graph = [
            'A' => ['B' => 5],
            'B' => ['A' => 5, 'C' => 7, 'D' => 3],
            'C' => ['B' => 7, 'E' => 4],
            'D' => ['B' => 3, 'E' => 10, 'F' => 8],
            'E' => ['C' => 4, 'D' => 10],
            'F' => ['D' => 8]
        ];
    }

    /**
     * Calcula o score final do candidato, retornado como um número inteiro.
     *
     * @param int    $nivelVaga      Nível de experiência requerido pela vaga (1-5).
     * @param int    $nivelCandidato Nível de experiência do candidato (1-5).
     * @param string $localVaga      Localidade da vaga (A-F).
     * @param string $localCandidato Localidade do candidato (A-F).
     *
     * @return int|null O score final como um inteiro, ou null se as localidades forem inválidas.
     */
    public function calcularScore(int $nivelVaga, int $nivelCandidato, string $localVaga, string $localCandidato): ?int
    {
        // 1. Calcular o score de experiência (N)
        $scoreN = $this->calculateScoreN($nivelVaga, $nivelCandidato); //

        // 2. Encontrar a menor distância entre as localidades
        $distancia = $this->findShortestPathDistance($localCandidato, $localVaga); //

        // Se a distância for null, não há caminho entre os pontos.
        if ($distancia === null) {
            return null;
        }

        // 3. Converter a distância para o score D, conforme a tabela
        $scoreD = $this->calculateScoreD($distancia); //

        // 4. Calcular o SCORE final e retornar apenas a parte inteira
        $scoreFinal = ($scoreN + $scoreD) / 2; //
        
        return (int) $scoreFinal; //
    }

    /**
     * Calcula o componente N do score. N = 100 - 25 * |NV - NC|.
     */
    private function calculateScoreN(int $nivelVaga, int $nivelCandidato): int
    {
        return 100 - (25 * abs($nivelVaga - $nivelCandidato));
    }

    /**
     * Converte a distância do caminho mais curto para a pontuação D, usando a tabela de referência.
     */
    private function calculateScoreD(int $distance): int
    {
        if ($distance <= 5) { //
            return 100; //
        } elseif ($distance <= 10) { //
            return 75; //
        } elseif ($distance <= 15) { //
            return 50; //
        } elseif ($distance <= 20) { //
            return 25; //
        } else { // maiores que 20
            return 0; //
        }
    }

    /**
     * Encontra a menor distância no grafo usando o Algoritmo de Dijkstra.
     * @return int|null Retorna a distância ou null se não houver caminho.
     */
    private function findShortestPathDistance(string $inicio, string $fim): ?int
    {
        // Validação básica das localidades
        if (!isset($this->graph[$inicio]) || !isset($this->graph[$fim])) {
            return null;
        }

        $distancias = array_fill_keys(array_keys($this->graph), INF);
        $distancias[$inicio] = 0;
        $filaPrioridade = new SplPriorityQueue();
        $filaPrioridade->insert($inicio, 0);

        while (!$filaPrioridade->isEmpty()) {
            $verticeAtual = $filaPrioridade->extract();

            if (!isset($this->graph[$verticeAtual])) continue;

            foreach ($this->graph[$verticeAtual] as $vizinho => $peso) {
                $novaDistancia = $distancias[$verticeAtual] + $peso;
                if ($novaDistancia < $distancias[$vizinho]) {
                    $distancias[$vizinho] = $novaDistancia;
                    $filaPrioridade->insert($vizinho, -$novaDistancia);
                }
            }
        }
        
        $resultado = $distancias[$fim];
        return $resultado === INF ? null : (int) $resultado;
    }
}