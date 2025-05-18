<?php
$concerts = $_SESSION['concerts'] ?? [];
$sales = $_SESSION['datosConcierto_Salas'];
$generes = $_SESSION['datosConciert_Genero'];
$grups = $_SESSION['datosConcierto_Grups'];
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Próximos Conciertos</h2>

    <form method="GET" action="/filtroConciertos" class="mb-4">
        <div class="row g-2">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar concierto o grupo" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>

            <div class="col-6 col-md-2">
                <select class="form-select" name="genere" id="selector-genero" required>
                    <option value="">Género</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select class="form-select" name="sala" id="selector-salas" required>
                    <option value="">Sala</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select class="form-select" name="grupo_musical" id="selector-grups" required>
                    <option value="">Grupo</option>
                </select>
            </div>

            <div class="col-6 col-md-1">
                <select name="entradas" class="form-select">
                    <option value="">Entradas</option>
                    <option value="disponibles" <?= ($_GET['entradas'] ?? '') === 'disponibles' ? 'selected' : '' ?>>Disponibles</option>
                </select>
            </div>

            <div class="col-12 col-md-2">
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

    const salas = JSON.parse(`<?= addslashes($sales) ?>`);
    const grupos = JSON.parse(`<?= addslashes($grups) ?>`);
    const generos = JSON.parse(`<?= addslashes($generes) ?>`);

    const selectorSalas = document.getElementById("selector-salas");
    const selectorGrupos = document.getElementById("selector-grups");
    const selectorGenero = document.getElementById("selector-genero");

    salas.forEach(sala => {
        const option = document.createElement("option");
        option.value = sala.idSala;
        option.textContent = sala.nom;
        selectorSalas.appendChild(option);
    });

    grupos.forEach(grup => {
        const option = document.createElement("option");
        option.value = grup.idGrup;
        option.textContent = grup.nomGrup;
        selectorGrupos.appendChild(option);
    });

    generos.forEach(genero => {
        const option = document.createElement("option");
        option.value = genero.idGenere;
        option.textContent = genero.nomGenere;
        selectorGenero.appendChild(option);
    });
</script>
