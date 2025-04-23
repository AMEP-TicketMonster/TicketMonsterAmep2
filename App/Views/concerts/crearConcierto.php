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
    <div class="card shadow">
      <div class="card-header text-bg-primary text-center">
        <h4>🎤 Crear nuevo concierto</h4>
      </div>
      <div class="card-body">
        <form>
          <input type="text" class="form-control mb-3" placeholder="Nombre del concierto">
          <input type="date" class="form-control mb-3">
          <input type="time" class="form-control mb-3">
          <input type="text" class="form-control mb-3" placeholder="Lugar / Sala">
          <input type="text" class="form-control mb-3" placeholder="Grupo musical">
          <input type="number" class="form-control mb-3" placeholder="Precio (€)">
          <input type="number" class="form-control mb-4" placeholder="Entradas disponibles">

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Crear concierto</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
