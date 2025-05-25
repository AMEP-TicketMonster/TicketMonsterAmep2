<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class SalaGateway
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    // Obtener todas las salas y sus slots (libres o reservadas)
    public function fetchSalasConSlots(): array
    {
        $sql = "
            SELECT s.idSala, s.nom, s.ciutat, s.capacitat,
                   ds.idDataSala, ds.dia, ds.hora_inici, ds.hora_fi,
                   a.idAssajos, a.idUsuari
            FROM Sales s
            JOIN DataSala ds ON ds.idSala = s.idSala
            LEFT JOIN Assajos a ON a.idDataSala = ds.idDataSala
            ORDER BY ds.dia, ds.hora_inici
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Reservar una sala en un slot para un usuario
    public function reservar(int $idSala, int $idDataSala, int $idUsuari): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM Assajos WHERE idDataSala = ?");
        $stmt->execute([$idDataSala]);
        if ($stmt->fetch()) return false;

        $stmt = $this->pdo->prepare("
            INSERT INTO Assajos (idUsuari, idSala, idDataSala, entrades_disponibles)
            VALUES (?, ?, ?, 0)
        ");
        return $stmt->execute([$idUsuari, $idSala, $idDataSala]);
    }

    // Obtener reservas de un usuario
    public function getReservasPorUsuario(int $idUsuari): array
    {
        $sql = "
            SELECT a.idAssajos, s.nom AS sala, s.ciutat, ds.dia, ds.hora_inici, ds.hora_fi
            FROM Assajos a
            JOIN Sales s ON a.idSala = s.idSala
            JOIN DataSala ds ON a.idDataSala = ds.idDataSala
            WHERE a.idUsuari = ?
            ORDER BY ds.dia, ds.hora_inici
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUsuari]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarReserva(int $idAssajos, int $idUsuari): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM Assajos WHERE idAssajos = ? AND idUsuari = ?");
        return $stmt->execute([$idAssajos, $idUsuari]);
    }

    public function actualizarReserva(int $idAssajos, int $nuevoIdDataSala, int $idUsuari): bool
{
    // Validar si la reserva pertenece al usuario actual
    $stmt = $this->pdo->prepare("SELECT 1 FROM Assajos WHERE idAssajos = ? AND idUsuari = ?");
    $stmt->execute([$idAssajos, $idUsuari]);
    if (!$stmt->fetch()) {
        return false;
    }

    // Verificar si el nuevo slot ya está ocupado
    $stmt = $this->pdo->prepare("SELECT 1 FROM Assajos WHERE idDataSala = ?");
    $stmt->execute([$nuevoIdDataSala]);
    if ($stmt->fetch()) {
        return false;
    }

    // Actualizar la reserva
    $stmt = $this->pdo->prepare("UPDATE Assajos SET idDataSala = ? WHERE idAssajos = ?");
    return $stmt->execute([$nuevoIdDataSala, $idAssajos]);
}

}
