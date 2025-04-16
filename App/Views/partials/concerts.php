<?php require __DIR__ . '/partials/navbar_guest.php'; ?>

<main class="container">
    <h1>🎶 Próximos Conciertos</h1>

    <div class="concerts-grid">
        <?php
        // Simulación de conciertos en array (estático por ahora)
        $concerts = [
            [
                'title' => 'Rock Fest 2025',
                'date' => '20 de Junio 2025',
                'venue' => 'Sala Apolo, Barcelona',
                'image' => 'https://via.placeholder.com/300x200?text=Rock+Fest+2025'
            ],
            [
                'title' => 'Jazz Night',
                'date' => '5 de Julio 2025',
                'venue' => 'Teatro Real, Madrid',
                'image' => 'https://via.placeholder.com/300x200?text=Jazz+Night'
            ],
            [
                'title' => 'Indie Explosion',
                'date' => '12 de Agosto 2025',
                'venue' => 'WiZink Center, Madrid',
                'image' => 'https://via.placeholder.com/300x200?text=Indie+Explosion'
            ],
        ];

        // Recorrer los conciertos y mostrarlos
        foreach ($concerts as $concert): ?>
            <div class="concert-card">
                <img src="<?= $concert['image']; ?>" alt="<?= $concert['title']; ?>">
                <h2><?= $concert['title']; ?></h2>
                <p>📅 <?= $concert['date']; ?></p>
                <p>📍 <?= $concert['venue']; ?></p>
                <button>Comprar Entradas</button>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>

