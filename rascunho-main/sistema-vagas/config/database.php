<?php
declare(strict_types=1);

class Database {
    private static ?self $instance = null;
    private PDO $connection;

    private string  $host = 'localhost'; // host
    private string $db_name = 'sistema_vagas';   // sbanco
    private string $username = 'root';  //  usuário
    private string $password = ''; // senha

    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->connection = new PDO($dsn, $this->username, $this->password, $options);
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}