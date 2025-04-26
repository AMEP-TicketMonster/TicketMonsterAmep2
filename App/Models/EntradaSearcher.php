<?php

namespace App\Models;

use \PDO;
use App\Config\Database;
use App\Models\EntradaGateway;

// Aquesta classe és la cercadora del patró Row Data Gateway
class EntradaSearcher {
    private \PDO $pdo;
    private $idEntrada;
    private $idUsuari;
    private $idEsdeveniment;
    private $tipus;
    private $preu;
    private $idEstatEntrada;

    public function __construct(?int $idEntrada = null) {
        $this->pdo = Database::getConnection(); // patró singleton
    }

    public function findByEntradaId(int $idEntrada): ?EntradaGateway {
        $stmt = $this->pdo->prepare("SELECT * FROM Entrades WHERE idEntrada = ?");
        $stmt->execute([$idEntrada]);
        $result = $stmt->fetch();
    
        return $result ? new EntradaGateway($idEntrada) : null;
    }
        
    public function findByUsuariId(int $idUsuari): array {
        $stmt = $this->pdo->prepare("SELECT * FROM Entrades WHERE idUsuari = ?");
        $stmt->execute([$idUsuari]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findAll(string $tipus): array {
        $stmt = $this->pdo->prepare("SELECT * FROM Entrades WHERE tipus = ?");
        $stmt->execute([$tipus]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
