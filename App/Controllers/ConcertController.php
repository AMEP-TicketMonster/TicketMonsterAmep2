<?php

namespace App\Controllers;

use App\Models\ConcertGateway;
use Core\Route;
use Core\Auth;
use Core\Session;

class ConcertController
{
    private $concertGateway;

    public function __construct()
    {
        $this->concertGateway = new ConcertGateway();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    public function mostraConcerts()
    {
        $concerts = $this->concertGateway->getConcertList();
        var_dump($concerts);
        die();
        //pasar a json y ya lo tratará el frontend.
        $_SESSION['concerts'] = json_encode($concerts);
    }


    public function createConcert($idGrup, $idSala, $nomConcert, $dia, $hora, $entrades_disponibles, $preu, $idGenere)
    {
        $this->concertGateway->creaConcert($idGrup, $idSala, $nomConcert, $dia, $hora, $entrades_disponibles, $preu, $idGenere);
    }    
}
