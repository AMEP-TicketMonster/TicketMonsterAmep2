

<div class="container py-5">
  <div class="col-md-8 mx-auto">


    <div class="card shadow">
      <div class="card-header text-bg-primary text-center">
        <h4>🎤 Crear nuevo concierto</h4>
      </div>
      <div class="card-body">
        <form action="/crea-concert-backend" method="POST">
          <input type="text" class="form-control mb-3" name="nombre_concierto" placeholder="Nombre del concierto" required>

          <input type="date" class="form-control mb-3" name="fecha" required>

          <input type="time" class="form-control mb-3" name="hora" required>

          <input type="text" class="form-control mb-3" name="lugar" placeholder="Lugar / Sala" required>

          <input type="text" class="form-control mb-3" name="grupo_musical" placeholder="Grupo musical" required>

          <input type="number" class="form-control mb-3" name="precio" placeholder="Precio (€)" step="0.01" required>

          <input type="text" class="form-control mb-3" name="genero"  placeholder="genero" required>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Crear concierto</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

