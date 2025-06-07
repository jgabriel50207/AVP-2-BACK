<?php
class Database {
    public static function conectar() {
        $host = 'localhost';
        $dbname = 'sistema_vagas';
        $user = 'root';
        $pass = ''; // sua senha do MySQL

        try {
            return new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao conectar ao banco de dados']);
            exit;
        }
    }
}
?>
