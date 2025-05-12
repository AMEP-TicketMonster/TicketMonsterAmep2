<?php

namespace App\Controllers;

use App\Models\SalesGateway;
use App\Models\SalesSearcher;
use Core\Route;
use Core\Auth;
use Core\Session;

class SalesController
{
    private $searcher;

    public function __construct()
    {
        $this->searcher = new SalesSearcher();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    public function creaSala(string $nom, string $ciutat, int $capacitat) : int {
        $sala = new SalesGateway();
        return $sala->crea($nom, $ciutat, $capacitat);
    }

    public function modificaSala(string $nom, string $nouNom, string $novaCiutat, int $novaCapacitat) : void {
        $sala = $this->searcher->findByName($nom);
        if ($sala !== null) {
            $sala->modifica($nouNom, $novaCiutat, $novaCapacitat);
        } else {
            echo "No s'ha trobat cap sala amb nom " . $nom;
        }
    }   

    public function eliminaSala(string $nom) : void {
        $sala = $this->searcher->findByName($nom);
        if ($sala !== null) {
            $sala->elimina();
        } else {
            echo "No s'ha trobat cap sala amb nom " . $nom;
        }
    }

    public function consultaSala(int $idSala) : ?array {
        $sala = $this->searcher->findById($idSala);
        if ($sala !== null) {
            return $sala->consulta();
        } else {
            echo "No s'ha trobat cap sala amb id " . $idSala;
            return null;
        }
    }

}
