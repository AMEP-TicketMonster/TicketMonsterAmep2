<?php
$concert = isset($_SESSION['concert']) ? $_SESSION['concert'] : null;
//var_dump($concert);
?>
<div class="container my-5">
    <h2 class="text-center mb-4" id="concert-title"></h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Fecha:</strong> <span id="concert-date-dia"></span> <span id="concert-date-hora"></span></p>
            <p><strong>Ubicación:</strong> (Sala <span id="concert-location"></span>)</p>
            <p><strong>Precio:</strong> <span id="concert-price"></span> €</p>
            <p><strong>Entradas disponibles:</strong> <span id="concert-aforo"></span></p>
            <p><strong>Género:</strong> <span id="concert-genre"></span></p>
            <p><strong>Organizador:</strong> <span id="concert-organizer"></span></p>
            <div class="d-flex justify-content-between mt-4">
                <form method="POST" action="/reserva" id="form-reserva">
                    <input type="hidden" name="idConcert" value="">
                    <button type="submit" class="btn btn-success">Hacer una Reserva</button>
                </form>
                <form method="POST" action="/compra-entrada-concert" id="form-compra">
                    <input type="hidden" name="idConcert" value="">
                    <button type="submit" class="btn btn-primary">Comprar Entrada</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const concertData = <?php echo json_encode($concert); ?>;

    document.getElementById('concert-title').textContent = concertData.nomConcert;
    document.getElementById('concert-date-dia').textContent = concertData.dia;
    document.getElementById('concert-date-hora').textContent = concertData.hora;
    document.getElementById('concert-location').textContent = concertData.idSala;
    document.getElementById('concert-price').textContent = concertData.preu;
    document.getElementById('concert-aforo').textContent = concertData.entrades_disponibles;
    document.getElementById('concert-genre').textContent = concertData.idGenere;
    document.getElementById('concert-organizer').textContent = concertData.idUsuari;
    
    document.querySelector('#form-reserva input[name="idConcert"]').value = concertData.idConcert;
    document.querySelector('#form-compra input[name="idConcert"]').value = concertData.idConcert;
  

</script>