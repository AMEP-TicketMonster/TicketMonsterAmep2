<?php

namespace App\Models;

use \PDO;
use App\Config\Database;
use App\Models\SalesGateway;

// Aquesta classe és la cercadora del patró Row Data Gateway
class SalesSearcher {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection(); // patró singleton
    }

    public function findById(int $idSala): ?SalesGateway {
        $stmt = $this->pdo->prepare("SELECT * FROM Sales WHERE idSala = ?");
        $stmt->execute([$idSala]);
        $data = $stmt->fetch();

        return $data ? new SalesGateway($idSala) : null;
    }

    public function findByCity(string $ciutat): array {
        $stmt = $this->pdo->prepare("SELECT * FROM Sales WHERE ciutat = ?");
        $stmt->execute([$ciutat]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM Sales");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
