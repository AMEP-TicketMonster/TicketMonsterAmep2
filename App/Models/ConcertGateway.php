<?php

namespace App\Models;

use App\Config\Database;

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
        $stmt = $this->pdo->prepare("SELECT * FROM Concerts WHERE data > CURDATE()");
        $stmt->execute();
        $concerts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $concerts;
        
    }

    public function getByConcertId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Concerts WHERE idConcert = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user;
    }

    public function createConcert($idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere)
    {
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

    // Guardar la valoració d'un concert
    public function guardaValoracio($idConcert, $puntuacio, $comentari)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO Valoracio (idConcert, puntuacio, comentari) 
            VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$idConcert, $puntuacio, $comentari]);
        return true;
    }

}
