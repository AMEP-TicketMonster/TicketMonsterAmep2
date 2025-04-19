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

    public function carregaConcerts()
    {
        $concerts = $this->concertGateway->getConcertList();
        //pasar a json y ya lo tratará el frontend.
        $_SESSION['concerts'] = $concerts;
    }

    public function showConcert($id)
    {
        $concert = $this->concertGateway->getByConcertId($id);

        $_SESSION['concert'] = $concert;
        setcookie('concert_id', $id, time() + 3600, '/');
        //header("Location: /concierto");
    }
}   
