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

<?php
//Esto hay que ponerlo en otro sitio, queda provisional aquí (código spaguetti)

?>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<style>
  .dash {
    /*display: none;*/
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
      let compareData = new Date(entrada.dia); // Fecha del JSON
      console.log("fechaConciert: " + compareData);
      console.log("fechaActual: " + fechaActual);
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
        <div class="formulario-valoracion">




    <form action="/guardarValoracion" method="POST">
    <div class="mb-3 ">
      <label for="comentario" class="form-label"><strong>Comentario:</strong></label>
      <textarea id="comentario" name="comentario" class="form-control valoracion-textarea" placeholder="Escribe tu comentario aquí..." required></textarea>
    </div>

    <div class="mb-3 ">
      <label class="form-label"><strong>Puntuación:</strong></label>
      <div class="valoracion-estrellas">
        <input type="radio" id="estrella5" name="puntuacion" value="5" required>
        <label for="estrella5">★</label>

        <input type="radio" id="estrella4" name="puntuacion" value="4">
        <label for="estrella4">★</label>

        <input type="radio" id="estrella3" name="puntuacion" value="3">
        <label for="estrella3">★</label>

        <input type="radio" id="estrella2" name="puntuacion" value="2">
        <label for="estrella2">★</label>

        <input type="radio" id="estrella1" name="puntuacion" value="1">
        <label for="estrella1">★</label>
        <input type="hidden" class="form-idConcert" name="idConcert" value="${entrada.idConcert}">
      </div>
    </div>

    <button type="submit" class="btn valoracion-boton">Enviar valoración</button>
  </form>
  </div>


    <button type="submit" class="btn btn-primary formulario-valoracion-boton" style="background-color:#624DE3;">Valorar</button>
        
        
        </td>
      `;
        tbody2.appendChild(row);
      }


    });


    const swiper = new Swiper(".mySwiper", {
      slidesPerView: 2.5,
      spaceBetween: 20,
      loop: true,
      speed: 4000,
      autoplay: {
        delay: 0,
        disableOnInteraction: false,
      },
      freeMode: true,
      freeModeMomentum: false,
      grabCursor: true,
      breakpoints: {
        768: {
          slidesPerView: 3.5
        },
        1024: {
          slidesPerView: 4.5
        },
      },
    });

    document.querySelector('.slider-container').addEventListener('click', function() {
      window.location.href = '/grupos'; // Aquí pon la URL a la que quieres redirigir
    });

    //caja de la valoración
 // Selecciona todos los botones de "Valorar"
document.querySelectorAll('.formulario-valoracion-boton').forEach((boton) => {
  boton.addEventListener('click', () => {
    // Encuentra el formulario que está dentro del mismo TD (padre del botón)
    let td = boton.closest('td'); // Busca el td más cercano al botón
    let form = td.querySelector('.formulario-valoracion');

    // Mostrar/ocultar
    form.style.display = 'flex';
    boton.style.display = 'none';
  });
});

document.querySelectorAll('.formulario-valoracion').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch(form.action, {
      method: form.method,
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      console.log("Valoración enviada:", data);
      // Aquí podrías mostrar un mensaje de éxito, ocultar el formulario, etc.
    })
    .catch(error => {
      console.error("Error al enviar la valoración:", error);
    });
  });
});


  </script>