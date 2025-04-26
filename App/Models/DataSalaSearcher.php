<?php

namespace App\Models;

use \PDO;
use App\Config\Database;
use App\Models\DataSalaGateway;

class DataSalaSearcher {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection(); // patró singleton
    }

    public function findById(int $idDataSala): ?DataSalaGateway {
        $stmt = $this->pdo->prepare("SELECT * FROM DataSala WHERE idDataSala = ?");
        $stmt->execute([$idDataSala]);
        $data = $stmt->fetch();

        return $data ? new DataSalaGateway($idDataSala) : null;
    }

    public function findBySala(int $idSala): array {
        $stmt = $this->pdo->prepare("SELECT * FROM DataSala WHERE idSala = ?");
        $stmt->execute([$idSala]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByDate(string $dia): array {
        $stmt = $this->pdo->prepare("SELECT * FROM DataSala WHERE dia = ?");
        $stmt->execute([$dia]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM DataSala");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
