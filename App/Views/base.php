<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TicketMonster</title>

    <link rel="stylesheet" href="/public/bootstrap-5.3.3/css/bootstrap.min.css">
    <script src="/public/bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>
</head>

<body class ="bg-light">
    <!-- Navbar -->
    <?php
    //antes de nada, comprobar si estamos autenticados
    use App\Core\Auth;

    require_once __DIR__ . '/partials/footer.php';
    // Incluir navbar según si el usuario está logueado
    if (Auth::isLogged()) {
        require_once __DIR__ . '/partials/navbar_user.php';
    } else {
        require_once __DIR__ . '/partials/navbar_guest.php';
    }
    ?>

    <div class="container mt-4">
        <?php //más adelante me interasará hacer componentes //include __DIR__ . "/partials/navbar.php"; 
        ?>

        <!-- Contenido dinámico -->
        <main>
            <?php
            require_once $view;
            ?>
        </main>

        <!-- Footer -->
        <?php //  include __DIR__ . "/partials/footer.php"; 
        ?>

    </div>
</body>

</html>