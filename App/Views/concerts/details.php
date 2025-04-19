<?php
$concert = isset($_SESSION['concert']) ? $_SESSION['concert'] : null;
?>
<div class="container my-5">
    <h2 class="text-center mb-4" id="concert-title"></h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Fecha:</strong> <span id="concert-date"></span></p>
            <p><strong>Ubicación:</strong> <span id="concert-location"></span></p>
            <p><strong>Precio:</strong> <span id="concert-price"></span> €</p>
            <p><strong>Aforo:</strong> <span id="concert-aforo"></span></p>
            <p><strong>Género:</strong> <span id="concert-genre"></span></p>
            <p><strong>Organizador:</strong> <span id="concert-organizer"></span></p>
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-success" id="reserve-btn">Hacer una Reserva</button>
                <button class="btn btn-primary" id="buy-btn">Comprar Entrada</button>
            </div>
        </div>
    </div>
</div>
<script>
    const concertData = <?php echo json_encode($concert); ?>;

    document.getElementById('concert-title').textContent = concertData.nomConcert;
    document.getElementById('concert-date').textContent = concertData.data;
    document.getElementById('concert-location').textContent = concertData.ubicacio;
    document.getElementById('concert-price').textContent = concertData.preu;
    document.getElementById('concert-aforo').textContent = concertData.aforament;
    document.getElementById('concert-genre').textContent = concertData.idGenere; 
    document.getElementById('concert-organizer').textContent = concertData.idUsuariOrganitzador;

    // Mostrar animación y redirigir a controlador de entradas: (esto quedar por acabar)
    document.getElementById('reserve-btn').addEventListener('click', function() {
        alert('Reserva realizada para el concierto: ' + concertData.nomConcert);

    });

    document.getElementById('buy-btn').addEventListener('click', function() {
        alert('Entrada comprada para el concierto: ' + concertData.nomConcert);
      
    });
</script>
