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

    public function decrementarEntradesDisponiblesAssaig($idAssaig)
    {
        $stmt = $this->pdo->prepare("UPDATE Assajos SET entrades_disponibles = entrades_disponibles - 1 
                                     WHERE idAssajos = ? AND entrades_disponibles > 0");
        $stmt->execute([$idAssaig]);
    }
    
}
