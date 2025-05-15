<?php
$saldo = $_SESSION['user']['saldo'] ?? null;
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">LOGO TICKETMONSTER</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="/conciertos">CONCIERTOS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/salas">SALAS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/info">INFORMACIÓN</a>
                </li>

                <?php if ($saldo !== null): ?>
                    <li class="nav-item">
                        <a href="/saldo" class="btn btn-outline-success ms-3">
                            Saldo: €<?= number_format($saldo, 2) ?>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="/profile" class="btn btn-primary ms-2">Perfil</a>
                </li>
                <li class="nav-item">
                    <a href="/logout" class="btn btn-danger ms-3">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
