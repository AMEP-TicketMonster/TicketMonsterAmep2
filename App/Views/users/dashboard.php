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
$entrades = new EntradaController();
$entrades->consultarEntrades();
?>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


<div class="container my-5">

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

<script>
  let entradas = <?php echo json_encode($_SESSION['entrades_usuari']) ?>


  const tbody = document.getElementById("entradas-body");

  entradas.forEach(entrada => {
    const row = document.createElement("tr");
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
      768: { slidesPerView: 3.5 },
      1024: { slidesPerView: 4.5 },
    },
  });

</script>
