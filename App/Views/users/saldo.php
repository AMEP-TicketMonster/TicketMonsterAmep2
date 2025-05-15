<?php
$saldo = $_SESSION['user']['saldo'] ?? 0.00;
?>


<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="container">
    <div class="card shadow-sm p-4 mx-auto" style="max-width: 100%; width: 100%; max-width: 500px;">

      <div class="text-center mb-4">
        <i class="bi bi-wallet2 fs-1 text-success mb-3"></i>
        <h5 class="fw-bold">Tu saldo actual</h5>
        <p class="fs-4 text-success fw-bold"><?= number_format($saldo, 2) ?>€</p>
      </div>

      <form method="POST" action="/recargar-saldo">
        <div class="mb-4">
          <label for="cantidad" class="form-label">Cantidad a recargar (€)</label>
          <input type="number" name="cantidad" step="0.01" min="1" class="form-control" id="cantidad" placeholder="Introduce la cantidad" required>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-success" style="background-color:#624DE3;">Recargar Saldo</button>
        </div>
      </form>
    </div>
  </div>
</div>
