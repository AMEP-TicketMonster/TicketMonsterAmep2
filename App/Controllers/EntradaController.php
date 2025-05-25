<?php

namespace App\Controllers;

use App\Models\EntradaGateway;
use App\Models\UserGateway;
use App\Controllers\UserController;
use Core\Route;
use Core\Auth;
use Core\Session;

class EntradaController
{
    private string $id;
    private float $preu;
    private string $estat;
    private $entradaGateway;
    private $usuariGateway;
    private $userController;

    public function __construct()
    {
        $this->entradaGateway = new EntradaGateway();
        $this->usuariGateway = new UserGateway();
        $this->userController = new UserController();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    public function consultarEntrades()
    {
        $idUsuari = $_SESSION['user']['idUsuari'];
        $entrades = $this->entradaGateway->getEntradesComprades($idUsuari);
        $_SESSION["entrades_usuari"] = $entrades;
    }


    /**
     * Compra una entrada assaig si est� disponible y el usuario tiene saldo suficiente.
     */
    public function comprarEntradaAssaig()
    {
        $errors = [];

        $idUsuari = $this->usuariGateway->getId();
        $idEntrada = $_POST['idEntrada'] ?? null;

        if (!$idUsuari || !$idEntrada) {
            $errors[] = "Dades d'usuari o entrada no vàlides";
        }

        $entrada = $this->entradaGateway->getEntradaAssaigById($idEntrada);
        if (!$entrada) {
            $errors[] = "Entrada no trobada";
        }

        if ($entrada) {
            $estatEntrada = $this->entradaGateway->getStringFromEntradaId($entrada['idEstatEntrada']);
            if ($estatEntrada !== "Disponible") {
                $errors[] = "Entrada no disponible";
            }

            $preu = $entrada['preu'];
            $idAssaig = $entrada['idAssaig'];
        }

        $usuari = $this->usuariGateway->getByUserId($idUsuari);
        if (!$usuari) {
            $errors[] = "Usuari no trobat";
        }

        if (isset($preu, $usuari['saldo']) && $usuari['saldo'] < $preu) {
            $errors[] = "Saldo insuficient";
        }

        if (empty($errors)) {
            $nouSaldo = $usuari['saldo'] - $preu;
            $this->usuariGateway->actualizarSaldo($idUsuari, $nouSaldo);
            $this->entradaGateway->assignarEntradaAssaig($idEntrada, $idUsuari, "Comprada");
            $this->entradaGateway->decrementarEntradesDisponiblesAssaig($idAssaig);
            $saldo = new UserController();
            $saldo->actualitzaSaldo();
        } else {
            $_SESSION['errorCompraEntrades'] = json_encode(['errors' => $errors]);
        }
    }


    /**
     * Compra una entrada concert si est� disponible y el usuario tiene saldo suficiente.
     */

    public function comprarEntradaConcert(): void
    {
        $errors = [];

        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;
        $idEntrada = $_POST['idEntrada'] ?? null;
        $concertId = $_COOKIE['concert_id'] ?? '';

        if (!$idUsuari || !$idEntrada) {
            $errors[] = "Falten dades per completar la compra.";
        }

        $entrada = $this->entradaGateway->getEntradaConcertById($idEntrada);
        if (!$entrada) {
            $errors[] = "La entrada no existeix.";
        }

        if ($entrada) {
            $estatEntrada = $this->entradaGateway->getStringFromEntradaId($entrada['idEstatEntrada']);
            if ($estatEntrada !== "Disponible") {
                $errors[] = "La entrada no està disponible.";
            }

            $preu = $entrada['preu'];
            $idConcert = $entrada['idConcert'];
        }

        $usuari = $this->usuariGateway->getByUserId($idUsuari);
        if (!$usuari) {
            $errors[] = "Usuari no trobat.";
        }

        if (isset($preu, $usuari['saldo']) && $usuari['saldo'] < $preu) {
            $errors[] = "Saldo insuficient.";
        }

        if (empty($errors)) {
            $nouSaldo = $usuari['saldo'] - $preu;
            $this->usuariGateway->actualizarSaldo($idUsuari, $nouSaldo);
            $this->entradaGateway->assignarEntradaConcert($idEntrada, $idUsuari, "Comprada");
            $this->entradaGateway->decrementarEntradesDisponiblesConcert($idConcert);
            $this->userController->actualitzaSaldo();
            $_SESSION['mensaje'] = "Compra realitzada amb èxit.";
        } else {
            $_SESSION['mensaje'] = implode(" ", $errors);
        }

        header("Location: /concierto?id=$concertId");
        exit();
    }

    /*
    public function comprarEntradaConcert()
    {
        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;
        $idEntrada = $_POST['idEntrada'] ?? null; // ✅ aquí estaba el fallo (antes usabas idConcert)

        if (!$idUsuari || !$idEntrada) {
            $_SESSION['mensaje'] = "Falten dades per completar la compra.";
            header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
            exit();
        }

        $entrada = $this->entradaGateway->getEntradaConcertById($idEntrada);

        if (!$entrada) {
            $_SESSION['mensaje'] = "La entrada no existeix.";
            header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
            exit();
        }

        $estatEntrada = $this->entradaGateway->getStringFromEntradaId($entrada['idEstatEntrada']);
        if ($estatEntrada !== "Disponible") {
            $_SESSION['mensaje'] = "La entrada no está disponible.";
            header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
            exit();
        }

        $saldo = $this->usuariGateway->getByUserId($idUsuari)['saldo'];
        $preu = $entrada['preu'];

        if ($saldo < $preu) {
            $_SESSION['mensaje'] = "Saldo insuficient.";
            header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
            exit();
        }

        $nouSaldo = $saldo - $preu;
        $this->usuariGateway->actualizarSaldo($idUsuari, $nouSaldo);
        $this->entradaGateway->assignarEntradaConcert($idEntrada, $idUsuari, "Comprada");
        $this->entradaGateway->decrementarEntradesDisponiblesConcert($entrada['idConcert']);

        $_SESSION['mensaje'] = "Compra realizada con éxito.";
        header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
        exit();
    }
*/



    /**
     * Reserva una entrada disponible.
     * M�todos necesarios en EntradaGateway:
     * - getEntradaAssaigById
     * - getStringFromEntradaId
     * - assignarEntradaAssaig
     * - decrementarEntradesDisponiblesAssaig
     */
    public function reservarEntradaAssaig()
    {
        // TODO: creo que deberíamos tener un idEntradaConcert y un idEntradaAssaig
        //       no he podido probar estas dos _SESSION y _POST pq creo que no está cableado todavía
        //       pero he probado el resto de la función poniendo valores válidos en $idUsuari y $idEntrada
        $idUsuari = $_SESSION['user']['id'] ?? null;
        $idEntrada = $_POST['idEntrada'] ?? null;

        if (!$idUsuari || !$idEntrada) {
            echo "Error: Falten dades necessaris per realitzar la reserva.";
            return;
        }

        $entrada = $this->entradaGateway->getEntradaAssaigById($idEntrada);

        if (!$entrada) {
            echo "Error: La entrada no existeix.";
            return;
        }
        $estatEntrada = $this->entradaGateway->getStringFromEntradaId($entrada['idEstatEntrada']);

        if ($estatEntrada !== "Disponible") {
            echo "Error: La entrada ja est� reservada o comprada.";
            return;
        }

        $this->entradaGateway->assignarEntradaAssaig($idEntrada, $idUsuari, "Reservada");

        $idAssaig = $entrada['idAssaig'];
        $this->entradaGateway->decrementarEntradesDisponiblesAssaig($idAssaig);

        echo "Reserva realitzada amb �xit.";
    }

    /**
     * Reserva una entrada disponible.
     * M�todos necesarios en EntradaGateway:
     * - getEntradaConcertById
     * - getStringFromEntradaId
     * - assignarEntradaConcert
     * - decrementarEntradesDisponiblesConcert
     */

    public function reservarEntradaConcert()
    {
        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;
        $idEntrada = $_POST['idEntrada'] ?? null;

        if (!$idUsuari || !$idEntrada) {
            $_SESSION['mensaje'] = "Falten dades per realitzar la reserva.";
            header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
            exit();
        }

        $entrada = $this->entradaGateway->getEntradaConcertById($idEntrada);

        if (!$entrada) {
            $_SESSION['mensaje'] = "La entrada no existeix.";
            header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
            exit();
        }

        $estat = $this->entradaGateway->getStringFromEntradaId($entrada['idEstatEntrada']);
        if ($estat !== "Disponible") {
            $_SESSION['mensaje'] = "La entrada ja no està disponible.";
            header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
            exit();
        }

        $this->entradaGateway->assignarEntradaConcert($idEntrada, $idUsuari, "Reservada");
        $this->entradaGateway->decrementarEntradesDisponiblesConcert($entrada['idConcert']);

        $_SESSION['mensaje'] = "Reserva realitzada amb èxit.";
        header("Location: /concierto?id=" . ($_COOKIE['concert_id'] ?? ''));
        exit();
    }

    /*public function reservarEntradaConcert()
    {
        // TODO: creo que deberíamos tener un idEntradaConcert y un idEntradaAssaig
        //       no he podido probar estas dos _SESSION y _POST pq creo que no está cableado todavía
        //       pero he probado el resto de la función poniendo valores válidos en $idUsuari y $idEntrada
        $idUsuari = $_SESSION['user']['id'] ?? null;
        $idEntrada = $_POST['idEntrada'] ?? null;

        if (!$idUsuari || !$idEntrada) {
            echo "Error: Falten dades necessaris per realitzar la reserva.";
            return;
        }

        $entrada = $this->entradaGateway->getEntradaConcertById($idEntrada);

        if (!$entrada) {
            echo "Error: La entrada no existeix.";
            return;
        }
        $estatEntrada = $this->entradaGateway->getStringFromEntradaId($entrada['idEstatEntrada']);

        if ($estatEntrada !== "Disponible") {
            echo "Error: La entrada ja est� reservada o comprada.";
            return;
        }

        $this->entradaGateway->assignarEntradaConcert($idEntrada, $idUsuari, "Reservada");

        $idConcert = $entrada['idConcert'];
        $this->entradaGateway->decrementarEntradesDisponiblesConcert($idConcert);

        echo "Reserva realitzada amb �xit.";
    }*/

    /**
     * Consulta todas las entradas.
     *  M�todo necesario en EntradaGateway:
     * - getAllEntrades()
     */
    //deberíamos hacer una función que muestre todas las entradas que tiene compradas un usuario?
    public function consultarEntradesAssaig()
    {
        $entrades = $this->entradaGateway->getAllEntradesAssaig();

        var_dump($entrades); // TODO: debe haber algun error de encoding UTF8 en $entrades
        //       si no hago el var_dump json_encode falla
        $_SESSION["entrades"] = json_encode($entrades, JSON_PRETTY_PRINT);
        if (empty($entrades)) {
            echo "No hi ha entrades disponibles.";
            return;
        }

        // Mostrar en HTML simple 
        foreach ($entrades as $entrada) {
            echo "ID: " . $entrada['idEntrada'] . "<br>";
            echo "IDUsuari: " . $entrada['idUsuari'] . "<br>";
            echo "Preu: " . $entrada['preu'] . " &euro;<br>";
            echo "Estat: " . $entrada['idEstatEntrada'] . "<br>";
            echo "ID assaig: " . $entrada['idAssaig'] . "<br>";
            echo "<hr>";
        }
    }
    //pasar parámetros... $idConcert, etc.
    public function creaEntradesConcert($idConcert)
    {
        //to do...
    }

    /**
     * Cancela una reserva del usuario actual.
     * Reembolsa el preu i actualitza aforament.
     *  M�todos necesarios en EntradaGateway:
     * - getById($idEntrada)
     * - getSaldoUsuari($idUsuari)
     * - actualizarSaldo($idUsuari, $nouSaldo)
     * - cancelarReserva($idEntrada, $idUsuari)
     * - incrementarAforament($idConcert)
     */
    public function cancelarReserva()
    {
        $idUsuari = $_SESSION['user']['id'] ?? null;
        $idEntrada = $_POST['idEntrada'] ?? null;

        if (!$idUsuari || !$idEntrada) {
            echo "Error: Falten dades.";
            return;
        }

        $entrada = $this->entradaGateway->getById($idEntrada);

        if (!$entrada || $entrada['estat'] !== 'Reservada' || $entrada['id_usuari'] != $idUsuari) {
            echo "No pots cancelar aquesta reserva.";
            return;
        }

        // Reembolso
        $saldo = $this->entradaGateway->getSaldoUsuari($idUsuari);
        $nouSaldo = $saldo + $entrada['preu'];
        $this->entradaGateway->actualizarSaldo($idUsuari, $nouSaldo);

        // Cancelar la reserva
        $this->entradaGateway->cancelarReserva($idEntrada, $idUsuari);


        if ($resultat > 0) {
            $entrada = $this->entradaGateway->getById($idEntrada);
            $idConcert = $entrada['idConcert'];
            $this->entradaGateway->incrementarAforament($idConcert);
        }

        echo "Reserva cancelada i diners reemborsats.";
    }
}
