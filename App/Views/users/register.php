<?php
//queda por settear en el backend!
$retry_registration = isset($_SESSION['bad_registration_data']) ? $_SESSION['bad_registration_data'] : null;
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="container">
        <!-- Tarjeta del login -->
        <div class="card shadow-sm p-4 mx-auto" style="max-width: 100%; width: 100%; max-width: 500px;">

            <div class="text-center mb-5"><br>
                <h5 class="fw-bold">Crear una cuenta</h5>
                <br>
                <p>Introduce tus datos</p>
            </div>

            <form method="POST" action="/register">
                <div class="mb-4">
                    <input name="nom" class="form-control" id="nom" placeholder="Nombre" required>
                </div>
                <div class="mb-4">
                    <input name="cognoms" class="form-control" id="nom" placeholder="Apellidos" required>
                </div>
                <div class="mb-4">
                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                </div>
                <div class="mb-4">
                    <input type="password" class="form-control" name="contrasenya" id="password" placeholder="Contraseña" required>
                </div>
                <div class="mb-4">

                    <input type="password" class="form-control" name="confirma_contrasenya" id="password" placeholder="Repetir contraseña" required>
                </div>

                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary" style="background-color:#624DE3;">Registrarse</button>
                </div>
            </form>
        </div>
        <br>

    </div>
</div>

<script>
    let datos = <?php echo json_encode($retry_login); ?>;
    if (datos) {
        alert("Datos incorrectos");
        <?php $_SESSION['bad_login_data'] = false; ?>
    }
</script>

































<script>
    //pasar php->json a js
    let datos = <?php echo json_encode($retry_registration); ?>;
    if (datos) { //si ha habido otro intento de registro fallido
        alert("Datos incorrectos");
        //solo mostrará el mensaje una vez cuando se produzca el fallo y no lo volverá a mostrar hasta que vuelva a haber otro intento
        <?php $_SESSION['bad_registration_data'] = false; ?>
    }
</script>