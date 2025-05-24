<?php

namespace App\Controllers;

use App\Models\SalaGateway;

class SalaController
{
    private SalaGateway $salaGateway;

    public function __construct()
    {
        $this->salaGateway = new SalaGateway();
    }

    public function index(): void
    {
        $salasConSlots = $this->salaGateway->fetchSalasConSlots();
        $_SESSION['salas_con_slots'] = $salasConSlots;
        include_once __DIR__ . '/../Views/salas/index.php';
    }

    public function reservarSala(): void
    {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Debes iniciar sesión para reservar.";
            header("Location: /salas");
            exit;
        }

        $idSala = $_POST['idSala'] ?? null;
        $idDataSala = $_POST['idDataSala'] ?? null;
        $idUsuari = $_SESSION['user']['idUsuari'];

        if (!$idSala || !$idDataSala) {
            $_SESSION['error'] = "Datos de reserva incompletos.";
            header("Location: /salas");
            exit;
        }

        $ok = $this->salaGateway->reservar((int)$idSala, (int)$idDataSala, (int)$idUsuari);
        $_SESSION[$ok ? 'mensaje' : 'error'] = $ok ? "Reserva realizada con éxito." : "Esa sala ya está reservada.";

        header("Location: /salas");
        exit;
    }

    public function verMisReservas(): void
    {
        $idUsuari = $_SESSION['user']['idUsuari'];
        $reservas = $this->salaGateway->getReservasPorUsuario($idUsuari);
        $_SESSION['mis_reservas'] = $reservas;
        include_once __DIR__ . '/../Views/salas/mis_reservas.php';
    }

    public function eliminarReserva(): void
    {
        $id = $_POST['idAssajos'] ?? null;
        $idUsuari = $_SESSION['user']['idUsuari'];

        $ok = $this->salaGateway->eliminarReserva($id, $idUsuari);
        $_SESSION[$ok ? 'mensaje' : 'error'] = $ok ? "Reserva cancelada." : "No se pudo cancelar.";
        header("Location: /salas");
        exit();
    }

    public function editarReserva(): void
    {
        $idAssajos = $_GET['idAssajos'] ?? null;
        $_SESSION['idAssajosEditar'] = $idAssajos;
        $_SESSION['slots_disponibles'] = $this->salaGateway->fetchSalasConSlots();
        include_once __DIR__ . '/../Views/salas/editar_reserva.php';
    }

    public function guardarEdicion(): void
    {
        $idAssajos = $_POST['idAssajosEditar'] ?? null;
        $nuevoIdDataSala = $_POST['nuevoIdDataSala'] ?? null;
        $idUsuari = $_SESSION['user']['idUsuari'];

        if (!$idAssajos || !$nuevoIdDataSala) {
            $_SESSION['error'] = "Datos incompletos para editar la reserva.";
            header("Location: /salas");
            exit();
        }

        $ok = $this->salaGateway->actualizarReserva((int)$idAssajos, (int)$nuevoIdDataSala, (int)$idUsuari);

        $_SESSION[$ok ? 'mensaje' : 'error'] = $ok
            ? "Reserva modificada con éxito."
            : "No se pudo modificar la reserva. Verifica si ese horario ya está reservado.";

        header("Location: /salas");
        exit();
    }
}
