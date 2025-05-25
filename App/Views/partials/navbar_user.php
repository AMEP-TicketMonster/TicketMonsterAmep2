<?php $saldo = $_SESSION['user']['saldo'] ?? null; ?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">LOGO TICKETMONSTER</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end text-center text-lg-start" id="navbarNav">

            <ul class="navbar-nav me-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="/conciertos">CONCIERTOS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/salas">SALAS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/info">INFORMACIÓN</a>
                </li>
            </ul>
            <div class="d-flex flex-column flex-lg-row align-items-center gap-2 w-auto w-sm-100 w-lg-auto">
                <?php if ($saldo !== null): ?>
                    <a href="/saldo" class="btn btn-outline-success w-100 w-lg-auto">
                        <?= $saldo ?>€
                    </a>
                <?php endif; ?>
                <a href="/profile" class="btn btn-primary w-100 w-lg-auto">Perfil</a>
                <a href="/logout" class="btn btn-danger w-100 w-lg-auto">Logout</a>
            </div>
        </div>
    </div>
</nav>