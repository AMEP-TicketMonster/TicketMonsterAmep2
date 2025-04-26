<?php

namespace App\Models;
use \PDO;
use App\Config\Database;

// Aquesta classe compleix el patró Row Data Gateway
class EntradaGateway
{
    private PDO $pdo;
    private $idEntrada;
    private $idUsuari;
    private $idEsdeveniment;
    private $tipus;
    private $preu;
    private $idEstatEntrada;

    public function __construct()
    {
        $this->pdo = Database::getConnection(); // patró singleton

        if ($idEntrada !== null) {
            $stmt = $this->pdo->prepare("SELECT * FROM Entrades WHERE idEntrada = ?");
            $stmt->execute([$idEntrada]);
            $data = $stmt->fetch();

            if ($data) {
                $this->idEntrada = $data['idEntrada'];
                $this->idUsuari = $data['idUsuari'];
                $this->idEsdeveniment = $data['idEsdeveniment'];
                $this->tipus = $data['tipus'];
                $this->preu = $data['preu'];
                $this->idEstatEntrada = $data['idEstatEntrada'];
            } else {
                throw new \Exception("Entrada no trobada.");
            }
        }
    }

    // Getters
    public function getIdEntrada(): int {
        return $this->idEntrada;
    }

    public function getIdUsuari(): int {
        return $this->idUsuari;
    }

    public function getIdEsdeveniment(): int {
        return $this->idEsdeveniment;
    }
    
    public function getTipus(): string {
        return $this->tipus;
    }

    public function getPreu(): float {
        return $this->preu;
    }
    
    public function getIdEstatEntrada(): int {
        return $this->idEstatEntrada;
    }
 
    public function getStringFromEntradaId($idEntrada)
    {
        // Obtenim el string del estat a partir del seu id
        $stmt = $this->pdo->prepare("SELECT estat FROM EstatEntrada WHERE idEstatEntrada = ?");
        $stmt->execute([$idEntrada]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['estat'];
    }

    // quan un usuari compra/reserva/cancela una entrada modifica el idUsuari i el estat de l'entrada
    public function assignarEntrada($idEntrada, $idUsuari, $nou_estat)
    {
        // Obtenim el id del estat a partir del seu string
        $stmt = $this->pdo->prepare("SELECT idEstatEntrada FROM EstatEntrada WHERE estat = ?");
        $stmt->execute([$nou_estat]);
        $nou_estat_id = $stmt->fetch(\PDO::FETCH_ASSOC)['idEstatEntrada'];

        $sql = "UPDATE Entrades 
                SET idUsuari = ?, idEstatEntrada = ?
                WHERE idEntrada = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuari, $nou_estat_id, $idEntrada]);
    }
    
}
