<?php

namespace App\Models;

use \PDO;
use App\Config\Database;

// Aquesta classe compleix el patró Row Data Gateway
class SalesGateway {
    private \PDO $pdo;
    private int $idSala;
    private string $nom;
    private string $ciutat;
    private int $capacitat;

    public function __construct(?int $idSala = null) {
        $this->pdo = Database::getConnection(); // patró singleton

        if ($idSala !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM Sales WHERE idSala = ?");
            $stmt->execute([$idSala]);
            $data = $stmt->fetch();

            if ($data) {
                $this->idSala = $data['idSala'];
                $this->nom = $data['nom'];
                $this->ciutat = $data['ciutat'];
                $this->capacitat = $data['capacitat'];
            } else {
                throw new \Exception("Sala no trobada.");
            }
        }
    }

    public function create(string $nom, string $ciutat, int $capacitat): int {
        if ($capacitat <= 0) {
            throw new \Exception("La capacitat ha de ser major que 0.");
        }
    
        $stmt = $this->pdo->prepare("INSERT INTO Sales (nom, ciutat, capacitat) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $ciutat, $capacitat]);
    
        $this->idSala = (int) $this->pdo->lastInsertId();
        return $this->idSala;
    }   

    public function updateCapacitat(int $novaCapacitat): void {
        if ($this->idSala === null) {
            throw new \Exception("No es pot actualitzar una sala que no ha estat carregada.");
        }
        if ($novaCapacitat <= 0) {
            throw new \Exception("La capacitat ha de ser major que 0.");
        }

        $stmt = $this->pdo->prepare("UPDATE Sales SET capacitat = ? WHERE idSala = ?");
        $stmt->execute([$novaCapacitat, $this->idSala]);
        $this->capacitat = $novaCapacitat;
    }

    public function delete(): void {
        if ($this->idSala === null) {
            throw new \Exception("No es pot eliminar una sala que no ha estat carregada.");
        }

        $stmt = $this->pdo->prepare("DELETE FROM Sales WHERE idSala = ?");
        $stmt->execute([$this->idSala]);
    }

    // Getters
    public function getIdSala(): ?int {
        return $this->idSala;
    }
    public function getNom(): string {
        return $this->nom;
    }
    public function getCiutat(): string {
        return $this->ciutat;
    }
    public function getCapacitat(): int {
        return $this->capacitat;
    }
}
