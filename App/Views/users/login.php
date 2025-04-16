
<?php
$retry_login = isset($_SESSION['bad_login_data']) ? $_SESSION['bad_login_data'] : null;
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
  <div>
    <!-- Tarjeta del login -->
    <div class="card shadow-sm p-4" style="max-width:100%; width: 600px;">

      <div class="text-center mb-5">
        <i class="bi bi-person-circle fs-1 text-primary mb-3"></i>
      <br>
      <br>
        <h5 class="fw-bold">Accede a tu cuenta</h5>
      </div>

      <form method="POST" action="/login">
        <div class="mb-4">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" class="form-control" id="email" placeholder="Introduce tu email" required>
        </div>

        <div class="mb-4">
          <label for="password" class="form-label">Contraseña</label>
          <div class="input-group">
            <input type="password" class="form-control" name="password" id="password" placeholder="Introduce tu contraseña" required>
            <button class="btn btn-outline-secondary" type="button">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <br>
        </div>

        <div class="d-grid mb-2">
          <button type="submit" class="btn btn-primary" style="background-color:#624DE3;">Accede</button>
        </div>
      </form>
    </div>
    <br>
    <div class="text-center mt-3">
      <small class="text-muted">No tienes cuenta? <a href="/register">Regístrate</a></small>
    </div>
  </div>
</div>

<script>
  let datos = <?php echo json_encode($retry_login); ?>;
  if (datos) {
    alert("Datos incorrectos");
    <?php $_SESSION['bad_login_data'] = false; ?>
  }
</script>