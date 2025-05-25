<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class GrupMusicalGateway
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    // Obtener grupo por nombre
    public function getByNomGrup($nomGrup)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM GrupsMusicals WHERE nomGrup = ?");
        $stmt->execute([$nomGrup]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener grupo por ID
    public function getByIdGrup($idGrup)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM GrupsMusicals WHERE idGrup = ?");
        $stmt->execute([$idGrup]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo grupo
    public function createGrup($nomGrup, $dataCreacio, $descripcio)
    {
        $stmt = $this->pdo->prepare("INSERT INTO GrupsMusicals (nomGrup, dataCreacio, descripcio) VALUES (?, ?, ?)");
        $stmt->execute([$nomGrup, $dataCreacio, $descripcio]);
        return $this->pdo->lastInsertId(); // Devuelve el ID del nuevo grupo
    }

    // Actualizar grupo
    public function updateGrup($idGrup, $nomGrup, $dataCreacio, $descripcio)
    {
        $stmt = $this->pdo->prepare("UPDATE GrupsMusicals SET nomGrup = ?, dataCreacio = ?, descripcio = ? WHERE idGrup = ?");
        return $stmt->execute([$nomGrup, $dataCreacio, $descripcio, $idGrup]);
    }

    // Eliminar grupo
    public function delete($idGrup)
    {
        $stmt = $this->pdo->prepare("DELETE FROM GrupsMusicals WHERE idGrup = ?");
        return $stmt->execute([$idGrup]);
    }


        // Obtener todos los grupos musicales
    public function getAllGrups()
    {
        $stmt = $this->pdo->query("SELECT * FROM GrupsMusicals ORDER BY nomGrup ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
