<?php
//echo json_decode($_SESSION['user']);
use App\Controllers\EntradaController;

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$entrades = isset($_SESSION['entrades']) ? $_SESSION['entrades'] : null;
$grups = isset($_SESSION['grupos']) ? $_SESSION['grupos'] : null;
//var_dump($entrades);
?>

<p id="userInfo"></p><br>
<p id="concerts"></p>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
  .dash {
    /*display: none;*/
  }
  .formulario-valoracion {
    display: none;
    flex-direction: column;
  }
</style>

<div class="container my-5 dash">
  <div class="slider-container">
    <div class="slider-track">
      <?php foreach ($grups as $grup): ?>
        <div class="group-card">
          <h5><?php echo htmlspecialchars($grup['nomGrup']); ?></h5>
          <p><?php echo htmlspecialchars($grup['descripcio']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <br>
  <div class="container">
    <h2 class="mb-4">Mis Entradas</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Evento</th>
            <th>Tipo</th>
            <th>Artista</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Precio</th>
            <th>Compra</th>
          </tr>
        </thead>
        <tbody id="entradas-body"></tbody>
      </table>
    </div>
  </div>

  <div class="container">
    <h2 class="mb-4">Historial</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Evento</th>
            <th>Tipo</th>
            <th>Artista</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Precio</th>
            <th>Compra</th>
            <th>Valorar</th>
          </tr>
        </thead>
        <tbody id="entradas-body2"></tbody>
      </table>
    </div>
  </div>

<script>
let entradas = <?php echo json_encode($_SESSION['entrades_usuari']) ?>;
let fechaActual = new Date();

const tbody = document.getElementById("entradas-body");
const tbody2 = document.getElementById("entradas-body2");

entradas.forEach(entrada => {
  const row = document.createElement("tr");
  let compareData = new Date(entrada.dia);

  if (compareData > fechaActual) {
    row.innerHTML = `
      <td>${entrada.nomConcert}</td>
      <td>${entrada.tipus}</td>
      <td>${entrada.nomGrup}</td>
      <td>${entrada.dia}</td>
      <td>${entrada.hora_inici} - ${entrada.hora_fi}</td>
      <td>€${parseFloat(entrada.preu).toFixed(2)}</td>
      <td>${entrada.data_transaccio}</td>
    `;
    tbody.appendChild(row);
  } else {
    row.innerHTML = `
      <td>${entrada.nomConcert}</td>
      <td>${entrada.tipus}</td>
      <td>${entrada.nomGrup}</td>
      <td>${entrada.dia}</td>
      <td>${entrada.hora_inici} - ${entrada.hora_fi}</td>
      <td>€${parseFloat(entrada.preu).toFixed(2)}</td>
      <td>${entrada.data_transaccio}</td>
      <td class="text-center align-middle">
        <button type="button" class="btn btn-primary formulario-valoracion-boton" style="background-color:#624DE3;">Valorar</button>

        <div class="formulario-valoracion mt-2">
          <form action="/guardarValoracion" method="POST">
            <div class="mb-3">
              <label for="comentario" class="form-label"><strong>Comentario:</strong></label>
              <textarea name="comentario" class="form-control valoracion-textarea" placeholder="Escribe tu comentario aquí..." required></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label"><strong>Puntuación:</strong></label>
              <div class="valoracion-estrellas">
                <input type="radio" id="estrella5-${entrada.idConcert}" name="puntuacion" value="5" required>
                <label for="estrella5-${entrada.idConcert}">★</label>

                <input type="radio" id="estrella4-${entrada.idConcert}" name="puntuacion" value="4">
                <label for="estrella4-${entrada.idConcert}">★</label>

                <input type="radio" id="estrella3-${entrada.idConcert}" name="puntuacion" value="3">
                <label for="estrella3-${entrada.idConcert}">★</label>

                <input type="radio" id="estrella2-${entrada.idConcert}" name="puntuacion" value="2">
                <label for="estrella2-${entrada.idConcert}">★</label>

                <input type="radio" id="estrella1-${entrada.idConcert}" name="puntuacion" value="1">
                <label for="estrella1-${entrada.idConcert}">★</label>
              </div>
            </div>

            <input type="hidden" name="idConcert" value="${entrada.idConcert}">
            <button type="submit" class="btn valoracion-boton">Enviar valoración</button>
          </form>
        </div>
      </td>
    `;
    tbody2.appendChild(row);
  }
});

// Mostrar/ocultar el formulario al hacer click
document.querySelectorAll('.formulario-valoracion-boton').forEach(boton => {
  boton.addEventListener('click', () => {
    let td = boton.closest('td');
    let form = td.querySelector('.formulario-valoracion');
    form.style.display = 'flex';
    boton.style.display = 'none';
  });
});
</script>
