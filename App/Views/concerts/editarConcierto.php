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
    <div class="card shadow">
      <div class="card-header text-bg-warning text-center">
        <h4>✏️ Editar concierto: <strong>Rock Fest</strong></h4>
      </div>
      <div class="card-body">
        <form>
          <input type="text" class="form-control mb-3" value="Rock Fest">
          <input type="date" class="form-control mb-3" value="2025-07-10">
          <input type="time" class="form-control mb-3" value="20:30">
          <input type="text" class="form-control mb-3" value="Sala Apolo, Barcelona">
          <input type="text" class="form-control mb-3" value="Metal Machine">
          <input type="number" class="form-control mb-3" value="30">
          <input type="number" class="form-control mb-4" value="100">

          <div class="d-grid">
            <button type="submit" class="btn btn-warning">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
