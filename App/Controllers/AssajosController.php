<?php

namespace App\Controllers;

use App\Models\AssajosGateway;
use App\Models\AssajosSearcher;
use Core\Route;
use Core\Auth;
use Core\Session;

class AssajosController
{
    private $searcher;

    public function __construct()
    {
        $this->searcher = new AssajosSearcher();
        // fa falta??
        // if (session_status() === PHP_SESSION_NONE) {
        //     Session::sessionStart("ticketmonster_session");
        // }
    }

    public function getEntradesDisponibles(int $idAssajos): int {
        $assajos = $this->searcher->findByAssajosId($idAssajos);
        if ($assajos !== null) {
            echo "Entrades disponibles per l'assaig ID " . $idAssajos . ": " . $assajos->getEntradesDisponibles();
            return $assajos->getEntradesDisponibles();
        } else {
            echo "No s'ha trobat cap assaig amb ID " . $idAssajos;
            return -1;
        }
    }
}
