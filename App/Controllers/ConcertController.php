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
        $id = 21;
        $img = "../../public/img/default.png";
        $this->concertGateway->guardaImatge($id, $img);
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
        
        $valoracions = $this->concertGateway->obtenerValoracionesPorConcierto($id);
        $mitjana = 0;
        if (count($valoracions) > 0) {
            $total = array_sum(array_column($valoracions, 'puntuacio'));
            $mitjana = $total / count($valoracions);
        }

        $_SESSION['concert'] = $concert;
        $_SESSION['valoracions'] = $valoracions;
        $_SESSION['mitjana_valoracio'] = round($mitjana, 1);
        setcookie('concert_id', $id, time() + 3600, '/');
    }



   /* public function showConcert($id)
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
            $_SESSION['error_creacio_concert'] = $error;
            header("Location: /crea-concert");
            exit;
        }
        echo "finish";
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

  public function valorar()
{
    $usuario_id = $_SESSION['user']['idUsuari'] ?? null;
    if (!$usuario_id) {
        $_SESSION['mensaje'] = "Debes iniciar sesión para valorar.";
        header("Location: /login");
        exit;
    }

    $concert_id = $_POST['concierto_id'] ?? null;
    $puntuacion = $_POST['puntuacion'] ?? null;
    $comentario = $_POST['comentario'] ?? '';

    if (!$concert_id || !$puntuacion || $puntuacion < 1 || $puntuacion > 5) {
        $_SESSION['mensaje'] = "Valoración inválida.";
        header("Location: /concierto?id=$concert_id");
        exit;
    }

    // Verificar si tiene entrada
    $entradaGateway = new \App\Models\EntradaGateway();
    $tieneEntrada = $entradaGateway->usuarioTieneEntrada($usuario_id, $concert_id);

    if (!$tieneEntrada) {
        $_SESSION['mensaje'] = "No puedes valorar un concierto que no has comprado.";
        header("Location: /concierto?id=$concert_id");
        exit;
    }

    $this->concertGateway->guardarValoracion($usuario_id, $concert_id, $puntuacion, $comentario);

    $_SESSION['mensaje'] = "¡Gracias por tu valoración!";
    header("Location: /concierto?id=$concert_id");
}
public function mostrarTodasValoraciones()
{
    $valoraciones = $this->concertGateway->obtenerTodasLasValoraciones();
    $_SESSION['valoraciones_globales'] = $valoraciones;
}



}


