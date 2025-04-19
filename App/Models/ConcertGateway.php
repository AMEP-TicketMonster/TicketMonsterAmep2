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
        $concerts = $stmt->fetch();
        return $concerts;
    }

    public function getByConcertId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Concerts WHERE idConcert = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user;
    }

    public function createConcert($idGrup, $idSala, $nomConcert, $dia, $hora, $entrades_disponibles, $preu, $idGenere)
    {
        $sql = "INSERT INTO Concerts (idGrup, idSala, nomConcert, dia, hora, entrades_disponibles, preu, idGenere)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idGrup, $idSala, $nomConcert, $dia, $hora, $entrades_disponibles, $preu, $idGenere]);
    }
}
