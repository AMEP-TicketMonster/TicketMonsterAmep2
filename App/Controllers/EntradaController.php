<?php
    namespace App\Controllers;

    use App\Models\EntradaGateway;
    Use Core\Route;
    use Core\Auth;
    use Core\Session;
    class EntradaController
    {
        private string $id;
        private float $preu;
        private string $estat;

        public function __construct()
        {
            $this->entradaGateway = new EntradaGateway();
            if (session_status() === PHP_SESSION_NONE)
            {
                Session::sessionStart("ticketmonster_session");
            }
        }



        /**
        * Compra una entrada si está disponible y el usuario tiene saldo suficiente.
        * Métodos necesarios en EntradaGateway:
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

            // Validación básica
            if (!$idUsuari || !$idEntrada)
            {
                echo "Error: Falten dades necessaris per realitzar la compra.";
                return;
            }

            // Obtener la entrada desde la base de datos
            $entrada = $this->entradaGateway->getById($idEntrada);

            if (!$entrada)
            {
                echo "Error: La entrada no existeix.";
                return;
            }

            if ($entrada['estat'] !== 'Disponible')
            {
                echo "Error: La entrada ha sigut comprada  o no està disponible.";
                return;
            }

                $saldo = $this->entradaGateway->getSaldoUsuari($idUsuari);
                $preu = $entrada['preu'];

            if ($saldo < $preu)
            {
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

            echo "Compra realitzada amb éxit.";
  
        }
        


        /**
        * Reserva una entrada disponible.
        * Métodos necesarios en EntradaGateway:
        * - getById($idEntrada)
        * - assignarEntrada($idEntrada, $idUsuari, $estat)
        * - decrementarAforament($idConcert)
        */
        public function reservarEntrada()
        {
            $idUsuari = $_SESSION['user']['id'] ?? null;
            $idEntrada = $_POST['idEntrada'] ?? null;

            if (!$idUsuari || !$idEntrada)
            {
                echo "Error: Falten dades necessaris per realitzar la reserva.";
                return;
            }

            $entrada = $this->entradaGateway->getById($idEntrada);

            if (!$entrada)
            {
                echo "Error: La entrada no existeix.";
                return;
            }

            if ($entrada['estat'] !== 'Disponible')
            {
                echo "Error: La entrada ja està reservada o comprada.";
                return;
            }

            $this->entradaGateway->assignarEntrada($idEntrada, $idUsuari, "Reservada");

            $idConcert = $entrada['idConcert'];
            $this->entradaGateway->decrementarAforament(idConcert);

            echo "Reserva realitzada amb éxit.";
        }



        /**
        * Consulta todas las entradas.
        *  Método necesario en EntradaGateway:
        * - getAllEntrades()
        */
        public function consultarEntrades()
        {
            $entrades = $this->entradaGateway->getAllEntrades();

            if (empty($entrades))
            {
                echo "No hi ha entrades disponibles.";
                return;
            }
        
            // Mostrar en HTML simple 
            foreach ($entrades as $entrada)
            {
                echo "ID: " . $entrada['idEntrada'] . "<br>";
                echo "Preu: " . $entrada['preu'] . " €<br>";
                echo "Estat: " . $entrada['estat'] . "<br>";
                echo "ID Concert: " . $entrada['idConcert'] . "<br>";
                echo "Data d'adquisició: " . $entrada['data_adquisicio'] . "<br>";
                echo "<hr>";
            }
        }



        /**
        * Cancela una reserva del usuario actual.
        * Reembolsa el preu i actualitza aforament.
        *  Métodos necesarios en EntradaGateway:
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
        
            if (!$idUsuari || !$idEntrada)
            {
                echo "Error: Falten dades.";
                return;
            }
        
            $entrada = $this->entradaGateway->getById($idEntrada);

            if (!$entrada || $entrada['estat'] !== 'Reservada' || $entrada['id_usuari'] != $idUsuari)
            {
                echo "No pots cancelar aquesta reserva.";
                return;
            }

            // Reembolso
            $saldo = $this->entradaGateway->getSaldoUsuari($idUsuari);
            $nouSaldo = $saldo + $entrada['preu'];
            $this->entradaGateway->actualizarSaldo($idUsuari, $nouSaldo);

            // Cancelar la reserva
            $this->entradaGateway->cancelarReserva($idEntrada, $idUsuari);ç
            

            if ($resultat > 0)
            {
                $entrada = $this->entradaGateway->getById($idEntrada);
                idConcert = $entrada['idConcert'];
                $this->entradaGateway->incrementarAforament($idConcert);
            }

            echo "Reserva cancelada i diners reemborsats.";
        }
}
?>