<?php
// Conexión a base de datos
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Obtener todas las reservas activas (puedes filtrar por usuario si quieres)
$sql = "SELECT reservas.id, conciertos.nombre AS concierto, reservas.cantidad
        FROM reservas
        JOIN conciertos ON reservas.concierto_id = conciertos.id
        WHERE reservas.estado = 'activo'";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Reservas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <h1 class="mb-4 text-center">🎟️ Mis Reservas</h1>

  <?php if ($resultado && $resultado->num_rows > 0): ?>
    <table class="table table-hover table-bordered bg-white shadow">
      <thead class="table-primary text-center">
        <tr>
          <th>ID</th>
          <th>Concierto</th>
          <th>Cantidad</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php while ($reserva = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($reserva['id']); ?></td>
          <td><?php echo htmlspecialchars($reserva['concierto']); ?></td>
          <td><?php echo htmlspecialchars($reserva['cantidad']); ?></td>
          <td>
            <a href="cancelarReserva.php?id=<?php echo $reserva['id']; ?>" class="btn btn-danger btn-sm">
              Cancelar
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-info text-center">
      No tienes reservas activas.
    </div>
  <?php endif; ?>

</div>

<?php $conn->close(); ?>

</body>
</html>