<?php

namespace App\Controllers;

use App\Models\ConcertGateway;
use App\Models\ConcertSearcher;
use App\Models\DataSalaGateway;
use Core\Route;
use Core\Auth;
use Core\Session;

class ConcertController
{
    private $searcher;

    public function __construct()
    {
        $this->searcher = new ConcertSearcher();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    // TODO: borrar cuando se borre del frontend también
    public function pruebas(){
        $idConcert = 1;
        $puntuacio = 5;
        $comentari = "El concert ha estat genial!";
        $this->concertGateway->guardaValoracio($idConcert, $puntuacio, $comentari);
    }   

    public function carregaConcerts()
    {
        $concerts = $this->searcher->findAll();
        //pasar a json y ya lo tratará el frontend.
        $_SESSION['concerts'] = $concerts;
    }

    public function showConcert($id)
    {
        $concert = $this->searcher->findByConcertId($id);
        $_SESSION['concert'] = $concert;
        setcookie('concert_id', $id, time() + 3600, '/');
        //header("Location: /concierto");
    }

    // Aquest mètode crea tantes entrades disponibles com capacitat té la sala
    public function createConcert($idGrup, $idSala, $nomConcert, $dia, $horaInici, $horaFi, $preu, $idGenere)
    {
        $dataSala = new DataSalaGateway();
        $idDatasala = $dataSala->create($dia, $horaInici, $horaFi, $idSala);
        if ($idDatasala !== null) {
            $concerts = new ConcertGateway();
            return $concerts->createConcert($idGrup, $idSala, $nomConcert, $idDatasala, $preu, $idGenere);
        }
        return null;       
    }

}
