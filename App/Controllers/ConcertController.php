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

    public function pruebas()
    {
        $idConcert = 20;
        $img = "../../public/img/feria_abril.jpg";
        $this->concertGateway->guardaImatge($idConcert, $img);
    }

    public function carregaConcerts()
    {
        $concerts = $this->concertGateway->getConcertList();
        //pasar a json y ya lo tratará el frontend.
        $_SESSION['concerts'] = $concerts;
    }

    public function dadesConcerts(){
        /*
        cal carregar:
            - idSala, nomSala
            - idGenere, nomGenere
            - idGrupMusical, nomGrup
        */
        //implementar función getSalas
        //implementar función getGeneres
        //implementar función getGrups

        
        $_SESSION['datos_concierto'] = $res;

    }

    public function showConcert($id)
    {
        $concert = $this->concertGateway->getByConcertId($id);

        // Buscar una entrada disponible para ese concierto
        $entradaGateway = new \App\Models\EntradaGateway();
        $entradaDisponible = $entradaGateway->getEntradaDisponiblePorConcert($id);

        if ($entradaDisponible) {
            $concert['idEntrada'] = $entradaDisponible['idEntrada'];
        }

        $_SESSION['concert'] = $concert;
        setcookie('concert_id', $id, time() + 3600, '/');
    }

    /*public function showConcert($id)
    {
        $concert = $this->concertGateway->getByConcertId($id);

        $_SESSION['concert'] = $concert;
        setcookie('concert_id', $id, time() + 3600, '/');
        //header("Location: /concierto");
    }*/
    public function creaConcert()
    {
        //habría que hacer ciertas comprobaciones sobre los datos.
        $idUsuariOrganitzador = $_SESSION['user']['idUsuari'];
        $idGrup = $_POST['grupo_musical'];
        $idSala = $_POST['lugar'];
        $nomConcert = $_POST['nombre_concierto'];
        $dia = $_POST['fecha'];
        $hora = $_POST['hora'];
        $preu = $_POST['precio'];
        $idGenere = $_POST['genero'];
        //falta por coger el campo entradas disponibles
        $this->createConcert($idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere);
    }
    // Aquest mètode crea tantes entrades disponibles com capacitat té la sala
    public function createConcert($idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere)
    {

        $this->concertGateway->createConcert($idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere);
    }

    // Aquest mètode actualitza també el preu de totes les entrades disponibles d'aquest concert
    public function modificaConcert($idConcert, $idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $hora, $preu, $idGenere)
    {
        $this->concertGateway->modificaConcert(
            $idConcert,
            $idUsuariOrganitzador,
            $idGrup,
            $idSala,
            $nomConcert,
            $dia,
            $hora,
            $preu,
            $idGenere
        );
    }



    public function filtroConciertos()
    {
        // Filtros por la URL
        $search = $_GET['search'] ?? $_POST['search'] ?? '';
        $genere = $_GET['genere'] ?? $_POST['genere'] ?? '';
        $sala = $_GET['sala'] ?? $_POST['sala'] ?? '';
        $entradas = $_GET['entradas'] ?? $_POST['entradas'] ?? '';

        var_dump([
            'search' => $search,
            'genere' => $genere,
            'sala' => $sala,
            'entradas' => $entradas,
        ]);
        die();
    }
}
