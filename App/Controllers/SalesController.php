<?php

namespace App\Controllers;

use App\Models\UserGateway;


//Implementar SalesGateway, gracias!
use App\Models\SalesGateway;



use Core\Route;
use Core\Auth;
use Core\Session;


class SalesController
{
    private SalesGateway $salesGateway;

    public function __construct()
    {
        $this->salesGateway = new SalesGateway();
        if (session_status() === PHP_SESSION_NONE)
        {
            Session::sessionStart("ticketmonster_session");
        }
    }

    public function crearReserva()
    {
        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;
        $idSala = $_POST['idSala'] ?? null;
        $idDataSala = $_POST['idDataSala'] ?? null;

        if (!$idUsuari || !$idSala || !$idDataSala)
        {
            echo "Error: Dades de reserva incompletes.";
            return;
        }

        if (!$this->salesGateway->existeSala($idSala))
        {
            echo "Error: La sala no existeix.";
            return;
        }

        if (!$this->salesGateway->existeFranjaHoraria($idDataSala, $idSala))
        {
            echo "Error: Franja hor�ria no v�lida per aquesta sala.";
            return;
        }

        if (!$this->salesGateway->hiHaDisponibilitat($idSala, $idDataSala))
        {
            echo "Error: No hi ha disponibilitat per aquesta sala en aquest horari.";
            return;
        }

        $reservaExitosa = $this->salesGateway->crearReserva($idUsuari, $idSala, $idDataSala);

        if ($reservaExitosa)
        {
            echo "Reserva creada correctament.";
        }
        else
        {
            echo "Error al crear la reserva.";
        }
    }

    public function consultarReserva()
    {
        $idSala = $_GET['idSala'] ?? null;
        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;

        if ($idSala)
        {
            $reserves = $this->salesGateway->getReservesSala($idSala);

            if (empty($reserves))
            {
                echo "No hi ha reserves per aquesta sala.";
                return;
            }

            foreach ($reserves as $r)
            {
                echo "Usuari: {$r['idGrup']} | Dia: {$r['dia']} | Inici: {$r['hora_inici']} | Fi: {$r['hora_fi']}<br>";
            }
        }
        elseif ($idUsuari)
        {
            $reserves = $this->salesGateway->getReservesUsuari($idUsuari);

            if (empty($reserves))
            {
                echo "No tens reserves actualment.";
                return;
            }

            foreach ($reserves as $r)
            {
                echo "Sala: {$r['nom']} ({$r['ciutat']}) | Dia: {$r['dia']} | Inici: {$r['hora_inici']} | Fi: {$r['hora_fi']}<br>";
            }
        }
        else
        {
            echo "Error: No s'ha proporcionat sala ni hi ha sessi� d'usuari.";
        }
    }

    public function modificarReserva()
    {
        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;
        $idAssaig = $_POST['idAssaig'] ?? null;
        $nouIdSala = $_POST['nouIdSala'] ?? null;
        $nouIdDataSala = $_POST['nouIdDataSala'] ?? null;

        if (!$idUsuari || !$idAssaig || !$nouIdSala || !$nouIdDataSala)
        {
            echo "Error: Falten dades per modificar la reserva.";
            return;
        }

        if (!$this->salesGateway->esReservaUsuari($idUsuari, $idAssaig))
        {
            echo "Error: No tens permisos per modificar aquesta reserva.";
            return;
        }

        if (
            !$this->salesGateway->existeSala($nouIdSala) ||
            !$this->salesGateway->existeFranjaHoraria($nouIdDataSala, $nouIdSala)
        )
        {
            echo "Error: Nova sala o franja hor�ria no v�lida.";
            return;
        }

        if (!$this->salesGateway->hiHaDisponibilitat($nouIdSala, $nouIdDataSala))
        {
            echo "Error: No hi ha disponibilitat en la nova franja.";
            return;
        }

        $resultat = $this->salesGateway->actualitzarReserva($idAssaig, $nouIdSala, $nouIdDataSala);

        if ($resultat)
        {
            echo "Reserva modificada correctament.";
        }
        else
        {
            echo "Error al modificar la reserva.";
        }
    }

    public function cancelReserva()
    {
        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;
        $idAssaig = $_POST['idAssaig'] ?? null;

        if (!$idUsuari || !$idAssaig)
        {
            echo "Error: Falten dades per cancel�lar la reserva.";
            return;
        }

        if (!$this->salesGateway->esReservaUsuari($idUsuari, $idAssaig))
        {
            echo "Error: No tens permisos per cancel�lar aquesta reserva.";
            return;
        }

        $resultat = $this->salesGateway->eliminarReserva($idAssaig);

        if ($resultat)
        {
            echo "Reserva cancel�lada correctament.";
        }
        else
        {
            echo "Error al cancel�lar la reserva.";
        }
    }
    
}
