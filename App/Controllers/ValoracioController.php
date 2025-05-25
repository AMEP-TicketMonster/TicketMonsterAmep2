<?php

namespace App\Controllers;

use App\Models\ValoracioGateway;
use Core\Session;

class ValoracioController
{
    private $valoracioGateway;

    public function __construct()
    {
        $this->valoracioGateway = new ValoracioGateway();
        Session::sessionStart("ticketmonster_session");
    }

    public function crear()
    {
        $usuariId = $_SESSION['user']['idUsuari'] ?? null;
        if (!$usuariId) {
            $_SESSION['error'] = "No autenticat.";
            header("Location: /login");
            exit;
        }

        $tipus = $_POST['tipus'] ?? null;  // 'concert' o 'sala'
        $idObjecte = $_POST['id'] ?? null;
        $puntuacio = $_POST['puntuacio'] ?? null;
        $comentari = $_POST['comentari'] ?? '';

        if (!$tipus || !$idObjecte || !$puntuacio) {
            $_SESSION['error'] = "Falten camps obligatoris.";
            header("Location: /valoracio-form");
            exit;
        }

        $this->valoracioGateway->crearValoracio($usuariId, $tipus, $idObjecte, $puntuacio, $comentari);
        $_SESSION['missatge'] = "Valoració guardada correctament.";
        header("Location: /valoracions?tipus=$tipus&id=$idObjecte");
    }

    public function eliminar()
    {
        $usuariId = $_SESSION['user']['idUsuari'] ?? null;
        $tipus = $_POST['tipus'] ?? null;
        $idObjecte = $_POST['id'] ?? null;

        $this->valoracioGateway->eliminarValoracio($usuariId, $tipus, $idObjecte);
        header("Location: /valoracions?tipus=$tipus&id=$idObjecte");
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
}
