<?php

namespace App\Models;

use App\Config\Database;
use Composer\InstalledVersions;

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
    public function getEntradesComprades($idUsuari){
        //tipus, nomConcert, nomGrup
        $stmt = $this->pdo->prepare("
        SELECT Entrades.idEntrada,
        EntradesUsuari.data_transaccio,
        Entrades.preu,
        Entrades.tipus,
        Concerts.nomConcert,
        GrupsMusicals.nomGrup,
        DataSala.dia,
        DataSala.hora_inici,
        DataSala.hora_fi
        FROM EntradesUsuari 
        JOIN Entrades ON Entrades.idEntrada = EntradesUsuari.idEntrada
        JOIN Concerts ON Concerts.idConcert = Entrades.idConcert
        JOIN GrupsMusicals ON Concerts.idGrup = GrupsMusicals.idGrup
        JOIN DataSala ON Concerts.idDataSala = DataSala.idDataSala

        WHERE idUsuari = ?"
    );
        $stmt->execute([$idUsuari]);
        return  $stmt->fetchAll();
    }

    public function getEntradaAssaigById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM EntradesAssaig WHERE idEntrada = ?");
        $stmt->execute([$id]);
        return  $stmt->fetch();
    }

    public function getEntradaConcertById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Entrades WHERE idEntrada = ?");
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

public function getEstatId($estat)
{
    $stmt = $this->pdo->prepare("SELECT idEstatEntrada FROM EstatEntrada WHERE estat = ?");
    $stmt->execute([$estat]);
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $res ? $res['idEstatEntrada'] : null;
}

public function getById($idEntrada)
{
    $stmt = $this->pdo->prepare("SELECT * FROM Entrades WHERE idEntrada = ?");
    $stmt->execute([$idEntrada]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}
public function cancelarReserva($idEntrada, $idUsuari)
{
    $stmt = $this->pdo->prepare("DELETE FROM EntradesUsuari WHERE idEntrada = ? AND idUsuari = ?");
    $stmt->execute([$idEntrada, $idUsuari]);
    return $stmt->rowCount(); // Devuelve cuántas filas eliminó
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
        // 1. Obtener el ID del estado ("Comprada" o "Reservada")
        $stmt = $this->pdo->prepare("SELECT idEstatEntrada FROM EstatEntrada WHERE estat = ?");
        $stmt->execute([$nou_estat]);
        $nouEstatId = $stmt->fetch(\PDO::FETCH_ASSOC)['idEstatEntrada'];
    
        // 2. Actualizar el estado de la entrada (sin tocar idUsuari)
        $stmt = $this->pdo->prepare("UPDATE Entrades SET idEstatEntrada = ? WHERE idEntrada = ?");
        $stmt->execute([$nouEstatId, $idEntrada]);
    
        // 3. Insertar la relación usuario-entrada
        $stmt = $this->pdo->prepare("
            INSERT INTO EntradesUsuari (idEntrada, idUsuari, data_transaccio)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$idEntrada, $idUsuari]);
    }
    
    /*public function assignarEntradaConcert($idEntrada, $idUsuari, $nou_estat)
    {
        // Falta por tratar el tema del aforo, ver si quedan entradas disponibles. En el controlador también debe de comprobarlo.
        //Queda pendiente.

        $stmt = $this->pdo->prepare("INSERT INTO EntradesUsuari(idEntrada, idUsuari, data_transaccio)VALUES(?, ?, CURDATE());");
        $stmt->execute([$idEntrada, $idUsuari]);
        $stmt->fetch(\PDO::FETCH_ASSOC);
    } */

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


    public function actualizarEstatEntrada($idEntrada, $nouEstat)
{
    $idEstat = $this->getEstatId($nouEstat);
    $stmt = $this->pdo->prepare("UPDATE Entrades SET idEstatEntrada = ? WHERE idEntrada = ?");
    $stmt->execute([$idEstat, $idEntrada]);
}

public function incrementarEntradesDisponiblesConcert($idConcert)
{
    $stmt = $this->pdo->prepare("UPDATE Concerts SET entrades_disponibles = entrades_disponibles + 1 WHERE idConcert = ?");
    $stmt->execute([$idConcert]);
}



        // Retorna una entrada disponible para un concierto
    public function getEntradaDisponiblePorConcert($idConcert)
        {
            $stmt = $this->pdo->prepare("
                SELECT idEntrada 
                FROM Entrades 
                WHERE idConcert = ? 
                AND idEstatEntrada = (
                    SELECT idEstatEntrada FROM EstatEntrada WHERE estat = 'Disponible'
                )
                LIMIT 1
            ");
            $stmt->execute([$idConcert]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    
        public function crearEntradesPerConcert($idConcert, $quantitat, $preu)
        {
            $sql = "INSERT INTO Entrades (idEsdeveniment, tipus, preu, idEstatEntrada, idConcert)
                    VALUES (:idEsdeveniment, 'Concert', :preu, :idEstatEntrada, :idConcert)";
        
            $stmt = $this->pdo->prepare($sql);
        
            $idEstatEntrada = 3;
        
            for ($i = 0; $i < $quantitat; $i++) {
                $stmt->execute([
                    ':idEsdeveniment' => $idConcert,
                    ':preu' => $preu,
                    ':idEstatEntrada' => $idEstatEntrada,
                    ':idConcert' => $idConcert
                ]);
            }
        }
        

}



