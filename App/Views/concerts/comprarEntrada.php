<?php
// CONFIGURACIÓN DE CONEXIÓN A BBDD
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Obtener lista de conciertos
$sql = "SELECT id, nombre FROM conciertos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comprar o Reservar Entrada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="col-md-8 mx-auto">
    <div class="card shadow">
      <div class="card-header text-bg-success text-center">
        <h4>🎟️ Comprar o Reservar Entrada</h4>
      </div>
      <div class="card-body">
        <form id="formCompra">
          <div class="mb-3">
            <label for="usuario" class="form-label">Nombre del Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Tu nombre" required>
          </div>

          <div class="mb-3">
            <label for="concierto" class="form-label">Concierto</label>
            <select class="form-select" id="concierto" name="concierto" required>
              <option value="">Selecciona un concierto</option>
              <?php
              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<option value='" . htmlspecialchars($row['nombre']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                  }
              } else {
                  echo "<option value=''>No hay conciertos disponibles</option>";
              }
              ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de Operación</label>
            <select class="form-select" id="tipo" name="tipo" required>
              <option value="">Elige una opción</option>
              <option value="compra">Comprar Entrada</option>
              <option value="reserva">Reservar Entrada</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad de Entradas</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" placeholder="Número de entradas" required>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-success">Confirmar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmacionModal" tabindex="-1" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="confirmacionModalLabel">✅ Operación Exitosa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p id="mensajeConfirmacion"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts Bootstrap + JavaScript para Modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Capturar el formulario
document.getElementById('formCompra').addEventListener('submit', function(event) {
  event.preventDefault(); // Evitar el envío real

  // Capturar los valores ingresados
  const usuario = document.getElementById('usuario').value;
  const concierto = document.getElementById('concierto').value;
  const tipo = document.getElementById('tipo').value;
  const cantidad = document.getElementById('cantidad').value;

  // Armar el mensaje dinámico
  let mensaje = "";
  if (tipo === "compra") {
    mensaje = `🎉 ${usuario}, has comprado ${cantidad} entrada(s) para el concierto "${concierto}".`;
  } else if (tipo === "reserva") {
    mensaje = `📅 ${usuario}, has reservado ${cantidad} entrada(s) para el concierto "${concierto}".`;
  } else {
    mensaje = "Operación realizada.";
  }

  // Insertar el mensaje en el modal
  document.getElementById('mensajeConfirmacion').textContent = mensaje;

  // Mostrar el modal
  const confirmacionModal = new bootstrap.Modal(document.getElementById('confirmacionModal'));
  confirmacionModal.show();
});
</script>

<?php
$conn->close(); // Cerrar conexión a BBDD
?>
</body>
</html>

