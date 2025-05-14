<?php
// CONEXIÓN A BASE DE DATOS
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

$mensaje = "";

// SI SE ENVÍA EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_concierto'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $lugar = $_POST['lugar'];
    $grupo = $_POST['grupo_musical'];
    $precio = $_POST['precio'];
    $entradas = $_POST['entradas_disponibles'];

    // PREPARAR INSERCIÓN
    $sql = "INSERT INTO conciertos (nombre, fecha, hora, lugar, grupo, precio, entradas_disponibles)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssd", $nombre, $fecha, $hora, $lugar, $grupo, $precio, $entradas);

    if ($stmt->execute()) {
        $mensaje = "🎉 ¡Concierto creado exitosamente!";
    } else {
        $mensaje = "❌ Error al crear concierto: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Concierto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="col-md-8 mx-auto">

    <?php if ($mensaje): ?>
    <div class="alert alert-info text-center">
      <?php echo $mensaje; ?>
    </div>
    <?php endif; ?>

    <div class="card shadow">
      <div class="card-header text-bg-primary text-center">
        <h4>🎤 Crear nuevo concierto</h4>
      </div>
      <div class="card-body">
        <form action="" method="POST">
          <input type="text" class="form-control mb-3" name="nombre_concierto" placeholder="Nombre del concierto" required>

          <input type="date" class="form-control mb-3" name="fecha" required>

          <input type="time" class="form-control mb-3" name="hora" required>

          <input type="text" class="form-control mb-3" name="lugar" placeholder="Lugar / Sala" required>

          <input type="text" class="form-control mb-3" name="grupo_musical" placeholder="Grupo musical" required>

          <input type="number" class="form-control mb-3" name="precio" placeholder="Precio (€)" step="0.01" required>

          <input type="number" class="form-control mb-4" name="entradas_disponibles" placeholder="Entradas disponibles" required>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Crear concierto</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $conn->close(); ?>

</body>
</html>

