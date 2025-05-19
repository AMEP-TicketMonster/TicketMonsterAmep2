<?php
$concert = $_SESSION['concert'] ?? null;
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
                <div class="d-flex justify-content-between mt-4 flex-wrap gap-2">
                    <form method="POST" action="/reserva-entrada-concert">
                        <input type="hidden" name="idEntrada" value="<?= htmlspecialchars($concert['idEntrada']) ?>">
                        <button type="submit" class="btn btn-success w-100">Hacer una Reserva</button>
                    </form>

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
</script>



