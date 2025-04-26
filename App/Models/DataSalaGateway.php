<?php

namespace App\Models;

use \PDO;
use App\Config\Database;

class DataSalaGateway {
    private PDO $pdo;
    private int $idDataSala;
    private string $dia;
    private string $horaInici;
    private string $horaFi;
    private int $idSala;

    public function __construct(?int $idDataSala = null) {
        $this->pdo = Database::getConnection(); // patró singleton

        if ($idDataSala !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM DataSala WHERE idDataSala = ?");
            $stmt->execute([$idDataSala]);
            $data = $stmt->fetch();

            if ($data) {
                $this->idDataSala = $data['idDataSala'];
                $this->dia = $data['dia'];
                $this->horaInici = $data['hora_inici'];
                $this->horaFi = $data['hora_fi'];
                $this->idSala = $data['idSala'];
            } else {
                throw new \Exception("DataSala no trobada.");
            }
        }
    }

    public function create(string $dia, string $horaInici, string $horaFi, int $idSala): ?int {
        // Comprovem si la sala està disponible
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM DataSala WHERE idSala = ? AND dia = ? AND hora_inici = ? AND hora_fi = ?");
        $stmt->execute([$idSala, $dia, $horaInici, $horaFi]);
        $exists = $stmt->fetchColumn();
    
        if ($exists > 0) {
            echo "Error: aquesta sala ja està utilitzada aquest dia i hora";
            return null;
        }
    
        // Creem la nova entrada a la taula
        $stmt = $this->pdo->prepare("INSERT INTO DataSala (dia, hora_inici, hora_fi, idSala) VALUES (?, ?, ?, ?)");
        $stmt->execute([$dia, $horaInici, $horaFi, $idSala]);
        $this->dia = $dia;
        $this->horaInici = $horaInici;
        $this->horaFi = $horaFi;
        $this->idSala = $idSala;
        $this->idDataSala = (int) $this->pdo->lastInsertId();
        return $this->idDataSala;
    }
    


    public function updateHoraFi(string $novaHoraFi): void {
        if (!isset($this->idDataSala)) {
            throw new \Exception("No es pot actualitzar una DataSala que no ha estat carregada.");
        }

        $stmt = $this->pdo->prepare("UPDATE DataSala SET hora_fi = ? WHERE idDataSala = ?");
        $stmt->execute([$novaHoraFi, $this->idDataSala]);
        $this->horaFi = $novaHoraFi;
    }

    public function delete(): void {
        if (!isset($this->idDataSala)) {
            throw new \Exception("No es pot eliminar una DataSala que no ha estat carregada.");
        }

        $stmt = $this->pdo->prepare("DELETE FROM DataSala WHERE idDataSala = ?");
        $stmt->execute([$this->idDataSala]);
    }

    // Getters
    public function getIdDataSala(): int {
        return $this->idDataSala;
    }
    public function getDia(): string {
        return $this->dia;
    }
    public function getHoraInici(): string {
        return $this->horaInici;
    }
    public function getHoraFi(): string {
        return $this->horaFi;
    }
    public function getIdSala(): int {
        return $this->idSala;
    }
}
