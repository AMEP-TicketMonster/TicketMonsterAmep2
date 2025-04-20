<?php

namespace App\Models;

use App\Config\Database;

class EntradaGateway
{
    private $pdo;
    private $id;
    //to do...

    public function __construct()
    {
        $this->pdo = Database::getConnection(); // patrón singleton
    }

    //cargar entradas
    public function getEntradaAssaigById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM EntradesAssaig WHERE idEntrada = ?");
        $stmt->execute([$id]);
        return  $stmt->fetch();
    }

    public function getEntradaConcertById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM EntradesConcert WHERE idEntrada = ?");
        $stmt->execute([$id]);
        return  $stmt->fetch();
    }
    
    public function getAllEntradesAssaig()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM EntradesAssaig");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
 
    public function getStringFromEntradaId($idEntrada)
    {
        // Obtenim el string del estat a partir del seu id
        $stmt = $this->pdo->prepare("SELECT estat FROM EstatEntrada WHERE idEstatEntrada = ?");
        $stmt->execute([$idEntrada]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['estat'];
    }

    public function assignarEntradaAssaig($idEntrada, $idUsuari, $nou_estat)
    {
        // Obtenim el id del estat a partir del seu string
        $stmt = $this->pdo->prepare("SELECT idEstatEntrada FROM EstatEntrada WHERE estat = ?");
        $stmt->execute([$nou_estat]);
        $nou_estat_id = $stmt->fetch(\PDO::FETCH_ASSOC)['idEstatEntrada'];

        $sql = "UPDATE EntradesAssaig 
                SET idUsuari = ?, idEstatEntrada = ?
                WHERE idEntrada = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuari, $nou_estat_id, $idEntrada]);
    }

    public function assignarEntradaConcert($idEntrada, $idUsuari, $nou_estat)
    {
        // Obtenim el id del estat a partir del seu string
        $stmt = $this->pdo->prepare("SELECT idEstatEntrada FROM EstatEntrada WHERE estat = ?");
        $stmt->execute([$nou_estat]);
        $nou_estat_id = $stmt->fetch(\PDO::FETCH_ASSOC)['idEstatEntrada'];

        $sql = "UPDATE EntradesConcert 
                SET idUsuari = ?, idEstatEntrada = ?
                WHERE idEntrada = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuari, $nou_estat_id, $idEntrada]);
    }

    public function decrementarEntradesDisponiblesAssaig($idAssaig)
    {
        $stmt = $this->pdo->prepare("UPDATE Assajos SET entrades_disponibles = entrades_disponibles - 1
                                     WHERE idAssajos = ? AND entrades_disponibles > 0");
        $stmt->execute([$idAssaig]);
    }

    public function decrementarEntradesDisponiblesConcert($idConcert)
    {
        $stmt = $this->pdo->prepare("UPDATE Concerts SET entrades_disponibles = entrades_disponibles - 1
                                     WHERE idConcert = ? AND entrades_disponibles > 0");
        $stmt->execute([$idConcert]);
    }

    // Retorna totes les entrades del usuari amb id = $idUsuari
    public function getTicketsByUserId($idUsuari)
    {
        // Validación estricta: entero positivo
        if (!filter_var($idUsuari, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new InvalidArgumentException("ID de concierto no válido");
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT idEntrada, idUsuari, idConcert, preu, estat 
                FROM Entrades_Concert 
                WHERE IdUsuari = ?"
            );

            if (!$stmt) {
                throw new RuntimeException("Error preparando la consulta SQL.");
            }

            $stmt->execute([$idConcierto]);
            $entrades = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $entrades;
        } catch (PDOException $e) {
            // Puedes loguearlo o relanzarlo según tu política de errores
            throw new RuntimeException("Error en la consulta a la base de datos: " . $e->getMessage());
        }
    }

    // Retorna totes les entrades del concert amb id = $idConcert
    public function getTicketsByConcertId($idConcierto)
    {
        // Validación estricta: entero positivo
        if (!filter_var($idConcierto, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new InvalidArgumentException("ID de concierto no válido");
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT IdEntrada, IdUsuari, IdConcert, preu, estat 
                FROM Entrades_Concert 
                WHERE IdConcert = ?"
            );

            if (!$stmt) {
                throw new RuntimeException("Error preparando la consulta SQL.");
            }

            $stmt->execute([$idConcierto]);
            $entrades = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $entrades;
        } catch (PDOException $e) {
            // Puedes loguearlo o relanzarlo según tu política de errores
            throw new RuntimeException("Error en la consulta a la base de datos: " . $e->getMessage());
        }
    }
}
