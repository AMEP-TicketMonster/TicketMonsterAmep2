<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Comprar Entrada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="col-md-8 mx-auto">
    <div class="card shadow">
      <div class="card-header text-bg-success text-center">
        <h4>🎟️ Comprar o reservar entrada</h4>
      </div>
      <div class="card-body">
        <h5>🎤 Concierto: Rock Fest</h5>
        <p>📍 Sala Apolo, Barcelona</p>
        <p>🕒 20:30 - 📅 10/07/2025</p>

        <form>
          <div class="mb-3">
            <label class="form-label">Selecciona tipo de entrada</label>
            <select class="form-select">
              <option>Entrada General - 30€</option>
              <option>Entrada VIP - 50€</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input type="number" class="form-control" value="1" min="1">
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-success">Reservar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
