<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private string $host;
    private string $dbname;
    private string $user;
    private string $password;
    /*
    private string $host = "localhost";
    private string $dbname = "";
    private string $user = "";
    private string $password = "";
*/
    private function __construct()
    {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->dbname = $_ENV['DB_DATABASE'] ?? 'default_db';
        $this->user = $_ENV['DB_USERNAME'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            die("Error al conectar con la base de datos.");
        }
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
}
