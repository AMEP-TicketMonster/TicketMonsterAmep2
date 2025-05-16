<?php
$concerts = $_SESSION['concerts'] ?? [];
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Próximos Conciertos</h2>

    <!-- Filtros -->
    <form method="GET" action="/filtroConciertos" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar concierto o grupo" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>

            <div class="col-md-2">
                <select name="genre" class="form-select">
                    <option value="">Género</option>
                    <!-- Habría que cargar los options según generos en la BD -->
                    <option value="rock" <?= ($_GET['genre'] ?? '') === 'rock' ? 'selected' : '' ?>>Rock</option>
                    <option value="pop" <?= ($_GET['genre'] ?? '') === 'pop' ? 'selected' : '' ?>>Pop</option>
                    <option value="jazz" <?= ($_GET['genre'] ?? '') === 'jazz' ? 'selected' : '' ?>>Jazz</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="sala" class="form-select">
                    <option value="">Sala</option>
                    <option value="Apolo" <?= ($_GET['sala'] ?? '') === 'Apolo' ? 'selected' : '' ?>>Apolo</option>
                    <option value="Razzmatazz" <?= ($_GET['sala'] ?? '') === 'Razzmatazz' ? 'selected' : '' ?>>Razzmatazz</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="entradas" class="form-select">
                    <option value="">Entradas</option>
                    <option value="disponibles" <?= ($_GET['entradas'] ?? '') === 'disponibles' ? 'selected' : '' ?>>Disponibles</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" style="background-color:#624DE3;">Filtrar</button>
            </div>
        </div>
    </form>

    <div id="concert-container" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4"></div>
</div>

<script>
    const concerts = JSON.parse(`<?= json_encode(array_values($concerts)) ?>`);
    const container = document.getElementById('concert-container');

    if (concerts.length === 0) {
        container.innerHTML = '<div class="col text-center">No se encontraron conciertos.</div>';
    } else {
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
                            <i class="bi bi-person"></i> ${concert.grup}
                        </h6>
                        <p class="card-text mb-1"><i class="bi bi-calendar-event"></i> Fecha: ${concert.dia}</p>
                        <p class="card-text mb-1"><i class="bi bi-geo-alt"></i> Ubicación: ${concert.ubicacio}</p>
                        <p class="card-text mb-1"><i class="bi bi-tag"></i> Género: ${concert.Genere}</p>
                        <p class="card-text mb-1"><i class="bi bi-building"></i> Sala: ${concert.sala}</p>
                        
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
    }
</script>
