<?php

namespace App\Models;

use App\Config\Database;
use App\Models\SalesGateway;
use App\Models\SalesSearcher;

// Aquesta classe compleix el patró Row Data Gateway
class AssajosGateway
{
    private $pdo;
    private int $idAssajos;
    private int $idGrup;
    private int $idSala;
    private int $idDataSala;
    private int $entradesDisponibles;

    public function __construct(?int $idAssajos = null)
    {
        $this->pdo = Database::getConnection(); // patró singleton
        
        if ($idAssajos !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM Assajos WHERE idAssajos = ?");
            $stmt->execute([$idAssajos]);
            $data = $stmt->fetch();

            if ($data) {
                $this->idAssajos = $data['idAssajos'];
                $this->idGrup = $data['idGrup'];
                $this->idSala = $data['idSala'];
                $this->idDataSala = $data['idDataSala'];
                $this->entradesDisponibles = $data['entrades_disponibles'];
            } else {
                throw new Exception("Assaig no trobat.");
            }
        }
    }

    // Getters 
    public function getIdAssajos(): ?int {
        return $this->idAssajos;
    }

    public function getIdGrup(): int {
        return $this->idGrup;
    }

    public function getIdSala(): int {
        return $this->idSala;
    }

    public function getIdDataSala(): int {
        return $this->idDataSala;
    }

    public function getEntradesDisponibles(): int {
        return $this->entradesDisponibles;
    }

    // Creem un assaig (requereix que existeix idSala i idDataSala)
    public function create(int $idGrup, int $idSala, int $idDataSala, float $preuEntrada): ?int {
        // Obtenim la capacitat de la sala que serà les entrades disponibles de l'assaig
        $searcher = new SalesSearcher();
        $sales = $searcher->findById($idSala);
        if ($sales === null) {
            return null; 
        }
        $entradesDisponibles = $sales->getCapacitat();

        // Creem l'assaig
        $stmt = $this->pdo->prepare("INSERT INTO Assajos (idGrup, idSala, idDataSala, entrades_disponibles) VALUES (?, ?, ?, ?)");
        $stmt->execute([$idGrup, $idSala, $idDataSala, $entradesDisponibles]);
        $this->idAssajos = (int)$this->pdo->lastInsertId();

        // Creem totes les entrades per aquest assaig
        $placeholders = array_fill(0, $entradesDisponibles, "(?, ?, ?)");
        $sql = "INSERT INTO EntradesAssaig (idAssaig, preu, idEstatEntrada) VALUES " . implode(", ", $placeholders);
        $stmt = $this->pdo->prepare($sql);
        $params = []; 
        for ($i = 0; $i < $entradesDisponibles; $i++) {
            array_push($params, $this->idAssajos, $preuEntrada, 3); // 3 és Disponible
        }        
        $stmt->execute($params);

        return $this->idAssajos;
    }
    
    // Incrementa les entrades disponibles
    public function incrementaEntradesDisponibles(int $valor): void {
        if ($this->idAssajos === null) {
            throw new Exception("No es pot actualitzar un Assaig que no ha estat carregat.");
        }       
        if ($valor < 0){
            throw new Exception("El valor a incrementar no pot ser negatiu.");
        }
        $this->entradesDisponibles += $valor;
        $stmt = $this->pdo->prepare("UPDATE Assajos SET entrades_disponibles = ? WHERE idAssajos = ?");
        $stmt->execute([$this->entradesDisponibles, $this->idAssajos]);
    }

    // Decrementa les entrades disponibles
    public function decrementaEntradesDisponibles(int $valor): void {
        if ($this->idAssajos === null) {
            throw new Exception("No es pot actualitzar un Assaig que no ha estat carregat.");
        }       
        if ($valor < 0){
            throw new Exception("El valor a decrementar no pot ser negatiu.");
        }
        if ($this->entradesDisponibles - $valor < 0) {
            throw new Exception("No es poden tenir entrades negatives.");
        }
        $this->entradesDisponibles -= $valor;
        $stmt = $this->pdo->prepare("UPDATE Assajos SET entrades_disponibles = ? WHERE idAssajos = ?");
        $stmt->execute([$this->entradesDisponibles, $this->idAssajos]);
    }

    // Borrem el registre idAssajos de la taula
    public function delete(): void {
        if ($this->idAssajos !== null) {
            $stmt = $this->pdo->prepare("DELETE FROM Assajos WHERE idAssajos = ?");
            $stmt->execute([$this->idAssajos]);
        } else {
            throw new Exception("No es pot eliminar un Assaig que no ha estat carregat.");
        }
    }
}
