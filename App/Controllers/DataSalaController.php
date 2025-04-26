<?php

namespace App\Controllers;

use App\Models\DataSalaGateway;
use App\Models\DataSalaSearcher;
use Core\Route;
use Core\Auth;
use Core\Session;

class DataSalaController
{
    private $searcher;

    public function __construct()
    {
        $this->searcher = new DataSalaSearcher();
        // fa falta??
        // if (session_status() === PHP_SESSION_NONE) {
        //     Session::sessionStart("ticketmonster_session");
        // }
    }

    public function createDataSala($dia, $horaInici, $horaFi, $idSala) : int {
        $dataSala = new DataSalaGateway();
        return $dataSala->create($dia, $horaInici, $horaFi, $idSala);
    }
    
}
