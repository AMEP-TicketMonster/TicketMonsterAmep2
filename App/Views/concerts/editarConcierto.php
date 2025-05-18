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

// Inicializar variables
$mensaje = "";
$nombre = $fecha = $hora = $lugar = $grupo = $precio = $entradas = "";

// SUPONEMOS que recibimos un ID de concierto por GET (editarConcierto.php?id=1)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Cargar datos del concierto
    $sql = "SELECT * FROM conciertos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $concierto = $resultado->fetch_assoc();
        $nombre = $concierto['nombre'];
        $fecha = $concierto['fecha'];
        $hora = $concierto['hora'];
        $lugar = $concierto['lugar'];
        $grupo = $concierto['grupo'];
        $precio = $concierto['precio'];
        $entradas = $concierto['entradas_disponibles'];
    } else {
        $mensaje = "❌ Concierto no encontrado.";
    }
}

// Si envían el formulario para guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $lugar = $_POST['lugar'];
    $grupo = $_POST['grupo'];
    $precio = $_POST['precio'];
    $entradas = $_POST['entradas'];

    $sql = "UPDATE conciertos SET nombre=?, fecha=?, hora=?, lugar=?, grupo=?, precio=?, entradas_disponibles=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdii", $nombre, $fecha, $hora, $lugar, $grupo, $precio, $entradas, $id);

    if ($stmt->execute()) {
        $mensaje = "✅ ¡Concierto actualizado exitosamente!";
    } else {
        $mensaje = "❌ Error al actualizar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Concierto</title>
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
      <div class="card-header text-bg-warning text-center">
        <h4>✏️ Editar concierto: <strong><?php echo htmlspecialchars($nombre); ?></strong></h4>
      </div>
      <div class="card-body">
        <form action="" method="POST">
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

          <input type="text" class="form-control mb-3" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

          <input type="date" class="form-control mb-3" name="fecha" value="<?php echo htmlspecialchars($fecha); ?>" required>

          <input type="time" class="form-control mb-3" name="hora" value="<?php echo htmlspecialchars($hora); ?>" required>

          <input type="text" class="form-control mb-3" name="lugar" value="<?php echo htmlspecialchars($lugar); ?>" required>

          <input type="text" class="form-control mb-3" name="grupo" value="<?php echo htmlspecialchars($grupo); ?>" required>

          <input type="number" class="form-control mb-3" name="precio" value="<?php echo htmlspecialchars($precio); ?>" required>

          <input type="number" class="form-control mb-4" name="entradas" value="<?php echo htmlspecialchars($entradas); ?>" required>

          <div class="d-grid">
            <button type="submit" class="btn btn-warning">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $conn->close(); ?>

</body>
</html>
