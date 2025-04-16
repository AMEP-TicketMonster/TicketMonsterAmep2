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
    public function getByEntradaId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM EntradesAssaig WHERE idEntrada = ?");
        $stmt->execute([$id]);
        return  $stmt->fetch();
    }
}
