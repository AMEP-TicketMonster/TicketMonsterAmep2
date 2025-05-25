<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¿Qué es TicketMonster?</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .card-info {
            border-left: 6px solid #624DE3;
            border-radius: 12px;
            max-width: 900px;
            margin: auto;
            padding: 2.5rem;
            background-color: #fff;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #222;
        }

        .section-text {
            font-size: 1.05rem;
            color: #555;
        }

        .footer-social {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 10px;
        }

        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            font-size: 1.3rem;
            color: #444;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
        }

        .footer-social a:hover {
            background-color: #eee;
            color: #624DE3;
        }
    </style>
</head>
<body>

<!-- CONTENIDO -->
<div class="container my-5">
    <h1 class="text-center mb-5 fw-bold text-primary display-6">TicketMonster</h1>

    <div class="card card-info shadow-sm">
        <p class="section-text mb-4">📢 <strong class="text-dark">TicketMonster</strong> es una plataforma en línea donde puedes comprar entradas para conciertos, hacer reservas anticipadas y alquilar salas para eventos musicales.</p>

        <hr>

        <h4 class="section-title mt-3">🎟️ Compra y reserva de entradas</h4>
        <p class="section-text">Explora conciertos de diferentes géneros y ciudades. Puedes <strong>comprar entradas directamente</strong> o hacer una <strong>reserva temporal</strong> para no perder tu lugar.</p>

        <h4 class="section-title mt-4">🏟️ Alquiler de salas</h4>
        <p class="section-text">¿Eres organizador de eventos o parte de un grupo musical? También puedes <strong>alquilar salas</strong> para organizar tus propios conciertos.</p>

        <h4 class="section-title mt-4">💬 Valoraciones y opiniones</h4>
        <p class="section-text">Los asistentes pueden dejar comentarios y valoraciones sobre los conciertos, ayudando a otros usuarios a descubrir las mejores experiencias musicales.</p>

        <h4 class="section-title mt-4">🔐 Seguridad garantizada</h4>
        <p class="section-text">TicketMonster garantiza <strong>transacciones seguras</strong> con un sistema claro de gestión de entradas.</p>
    </div>
</div>

<!-- FOOTER -->
<footer class="text-center py-4 bg-white mt-5 border-top">
    <div class="footer-social">
        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
    </div>
    <small class="text-muted">© <?= date('Y') ?> TicketMonster. Todos los derechos reservados.</small>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
