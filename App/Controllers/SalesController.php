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
        // fa falta??
        // if (session_status() === PHP_SESSION_NONE) {
        //     Session::sessionStart("ticketmonster_session");
        // }
    }

    public function getCapacitat(int $idSala): int {
        $sales = $this->searcher->findById($idSala);
        if ($sales !== null) {
            echo "Capacitat de la sala " . $idSala . ": " . $sales->getCapacitat();
            return $sales->getCapacitat();
        } else {
            echo "No s'ha trobat cap sala amb ID " . $idSala;
            return -1;
        }
    }

    
}
