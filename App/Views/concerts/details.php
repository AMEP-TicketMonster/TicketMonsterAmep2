<?php
$concert = isset($_SESSION['concert']) ? $_SESSION['concert'] : null;
//var_dump($concert);
?>
<div class="container my-5">
    <h2 class="text-center mb-4" id="concert-title"></h2>
    <div class="card shadow-sm">
        <div class="card-body">
        <p><strong>Artista:</strong> <span id="concert-artist"></span></p>
            <p><strong>Fecha:</strong> <span id="concert-date-dia"></span> <span id="concert-date-hora"></span></p>
            <p><strong>Ubicación:</strong> <span id="concert-location"> </span></p>
            <p><strong>Precio:</strong> <span id="concert-price"></span> €</p>
            <p><strong>Entradas disponibles:</strong> <span id="concert-aforo"></span></p>
            <p><strong>Género:</strong> <span id="concert-genre"></span></p>
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
    document.getElementById('concert-artist').textContent = concertData.nomGrup;
    document.getElementById('concert-title').textContent = concertData.nomConcert;
    document.getElementById('concert-date-dia').textContent = concertData.dia;
    document.getElementById('concert-date-hora').textContent = concertData.hora_inici;
    document.getElementById('concert-location').textContent = concertData.nom + " ("+concertData.ciutat +")";
    document.getElementById('concert-price').textContent = concertData.preu;
    //aquí falta revisar cómo sacar este dato
    document.getElementById('concert-aforo').textContent = concertData.entrades_disponibles;
    document.getElementById('concert-genre').textContent = concertData.nomGenere;
    
    document.querySelector('#form-reserva input[name="idConcert"]').value = concertData.idEntrada;
    document.querySelector('#form-compra input[name="idConcert"]').value = concertData.idEntrada;
  

</script>