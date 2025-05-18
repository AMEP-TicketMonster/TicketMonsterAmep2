<?php
// Conexión a la base de datos
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Suponemos que recibimos el ID de la reserva que queremos cancelar por GET
if (isset($_GET['id'])) {
    $idReserva = intval($_GET['id']);

    // Aquí decides: o eliminas la reserva o cambias su estado a cancelado
    // Ejemplo: eliminar la reserva
    $sql = "DELETE FROM reservas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idReserva);

    if ($stmt->execute()) {
        // Cancelación exitosa -> redirigir a pantalla de confirmación
        header('Location: cancelacionReserva.php');
        exit;
    } else {
        echo "❌ Error al cancelar la reserva: " . $conn->error;
    }
} else {
    echo "❌ No se especificó ninguna reserva para cancelar.";
}

$conn->close();
?>
