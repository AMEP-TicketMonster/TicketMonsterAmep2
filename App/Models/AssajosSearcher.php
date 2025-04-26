<?php

namespace App\Models;

use App\Config\Database;
use App\Models\AssajosGateway;


// Aquesta classe és la cercadora del patró Row Data Gateway
class AssajosSearcher {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection(); // patró singleton
    }

    public function findByAssajosId(int $idAssajos): ?AssajosGateway {
        $stmt = $this->pdo->prepare("SELECT * FROM Assajos WHERE idAssajos = ?");
        $stmt->execute([$idAssajos]);
        $result = $stmt->fetch();
    
        return $result ? new AssajosGateway($idAssajos) : null;
    }
        
    public function findByGrup(int $idGrup): array {
        $stmt = $this->conn->prepare("SELECT * FROM Assajos WHERE idGrup = ?");
        $stmt->execute([$idGrup]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findBySala(int $idSala): array {
        $stmt = $this->conn->prepare("SELECT * FROM Assajos WHERE idSala = ?");
        $stmt->execute([$idSala]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAll(): array {
        $stmt = $this->conn->query("SELECT * FROM Assajos");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
