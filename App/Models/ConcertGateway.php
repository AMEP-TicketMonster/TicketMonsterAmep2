<?php

namespace App\Models;

use App\Config\Database;

class ConcertGateway
{
    private $pdo;
    private $idConcert;
    private $idGrup;
    private $idSala;
    private $nomConcert;
    private $entradesDisponibles;
    private $idGenere;
    private $idDataSala;

    public function __construct(?int $idConcert = null)
    {
        $this->pdo = Database::getConnection(); // patró singleton
        
        if ($idConcert !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM Concerts WHERE idConcert = ?");
            $stmt->execute([$idConcert]);
            $data = $stmt->fetch();

            if ($data) {
                $this->idConcert = $data['idConcert'];
                $this->idGrup = $data['idGrup'];
                $this->idSala = $data['idSala'];
                $this->nomConcert = $data['nomConcert'];
                $this->entradesDisponibles = $data['entrades_disponibles'];
                $this->idGenere = $data['idGenere'];
                $this->idDataSala = $data['idDataSala'];
            } else {
                throw new Exception("Concert no trobat.");
            }
        }
    }

    // Getters 
    public function getIdConcert(): ?int {
        return $this->idConcert;
    }

    public function getIdGrup(): int {
        return $this->idGrup;
    }

    public function getIdSala(): int {
        return $this->idSala;
    }

    public function getNomConcert(): string {
        return $this->nomConcert;
    }

    public function getEntradesDisponibles(): int {
        return $this->entradesDisponibles;
    }

    public function getIdGenere(): int {
        return $this->idGenere;
    }

    public function getIdDataSala(): int {
        return $this->idDataSala;
    }

    public function createConcert($idGrup, $idSala, $nomConcert, $idDataSala, $preu, $idGenere): ?int
    {
        // Obtenim la capacitat de la sala que serà les entrades disponibles del concert
        $searcher = new SalesSearcher();
        $sales = $searcher->findById($idSala);
        if ($sales === null) {
            return null; 
        }
        $entradesDisponibles = $sales->getCapacitat();

        // Creem el concert
        $sql = "INSERT INTO Concerts (idGrup, idSala, idDataSala, nomConcert, entrades_disponibles, idGenere)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idGrup, $idSala, $idDataSala, $nomConcert, $entradesDisponibles, $idGenere]);
        $stmt = $this->pdo->query("SELECT LAST_INSERT_ID()");
        $this->idConcert = $stmt->fetchColumn();

        // Creem totes les entrades per aquest concert
        $placeholders = array_fill(0, $entradesDisponibles, "(?, ?, ?)");
        $sql = "INSERT INTO EntradesConcert (idConcert, preu, idEstatEntrada) VALUES " . implode(", ", $placeholders);
        $stmt = $this->pdo->prepare($sql);
        $params = []; 
        for ($i = 0; $i < $entradesDisponibles; $i++) {
            array_push($params, $this->idConcert, $preu, 3); // 3 és Disponible
        }        
        $stmt->execute($params);
        return $this->idConcert;
    }

    // Incrementa les entrades disponibles
    public function incrementaEntradesDisponibles(int $valor): void {
        if ($this->idConcert === null) {
            throw new Exception("No es pot actualitzar un Concert que no ha estat carregat.");
        }       
        if ($valor < 0){
            throw new Exception("El valor a incrementar no pot ser negatiu.");
        }
        $this->entradesDisponibles += $valor;
        $stmt = $this->pdo->prepare("UPDATE Concerts SET entrades_disponibles = ? WHERE idConcert = ?");
        $stmt->execute([$this->entradesDisponibles, $this->idConcert]);
    }

    // Decrementa les entrades disponibles
    public function decrementaEntradesDisponibles(int $valor): void {
        if ($this->idConcert === null) {
            throw new Exception("No es pot actualitzar un Concert que no ha estat carregat.");
        }       
        if ($valor < 0){
            throw new Exception("El valor a decrementar no pot ser negatiu.");
        }
        if ($this->entradesDisponibles - $valor < 0) {
            throw new Exception("No es poden tenir entrades negatives.");
        }
        $this->entradesDisponibles -= $valor;
        $stmt = $this->pdo->prepare("UPDATE Concerts SET entrades_disponibles = ? WHERE idConcert = ?");
        $stmt->execute([$this->entradesDisponibles, $this->idConcert]);
    }

    // Borrem el registre idConcert de la taula
    public function delete(): void {
        if ($this->idConcert !== null) {
            $stmt = $this->pdo->prepare("DELETE FROM Concerts WHERE idConcert = ?");
            $stmt->execute([$this->idConcert]);
        } else {
            throw new Exception("No es pot eliminar un Concert que no ha estat carregat.");
        }
    }

    

}
