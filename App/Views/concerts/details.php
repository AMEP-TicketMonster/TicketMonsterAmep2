<?php
$concert = $_SESSION['concert'] ?? null;
$usuarioCompro = $_SESSION['usuarioCompro'] ?? false;
$usuarioValoro = $_SESSION['usuarioValoro'] ?? false;
$valoraciones = $_SESSION['valoraciones'] ?? [];
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

    <!-- FORMULARIO DE VALORACIÓN -->
    <?php if ($usuarioCompro && !$usuarioValoro): ?>
        <div class="card mt-5 p-4">
            <h4 class="mb-3">Valora este concierto</h4>
            <form method="POST" action="/valorar">
                <input type="hidden" name="concierto_id" value="<?= htmlspecialchars($concert['idConcert']) ?>">

                <div class="mb-3">
                    <label for="comentario" class="form-label">Comentario:</label>
                    <textarea name="comentario" id="comentario" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Puntuación:</label><br>
                    <div class="estrellas">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="puntuacion" value="<?= $i ?>" id="estrella<?= $i ?>" required>
                            <label for="estrella<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning">Enviar valoración</button>
            </form>
        </div>
    <?php elseif ($usuarioValoro): ?>
        <div class="alert alert-success mt-4">
            Ya has valorado este concierto. ¡Gracias por tu opinión!
        </div>
    <?php endif; ?>

    <!-- LISTADO DE VALORACIONES -->
    <?php if (!empty($valoraciones)): ?>
        <div class="mt-5">
            <h4>Opiniones de otros asistentes:</h4>
            <?php foreach ($valoraciones as $valoracion): ?>
                <div class="card my-3 p-3">
                    <div>
                        <strong><?= htmlspecialchars($valoracion['nombre_usuario']) ?></strong> 
                        — <?= htmlspecialchars($valoracion['data']) ?>
                    </div>
                    <div class="mb-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $valoracion['puntuacio'] ? '★' : '☆' ?>
                        <?php endfor; ?>
                    </div>
                    <p><?= htmlspecialchars($valoracion['comentari']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- IMAGEN DINÁMICA -->
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



