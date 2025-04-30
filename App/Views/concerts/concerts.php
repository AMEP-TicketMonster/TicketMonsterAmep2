<?php
$concerts = isset($_SESSION['concerts']) ? $_SESSION['concerts'] : '[]';
// Esto es solo para debug en backend:
//var_dump(json_encode($concerts));
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Próximos Conciertos</h2>
    <div id="concert-container" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"></div>
</div>

<script>
    const concerts =JSON.parse(`<?php echo json_encode($concerts); ?>`);

    console.log('Concerts:', concerts);

    const container = document.getElementById('concert-container');
    concerts.forEach(concert => {
        const col = document.createElement('div');
        col.className = 'col';
        col.innerHTML = `
      <div class="card shadow-sm h-100">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title text-primary fw-bold">
            <i class="bi bi-music-note-beamed"></i> ${concert.nom}
          </h5>
           <h6 class="card-title text-primary fw-bold">
            <i class="bi bi-music-note-beamed"></i> ${concert.grup}
          </h6>
          <p class="card-text mb-1"><i class="bi bi-calendar-event"></i> Fecha: ${concert.dia}</p>
          <p class="card-text mb-1"><i class="bi bi-geo-alt"></i> Ubicación: ${concert.ubicacio}</p>
          <p class="card-text mb-3"><i class="bi bi-currency-euro"></i> Sala: ${concert.sala}</p>
          <div class="mt-auto">
            <a href="/concierto?id=${concert.idConcert}" class="btn btn-primary w-100" style="background-color:#624DE3;">
              Ver detalles
            </a>
          </div>
        </div>
      </div>
    `;
        container.appendChild(col);
    });
</script>