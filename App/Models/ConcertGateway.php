<?php

namespace App\Models;

use App\Config\Database;
use Exception;

class ConcertGateway
{
    private $pdo;
    private $id;
    private $nomConcert;
    private $data;
    private $aforament;
    private $preu;
    private $idUsuariOrganitzador;

    public function __construct()
    {
        $this->pdo = Database::getConnection(); // patrón singleton
    }

    // Cargar todos los conciertos
    public function getConcertList()
    {
        //Habría que poner un LIMIT 'int, sin las comillas'
        // idConcert, nomConcert AS nom, dia, nomGrup AS grup, nomGenere AS Genere, Sales.nom AS sala, Sales.ciutat AS ubicacio
        /*$stmt = $this->pdo->prepare("SELECT idConcert, nomConcert AS nom, dia, nomGrup AS grup, nomGenere AS Genere, Sales.nom AS sala, Sales.ciutat AS ubicacio  FROM Concerts JOIN DataSala ON Concerts.idDataSala = DataSala.idDataSala JOIN GrupsMusicals ON Concerts.idGrup = GrupsMusicals.idGrup JOIN  Sales ON Concerts.idSala = Sales.idSala JOIN Generes ON Concerts.idGenere = Generes.idGenere and DataSala.dia > CURDATE();
        ");
       */
        $stmt = $this->pdo->prepare("SELECT idConcert, nomConcert AS nom, dia, nomGrup AS grup, nomGenere AS Genere, Sales.nom AS sala, Sales.ciutat AS ubicacio  FROM Concerts JOIN DataSala ON Concerts.idDataSala = DataSala.idDataSala JOIN GrupsMusicals ON Concerts.idGrup = GrupsMusicals.idGrup JOIN  Sales ON Concerts.idSala = Sales.idSala JOIN Generes ON Concerts.idGenere = Generes.idGenere and DataSala.dia > CURDATE();
        ");
        $stmt->execute();
        $concerts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $concerts;
    }

    public function getByConcertId($id)
    {
        $stmt = $this->pdo->prepare("
        SELECT * 
        FROM Concerts
        JOIN DataSala ON Concerts.idDataSala = DataSala.idDataSala
        JOIN GrupsMusicals ON Concerts.idGrup = GrupsMusicals.idGrup
        JOIN Sales ON Concerts.idSala = Sales.idSala
        JOIN Generes ON Concerts.idGenere = Generes.idGenere
        JOIN Entrades ON Concerts.idConcert = Entrades.idConcert
        WHERE Concerts.idConcert = ?
    ");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user;
    }

    public function createConcert($idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere)
    {
        /*
        // Obtenim la capacitat de la sala que serà les entrades disponibles del concert
        $stmt = $this->pdo->prepare("SELECT capacitat FROM Sales WHERE idSala = ?");
        $stmt->execute([$idSala]);
        $entrades_disponibles = $stmt->fetch(\PDO::FETCH_ASSOC)['capacitat'];

        // Creem el concert
        $sql = "INSERT INTO Concerts (idGrup, idSala, nomConcert, dia, hora, entrades_disponibles, preu, idGenere, idUsuari)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idGrup, $idSala, $nomConcert, $dia, $hora, $entrades_disponibles, $preu, $idGenere, $idUsuariOrganitzador]);
        $stmt = $this->pdo->query("SELECT LAST_INSERT_ID()");
        $idConcert = $stmt->fetchColumn();

        // Creem totes les entrades per aquest concert
        $placeholders = array_fill(0, $entrades_disponibles, "(?, ?, ?, ?)");
        $sql = "INSERT INTO EntradesConcert (idUsuari, idConcert, preu, idEstatEntrada) VALUES " . implode(", ", $placeholders);
        $stmt = $this->pdo->prepare($sql);
        $params = [];
        for ($i = 0; $i < $entrades_disponibles; $i++) {
            array_push($params, $idUsuariOrganitzador, $idConcert, $preu, 3); // 3 és Disponible
        }
        $stmt->execute($params);
        */

        //Lo de antes es el código que se había preparado. ..........
        //Como apaño por ahora uso esto:
        $stmt = $this->pdo->prepare("INSERT INTO Concerts (nomConcert, fecha, hora, lugar, grupo, precio, entradas_disponibles)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nomConcert, $dia, $hora, $idSala, $idGrup, $preu, 1000]);
        die();
    }

    // Nota: aquesta funció no actualitza les entrades disponibles del concert pq es complica la lògica per actualitzar les entrades
    //       però sí modifica el preu de totes les entrades disponibles d'aquest concert
    public function modificaConcert($idConcert, $idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere)
    {
        // Modifica el concert
        $sql = "UPDATE Concerts
                SET idGrup = ?, 
                idSala = ?, 
                nomConcert = ?, 
                dia = ?, 
                hora = ?, 
                preu = ?, 
                idGenere = ?, 
                idUsuari = ?
                WHERE idConcert = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere, $idUsuariOrganitzador, $idConcert]);

        // Obtenim el id del estat "Disponible"
        $stmt = $this->pdo->prepare("SELECT idEstatEntrada FROM EstatEntrada WHERE estat = 'Disponible'");
        $stmt->execute();
        $idEstatEntrada = $stmt->fetch(\PDO::FETCH_ASSOC)['idEstatEntrada'];

        // Actualitzem els preus de totes les entrades per aquest concert que encara no s'han venut ni reservat
        $sql = "UPDATE EntradesConcert SET preu = ? WHERE idConcert = ? AND idEstatEntrada = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$preu, $idConcert, $idEstatEntrada]);
    }


    public function guardaValoracio($idConcert, $puntuacio, $comentari)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO Valoracions (idConcert, puntuacio, comentari) 
            VALUES (?, ?, ?)"
        );
        $stmt->execute([$idConcert, $puntuacio, $comentari]);
        return true;
    }

    public function consultaImatge($img)
    {
        $rutaImg = trim($img);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Concerts WHERE imatgeURL = :ruta");
        $stmt->bindParam(':ruta', $rutaImg, \PDO::PARAM_STR);
        $stmt->execute();

        $existe = $stmt->fetchColumn();
        return $existe > 0;
    }
    
    public function guardaImatge($idConcert, $img)
    {
        if ($this->consultaImatge($img)) {
            echo "Ja existeix";
            return true;
        } else {
            echo "No existeix";
            return true;
        }

        $rutaImg = trim($img);

        $stmt = $this->pdo->prepare("UPDATE Concerts SET imatgeURL = ? WHERE idConcert = ?");
        $stmt->execute([$rutaImg, $idConcert]);

        if ($stmt->rowCount() == 0) {
            throw new Exception("No s'ha pogut actualitzar la imatge del concert.");
        }
        return true;
    }
    /*Nombre concierto
    Nombre grupo
    Num entradas
    Genero
    Sala*/
    public function concertFiltre($filtres = []) 
    {
        
    }
}
