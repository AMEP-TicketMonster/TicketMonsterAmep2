<?php

namespace App\Controllers;

use App\Models\ConcertGateway;
use Core\Route;
use Core\Auth;
use Core\Session;
use App\Models\EntradaGateway;
use App\Models\SalesGateway;
use App\Models\ValoracioGateway;

class ConcertController
{
    private $concertGateway;
    private $entradaGateway;
    private $salesGateway;
    private $valoracioGateway;

    public function __construct()
    {
        $this->concertGateway = new ConcertGateway();
        $this->entradaGateway = new EntradaGateway();
        $this->salesGateway = new SalesGateway();
        $this->valoracioGateway = new valoracioGateway();

        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    public function pruebas()
    {
        $idConcert = 23;
        $quantitat = 10;
        $preu = 20.0;
        $this->entradaGateway->crearEntradesPerConcert($idConcert, $quantitat, $preu);
    }

    public function carregaConcerts()
    {
        $concerts = $this->concertGateway->getConcertList();
        //pasar a json y ya lo tratará el frontend.
        $_SESSION['concerts'] = $concerts;
    }

    public function carregaConcertsFiltered($ids)
    {
        $concerts = $this->concertGateway->getConcertListFiltered($ids);
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
        $sales = $this->salesGateway->getSalas();
        $generes = $this->concertGateway->getGeneres();
        //hay que incluir el grupMusical
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
        $entradaGateway = new EntradaGateway();
        $entradaDisponible = $entradaGateway->getEntradaDisponiblePorConcert($id);

        $valoracions = $this->valoracioGateway->obtenirValoracionsGrup($id);

        if ($entradaDisponible) {
            $concert['idEntrada'] = $entradaDisponible['idEntrada'];
        }

        $_SESSION['concert'] = $concert;
        $_SESSION['valoracions'] = $valoracions;
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
        $preu = $_POST['precio'];
        $idGenere = $_POST['genero'];

        $error = $this->concertGateway->validarParametrosCrearConcert($idGrup, $idSala, $nomConcert, $dia, $horaIni, $horaFin, $preu, $idGenere);
        if ($error) {
            $_SESSION['error_creacio_concert'] = $error;
            header("Location: /crea-concert");
            exit;
        }
        $idDataSala = (int)$this->salesGateway->reservaSalaConcert($idSala, $horaIni, $horaFin, $dia);
        $aforamentSala = (int)$this->salesGateway->getAforamentSala($idSala)[0]['capacitat'];
        //var_dump((int)$idDataSala);
        //die();
        //var_dump($idDataSala, $aforamentSala);      
        $idConcert = (int)$this->concertGateway->createConcert($idGrup, $idSala, $nomConcert, $idGenere, $idDataSala, $aforamentSala);
        $this->entradaGateway->crearEntradesPerConcert($idConcert, $aforamentSala, $preu);

        header("location: /conciertos");
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
        if ($search != '' or $genere != '' or $sala != '' or $entradas != '') {
            $res = $this->concertGateway->concertFiltre([
                'search' => $search,
                'genere' => $genere,
                'sala' => $sala,
                'entradas' => $entradas
            ]);
            $ids = [];
            $i = 0;
            foreach ($res as $elem) {
                $ids[$i] = $elem['idConcert'];
                $i++;
            }
            $this->carregaConcertsFiltered($ids);
            // $_SESSION['concerts'] = "";
        }

        /*  var_dump([
            'search' => $search,
            'genere' => $genere,
            'sala' => $sala,
            'entradas' => $entradas,
            'grupMusical' => $grup
        ]);
        die();
        */
    }
}
