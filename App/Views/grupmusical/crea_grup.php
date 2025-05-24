<div class="container py-5">
  <div class="col-md-8 mx-auto">

    <div class="card shadow">
      <div class="card-header text-bg-primary text-center">
        <h4>🎸 Crear nuevo grupo musical</h4>
      </div>
      <div class="card-body">
        <form action="/crea-grup-backend" method="POST">
          <p>Nombre del grupo:</p>
          <input type="text" class="form-control mb-3" name="nomGrup" placeholder="Nombre del grupo" required>

          <p>Descripción:</p>
          <textarea class="form-control mb-3" name="descripcio" placeholder="Descripción del grupo" rows="4" required></textarea>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Crear grupo musical</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
