<?php

namespace App\Models;

use App\Config\Database;

class ValoracioGateway
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    // Crear valoració
    public function crearValoracio($idUsuari, $idConcert, $comentari, $puntuacio)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO Valoracions (idUsuariClient, idConcert, puntuacio, comentari, data)
            VALUES (?, ?, ?, ?, CURDATE())
        ");
        $stmt->execute([$idUsuari, $idConcert, $puntuacio, $comentari]);

        // ON DUPLICATE KEY UPDATE puntuacio = VALUES(puntuacio), comentari = VALUES(comentari), data = CURDATE()
        header("location:/dashboard");
    }

    // Eliminar valoració
    public function eliminarValoracio($idUsuari, $idValoracio)
    {

        $stmt = $this->pdo->prepare("DELETE FROM Valoracions WHERE idValoracio = ? and idUsuariClient = ? ");

        return $stmt->execute([(int)$idValoracio, (int)$idUsuari]);
    }

    public function getIdUsuariValoracio($idValoracio)
    {
        $stmt = $this->pdo->prepare("SELECT idUsuariClient FROM Valoracions WHERE idValoracio = ? ");
        $stmt->execute([$idValoracio]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Consultar totes les valoracions d’un concert o sala
    public function obtenirValoracions($tipus, $idObjecte)
    {
        $stmt = $this->pdo->prepare("
            SELECT V.puntuacio, V.comentari, V.data, U.nom AS nom_usuari
            FROM Valoracions V
            JOIN Usuaris U ON V.idUsuariClient = U.idUsuari
            WHERE V.tipus = ? AND V.idObjecte = ?
            ORDER BY V.data DESC
        ");
        $stmt->execute([$tipus, $idObjecte]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenirValoracionsConcert($idConcert)
    {
        $stmt = $this->pdo->prepare("
           SELECT Valoracions.*, Usuaris.nom, Usuaris.cognom
        FROM 
            Valoracions
        JOIN 
            Usuaris ON Valoracions.idUsuariClient = Usuaris.idUsuari
            WHERE Valoracions.idConcert = ?
            ORDER BY Valoracions.data DESC
        ");
        $stmt->execute([$idConcert]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenirValoracionsGrup($idConcert)
    {
        $stmt = $this->pdo->prepare("

   SELECT 
    V.*, 
    U.nom, 
    U.cognom,
    C.idGrup
FROM 
    Valoracions V
JOIN 
    Usuaris U ON V.idUsuariClient = U.idUsuari
        
    JOIN 
    Concerts C ON V.idConcert = C.idConcert
WHERE 
    C.idGrup = (SELECT idGrup FROM Concerts WHERE idConcert = ?)

ORDER BY 
    V.data DESC;

        ");

        $stmt->execute([$idConcert]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Verificar si ja ha valorat
    public function haValorat($idUsuari, $tipus, $idObjecte)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Valoracions WHERE idUsuariClient = ? AND tipus = ? AND idObjecte = ?");
        $stmt->execute([$idUsuari, $tipus, $idObjecte]);
        return $stmt->fetchColumn() > 0;
    }
}
