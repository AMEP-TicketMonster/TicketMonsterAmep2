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
        $filtres = ([
            'search' => '',
            'genere' => '',
            'sala' => '',
            'entradas' => ''
        ]);
        $this->concertGateway->concertFiltre($filtres);
    }

    public function carregaConcerts()
    {
        $concerts = $this->concertGateway->getConcertList();
        //pasar a json y ya lo tratará el frontend.
        $_SESSION['concerts'] = $concerts;
    }

    public function getDadesCreaConcerts()
    {
        /*
        cal carregar:
            - idSala, nomSala
            - idGenere, nomGenere
            - idGrupMusical, nomGrup
        */
        //implementar función getSalas
        //implementar función getGeneres
        //implementar función getGrups
        $sales = $this->concertGateway->getSalas();
        $generes = $this->concertGateway->getGeneres();
        $grups = $this->concertGateway->getGrupMusical();

        //$res = [$sales, $generes, $grups];
    
        $_SESSION['datosConcierto_Salas'] = json_encode($sales, JSON_UNESCAPED_UNICODE);
        $_SESSION['datosConciert_Genero'] = json_encode($generes, JSON_UNESCAPED_UNICODE);
        $_SESSION['datosConcierto_Grups'] = json_encode($grups, JSON_UNESCAPED_UNICODE);
  
        
        //$_SESSION['datos_concierto'] = $res;

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
        $horaIni = $_POST['hora-ini'];
        $horaFin = $_POST['hora-fi'];
        $horaIni = $_POST['hora-ini'];
        $horaFin = $_POST['hora-fi'];
        $preu = $_POST['precio'];
        $idGenere = $_POST['genero'];  

        $error = $this->concertGateway->validarParametrosCrearConcert($idGrup, $idSala, $nomConcert, $dia, $horaIni, $horaFin, $preu, $idGenere);
        if ($error) {
            /*
            $_SESSION['error_creacio_concert'] = $error;
            header("Location: /crear-concert");
            exit;
            */
            echo $error;
            die();
        }
        die();
        $this->createConcert($idUsuariOrganitzador, $idGrup, $idSala, $nomConcert, $dia, $horaIni, $horaFin, $preu, $idGenere);
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
        $grup = $_GET['grupo_musical'] ?? $_POST['grupo_musical'] ?? '';
        $entradas = $_GET['entradas'] ?? $_POST['entradas'] ?? '';

        var_dump([
            'search' => $search,
            'genere' => $genere,
            'sala' => $sala,
            'entradas' => $entradas,
            'grupMusical' => $grup
        ]);
        die();
    }
}
