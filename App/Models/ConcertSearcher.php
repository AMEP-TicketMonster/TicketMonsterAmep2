<?php

namespace App\Models;

use \PDO;
use App\Config\Database;
use App\Models\ConcertGateway;


// Aquesta classe és la cercadora del patró Row Data Gateway
class ConcertSearcher {
    private \PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection(); // patró singleton
    }

    public function findByConcertId(int $idConcert): ?ConcertGateway {
        $stmt = $this->pdo->prepare("SELECT * FROM Concerts WHERE idConcert = ?");
        $stmt->execute([$idConcert]);
        $result = $stmt->fetch();
    
        return $result ? new ConcertGateway($idConcert) : null;
    }
        
    public function findByGrup(int $idGrup): array {
        $stmt = $this->pdo->prepare("SELECT * FROM Concerts WHERE idGrup = ?");
        $stmt->execute([$idGrup]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findBySala(int $idSala): array {
        $stmt = $this->pdo->prepare("SELECT * FROM Concerts WHERE idSala = ?");
        $stmt->execute([$idSala]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT c.* FROM Concerts c JOIN DataSala d ON c.idDataSala = d.idDataSala WHERE d.dia > CURDATE()");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
