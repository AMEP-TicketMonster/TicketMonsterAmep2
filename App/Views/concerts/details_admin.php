<?php
$concert = $_SESSION['concert'] ?? null;
$valoracions = $_SESSION['valoracions'];
?>

<div class="container my-5">
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($_SESSION['mensaje']);
            unset($_SESSION['mensaje']); ?>
        </div>
    <?php endif; ?>

    <h2 class="text-center mb-4"><?= htmlspecialchars($concert['nomConcert'] ?? 'Concierto') ?></h2>

    <div class="card shadow-sm text-center">
        <div id="banner-placeholder"></div>

        <div class="card-body">
            <p><strong>Artista:</strong> <?= htmlspecialchars($concert['nomGrup']) ?></p>
            <p><strong>Fecha:</strong> <?= htmlspecialchars($concert['dia']) ?></p>
            <p><strong>Hora:</strong> <?= htmlspecialchars($concert['hora_inici']) ?></p>
            <p><strong>Ubicación:</strong> <?= htmlspecialchars($concert['nom']) ?> (<?= htmlspecialchars($concert['ciutat']) ?>)</p>
            <p><strong>Precio:</strong> <?= htmlspecialchars($concert['preu']) ?> €</p>
            <p><strong>Entradas disponibles:</strong> <?= htmlspecialchars($concert['entrades_disponibles']) ?></p>
            <p><strong>Género:</strong> <?= htmlspecialchars($concert['nomGenere']) ?></p>

            <?php if (!empty($concert['idEntrada'])): ?>
                <div class="d-flex justify-content-center mt-4 flex-wrap gap-2">
                    <!--
                No ha dado tiempo :(
                    <form method="POST" action="/reserva-entrada-concert">
                        <input type="hidden" name="idEntrada" value="<?= htmlspecialchars($concert['idEntrada']) ?>">
                        <button type="submit" class="btn btn-success w-100">Hacer una Reserva</button>
                    </form>
            -->
                    <form method="POST" action="/compra-entrada-concert">
                        <input type="hidden" name="idEntrada" value="<?= htmlspecialchars($concert['idEntrada']) ?>">
                        <button type="submit" class="btn btn-primary w-100">Comprar Entrada</button>
                    </form>
                </div>
            <?php else: ?>
                <p class="mt-3 text-danger"><strong>No hay entradas disponibles para este concierto.</strong></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="container my-5">
    <h3 class="text-center mb-4">Valoraciones de conciertos anteriores</h3>
    <div id="valoracions-container" class="row g-3 justify-content-center"></div>
</div>


<script>
    const concert = <?= json_encode($concert, JSON_UNESCAPED_UNICODE) ?>;

    if (concert.imatgeURL) {
        const placeholder = document.getElementById("banner-placeholder");
        const bannerDiv = document.createElement("div");
        bannerDiv.className = "banner-container";

        const img = document.createElement("img");
        img.src = concert.imatgeURL;
        img.alt = "Imagen del concierto";
        img.className = "concert-banner";

        bannerDiv.appendChild(img);
        placeholder.appendChild(bannerDiv);
    }



    //Esto es para las valoraciones ;)
   const valoracions = <?= json_encode($valoracions, JSON_UNESCAPED_UNICODE) ?>;
const valoracionsContainer = document.getElementById("valoracions-container");

if (valoracions && valoracions.length > 0) {
    valoracions.forEach(v => {
        const card = document.createElement("div");
        card.className = "col-md-6 col-lg-4";

        const puntuacio = v.puntuacio || 0;
        const estrellesPlenes = '★'.repeat(puntuacio);
        const estrellesBuides = '☆'.repeat(5 - puntuacio);
        const estrellesHTML = `<span class="estrelles">${estrellesPlenes}${estrellesBuides}</span>`;

        card.innerHTML = `
            <div class="card h-100 shadow p-3 valoracio-card position-relative">
                <form method="POST" action="/eliminar-valoracio" class="delete-form">
                    <input type="hidden" name="idValoracio" value="${v.idValoracio}">
                    <button type="submit" class="btn btn-sm btn-danger btn-delete" title="Eliminar valoración">X</button>
                </form>
                <h5 class="card-title mb-2">${v.nom} ${v.cognom}</h5>
                <p class="mb-1"><strong>Puntuación:</strong> ${estrellesHTML}</p>
                <p class="mb-1"><strong>Comentario:</strong> ${v.comentari}</p>
                <p class="text-muted small">Fecha: ${v.data}</p>
            </div>
        `;

        valoracionsContainer.appendChild(card);
    });
} else {
    valoracionsContainer.innerHTML = '<p class="text-muted text-center">No hay valoraciones disponibles para este concierto.</p>';
}

</script>