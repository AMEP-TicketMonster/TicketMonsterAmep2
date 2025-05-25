<?php

namespace App\Controllers;

use App\Models\ValoracioGateway;
use Core\Session;
use App\Core\Auth;

class ValoracioController
{
    private $valoracioGateway;

    public function __construct()
    {
        $this->valoracioGateway = new ValoracioGateway();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    public function crear()
    {
        $usuariId = $_SESSION['user']['idUsuari'] ?? null;
        if (!$usuariId) {
            $_SESSION['error'] = "No autenticat.";
            header("Location: /login");
            exit;
        }

        // $tipus = $_POST['tipus'] ?? null;  // 'concert' o 'sala'
        $idConcert = $_POST['idConcert'] ?? null;
        $puntuacio = $_POST['puntuacion'] ?? null;
        $comentari = $_POST['comentario'] ?? '';

        if (!$idConcert || !$puntuacio) {
            $_SESSION['error'] = "Falten camps obligatoris.";
            header("Location: /dashboard");
            exit;
        }

        $this->valoracioGateway->crearValoracio($usuariId, $idConcert, $comentari, $puntuacio);
       // $_SESSION['missatge'] = "Valoració guardada correctament.";
        //header("Location: /valoracions?tipus=$tipus&id=$idObjecte");
        header('location: /dashboard');
    }

    public function eliminar($idValoracio)
    {

        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;
        //comprova si la valoració pertany a aquest usuari o qui ho está intentant eliminar es admin
        $id = $this->valoracioGateway->getIdUsuariValoracio($idValoracio)[0]['idUsuariClient'];
        if ($idUsuari == $id or Auth::isAdmin()) {

            $this->valoracioGateway->eliminarValoracio($idUsuari, $idValoracio);
        }
        header('location: /conciertos');


        // header("Location: /valoracions?tipus=$tipus&id=$idObjecte");
    }

    public function consultar()
    {
        $tipus = $_GET['tipus'] ?? null;
        $idObjecte = $_GET['id'] ?? null;

        $valoracions = $this->valoracioGateway->obtenirValoracions($tipus, $idObjecte);
        $_SESSION['valoracions'] = $valoracions;

        // Carregar la vista corresponent
        if ($tipus === 'concert') {
            require __DIR__ . "/../../Views/concerts/details.php";
        } elseif ($tipus === 'sala') {
            require __DIR__ . "/../../Views/sales/details.php";
        } else {
            echo "Tipus desconegut";
        }
    }

    public function consultar_valoracions_concert($idConcert)
    {
        $valoracions = $this->valoracioGateway->obtenirValoracionsGrup($idConcert);
      
        $_SESSION['valoracions'] = $valoracions;
    }
}
