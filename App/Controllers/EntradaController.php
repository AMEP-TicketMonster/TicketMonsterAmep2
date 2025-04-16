<?php

namespace App\Controllers;

use App\Models\EntradaGateway;
use Core\Route;
use Core\Auth;
use Core\Session;

class EntradaController
{
    private string $id;
    private float $preu;
    private string $estat;
    private $entradaGateway;

    public function __construct()
    {

        $this->entradaGateway = new EntradaGateway();
        if (session_status() === PHP_SESSION_NONE) {
            Session::sessionStart("ticketmonster_session");
        }
    }



    /**
     * Compra una entrada si est� disponible y el usuario tiene saldo suficiente.
     * M�todos necesarios en EntradaGateway:
     * - getById($idEntrada)
     * - getSaldoUsuari($idUsuari)
     * - actualizarSaldo($idUsuari, $nouSaldo)
     * - assignarEntrada($idEntrada, $idUsuari, $estat)
     * - decrementarAforament($idConcert)
     */
    public function comprarEntrada()
    {

        $idUsuari = $_SESSION['user']['id'] ?? null;

        // Obtener la entrada que se quiere comprar (por ejemplo, desde un formulario)
        $idEntrada = $_POST['idEntrada'] ?? null;

        // Validaci�n b�sica
        if (!$idUsuari || !$idEntrada) {
            echo "Error: Falten dades necessaris per realitzar la compra.";
            return;
        }

        // Obtener la entrada desde la base de datos
        $entrada = $this->entradaGateway->getByEntradaId($idEntrada);

        if (!$entrada) {
            echo "Error: La entrada no existeix.";
            return;
        }

        if ($entrada['estat'] !== 'Disponible') {
            echo "Error: La entrada ha sigut comprada  o no est� disponible.";
            return;
        }
        //tal vez getSaldoUsuari debería ser una función de el modelo Usuaris, es decir $this->usuariGateway->getSaldo($idUsuari);
        $saldo = $this->entradaGateway->getSaldoUsuari($idUsuari);
        $preu = $entrada['preu'];

        if ($saldo < $preu) {
            echo "Error: Saldo insuficient.";
            return;
        }

        // Actualizar saldo del usuario
        $nouSaldo = $saldo - $preu;
        $this->entradaGateway->actualizarSaldo($idUsuari, $nouSaldo);


        // Asignar la entrada al usuario y cambiar el estado
        $this->entradaGateway->assignarEntrada($idEntrada, $idUsuari, "Comprada");

        $idConcierto = $entrada['idConcierto'];
        $this->entradaGateway->decrementarAforament($idConcierto);

        echo "Compra realitzada amb �xit.";
    }



    /**
     * Reserva una entrada disponible.
     * M�todos necesarios en EntradaGateway:
     * - getById($idEntrada)
     * - assignarEntrada($idEntrada, $idUsuari, $estat)
     * - decrementarAforament($idConcert)
     */
    public function reservarEntrada()
    {
        $idUsuari = $_SESSION['user']['id'] ?? null;
        $idEntrada = $_POST['idEntrada'] ?? null;

        if (!$idUsuari || !$idEntrada) {
            echo "Error: Falten dades necessaris per realitzar la reserva.";
            return;
        }

        $entrada = $this->entradaGateway->getById($idEntrada);

        if (!$entrada) {
            echo "Error: La entrada no existeix.";
            return;
        }

        if ($entrada['estat'] !== 'Disponible') {
            echo "Error: La entrada ja est� reservada o comprada.";
            return;
        }

        $this->entradaGateway->assignarEntrada($idEntrada, $idUsuari, "Reservada");

        $idConcert = $entrada['idConcert'];
        $this->entradaGateway->decrementarAforament(idConcert);

        echo "Reserva realitzada amb �xit.";
    }



    /**
     * Consulta todas las entradas.
     *  M�todo necesario en EntradaGateway:
     * - getAllEntrades()
     */
    //deberíamos hacer una función que muestre todas las entradas que tiene compradas un usuario?
    public function consultarEntrades()
    {
        $entrades = $this->entradaGateway->getAllEntrades();
        $_SESSION["entrades"] = json_encode($entrades);
        if (empty($entrades)) {
            echo "No hi ha entrades disponibles.";
            return;
        }

        // Mostrar en HTML simple 
        foreach ($entrades as $entrada) {
            echo "ID: " . $entrada['idEntrada'] . "<br>";
            echo "Preu: " . $entrada['preu'] . " �<br>";
            echo "Estat: " . $entrada['estat'] . "<br>";
            echo "ID Concert: " . $entrada['idConcert'] . "<br>";
            echo "Data d'adquisici�: " . $entrada['data_adquisicio'] . "<br>";
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
