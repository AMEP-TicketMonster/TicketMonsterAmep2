<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class SalesGateway
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function existeSala($idSala)
    {

        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Sales WHERE idSala = ?");
            $stmt->execute([$idSala]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {

            error_log("Error en existeSala: " . $e->getMessage());
            return false;
        }
    }

    public function existeFranjaHoraria($idDataSala, $idSala)
    {
        try {

            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) 
                 FROM DataSales 
                 WHERE idDataSala = ? AND idSala = ?"
            );
            $stmt->execute([$idDataSala, $idSala]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {

            error_log("Error en existeFranjaHoraria: " . $e->getMessage());
            return false;
        }
    }

    public function hiHaDisponibilitat($idSala, $idDataSala)
    {

        try {

            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) 
                 FROM Assajos 
                 WHERE idSala = ? AND idDataSala = ?"
            );
            $stmt->execute([$idSala, $idDataSala]);
            $reservasExistentes = $stmt->fetchColumn();

            return $reservasExistentes == 0;
        } catch (PDOException $e) {

            error_log("Error en hiHaDisponibilitat: " . $e->getMessage());
            return false;
        }
    }

    public function crearReserva($idUsuari, $idSala, $idDataSala)
    {

        try {

            $stmt = $this->pdo->prepare(
                "INSERT INTO Assajos (idUsuari, idSala, idDataSala) 
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$idUsuari, $idSala, $idDataSala]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {

            error_log("Error en crearReserva: " . $e->getMessage());
            return false;
        }
    }

    public function getReservesSala($idSala)
    {

        try {

            $stmt = $this->pdo->prepare(
                "SELECT a.idGrup, ds.dia, ds.hora_inici, ds.hora_fi
                 FROM Assajos a
                 INNER JOIN DataSales ds ON a.idDataSala = ds.idDataSala
                 WHERE a.idSala = ?"
            );
            $stmt->execute([$idSala]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            error_log("Error en getReservesSala: " . $e->getMessage());
            return [];
        }
    }

    public function getReservesUsuari($idUsuari)
    {

        try {

            $stmt = $this->pdo->prepare(
                "SELECT s.nom, s.ciutat, ds.dia, ds.hora_inici, ds.hora_fi
                 FROM Assajos a
                 INNER JOIN Sales s ON a.idSala = s.idSala
                 INNER JOIN DataSales ds ON a.idDataSala = ds.idDataSala
                 WHERE a.idUsuari = ?"
            );
            $stmt->execute([$idUsuari]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            error_log("Error en getReservesUsuari: " . $e->getMessage());
            return [];
        }
    }

    public function esReservaUsuari($idUsuari, $idAssaig)
    {

        try {

            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) 
                 FROM Assajos 
                 WHERE idAssaig = ? AND idUsuari = ?"
            );
            $stmt->execute([$idAssaig, $idUsuari]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {

            error_log("Error en esReservaUsuari: " . $e->getMessage());
            return false;
        }
    }

    public function actualitzarReserva($idAssaig, $nouIdSala, $nouIdDataSala)
    {
        try {

            $stmt = $this->pdo->prepare(
                "UPDATE Assajos 
                 SET idSala = ?, idDataSala = ? 
                 WHERE idAssaig = ?"
            );
            $stmt->execute([$nouIdSala, $nouIdDataSala, $idAssaig]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {

            error_log("Error en actualitzarReserva: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarReserva($idAssaig)
    {

        try {

            $stmt = $this->pdo->prepare(
                "DELETE FROM Assajos 
                 WHERE idAssaig = ?"
            );
            $stmt->execute([$idAssaig]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {

            error_log("Error en eliminarReserva: " . $e->getMessage());
            return false;
        }
    }

    public function getSalas()



    {


        $stmt = $this->pdo->prepare("SELECT * FROM Sales");


        $stmt->execute();


        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function reservaSalaConcert($idSala, $hora_ini, $hora_fi, $dia)


    {


        $stmt = $this->pdo->prepare("INSERT INTO DataSala (dia, hora_inici, hora_fi, idSala)


                VALUES (?, ?, ?, ?)");


        $stmt->execute([$dia, $hora_ini, $hora_fi, $idSala]);


        //devolver la idDataSala


        return $this->pdo->lastInsertId();
    }


    public function getDataSala($idDataSala)


    {


        $stmt = $this->pdo->prepare("SELECT * FROM DataSala WHERE idDataSala = ?");


        $stmt->execute([$idDataSala]);


        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        //var_dump($res);
<<<<<<< HEAD
    }
    public function getAforamentSala($id){
        $stmt = $this->pdo->prepare("SELECT capacitat FROM Sales WHERE idSala = ? LIMIT 1");
        $stmt->execute([$id]);
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $res;
=======


>>>>>>> 9e3be27345fc8dab74ab7234f78e7f1f83e07d60
    }


    public function getAforamentSala($id)
    {


        $stmt = $this->pdo->prepare("SELECT capacitat FROM Sales WHERE idSala = ? LIMIT 1");


        $stmt->execute([$id]);


        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
