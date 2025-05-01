<?php

namespace App\Controllers;

use App\Models\EntradaGateway;
use App\Models\UserGateway;
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

    public function __construct()
    {
        $this->entradaGateway = new EntradaGateway();
        $this->usuariGateway = new UserGateway();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }

    /**
     * Compra una entrada assaig si est� disponible y el usuario tiene saldo suficiente.
     */
    public function comprarEntradaAssaig()
    {
        // TODO: creo que deberíamos tener un idEntradaConcert y un idEntradaAssaig
        //       no he podido probar estas dos _SESSION y _POST pq creo que no está cableado todavía
        //       pero he probado el resto de la función poniendo valores válidos en $idUsuari y $idEntrada

        //obtener la id del usuario por la variable de sesión no es seguro:
        //$idUsuari = $_SESSION['user']['id'] ?? null;
        $idUsuari = $this->usuariGateway->getId();
        // Obtener la entrada que se quiere comprar (por ejemplo, desde un formulario)
        $idEntrada = $_POST['idEntrada'] ?? null;

        // Validaci�n b�sica
        if (!$idUsuari || !$idEntrada) {
            echo "Error: Falten dades necessaris per realitzar la compra.";
            return;
        }

        // Obtener la entrada desde la base de datos
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

        $saldo = $this->usuariGateway->getByUserId($idUsuari)['saldo'];

        $preu = $entrada['preu'];

        if ($saldo < $preu) {
            echo "Error: Saldo insuficient.";
            return;
        }

        // Actualizar saldo del usuario
        $nouSaldo = $saldo - $preu;
        $this->usuariGateway->actualizarSaldo($idUsuari, $nouSaldo);

        // Asignar la entrada al usuario y cambiar el estado
        $this->entradaGateway->assignarEntradaAssaig($idEntrada, $idUsuari, "Comprada");

        $idAssaig = $entrada['idAssaig'];
        $this->entradaGateway->decrementarEntradesDisponiblesAssaig($idAssaig);

        echo "Compra realitzada amb �xit.";
    }


    /**
     * Compra una entrada concert si est� disponible y el usuario tiene saldo suficiente.
     */
    public function comprarEntradaConcert()
    {

        $idUsuari = $_SESSION['user']['idUsuari'] ?? null;
        // Obtener la entrada que se quiere comprar (por ejemplo, desde un formulario)
        $idEntrada = $_POST['idConcert'] ?? null;
        //var_dump($idUsuari,$idEntrada);
        

        // Validaci�n b�sica
        if (!$idUsuari || !$idEntrada) {
            echo "Error: Falten dades necessaris per realitzar la compra.";
            return;
        }

        // Obtener la entrada desde la base de datos
        //cuando se haga la creación de un concierto también hay que crear sus entradas
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

        $saldo = $this->usuariGateway->getByUserId($idUsuari)['saldo'];

        $preu = $entrada['preu'];
       
        if ($saldo < $preu) {
            echo "Error: Saldo insuficient.";
            return;
        }

        // Actualizar saldo del usuario
        $nouSaldo = $saldo - $preu;
        $this->usuariGateway->actualizarSaldo($idUsuari, $nouSaldo);
       
        // Asignar la entrada al usuario y cambiar el estado
        $this->entradaGateway->assignarEntradaConcert($idEntrada, $idUsuari, "Comprada");
      
        //Esto no está listo para funcionar con la DB actual
        /*
        $idConcert = $entrada['idConcert'];
        $this->entradaGateway->decrementarEntradesDisponiblesConcert($idConcert);

        echo "Compra realitzada amb �xit.";
        */
        header("location: /dashboard");
    }



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
    }

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
