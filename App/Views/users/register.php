<?php
//queda por settear en el backend!
$retry_registration = isset($_SESSION['bad_registration_data']) ? $_SESSION['bad_registration_data'] : null;
?>

<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <form action="/register" method="POST">
        <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="formFirstName">Nom</label>
            <input type="text" name="nom" id="formFirstName" class="form-control" />
        </div>

        <!-- Last Name input -->
        <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="formLastName">Cognom</label>
            <input type="text" name="cognoms" id="formLastName" class="form-control" />

        </div>

        <!-- Email input -->
        <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="formEmail">Email</label>
            <input type="email" name="email" id="formEmail" class="form-control" />

        </div>

        <!-- Password input -->
        <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="formPassword">Contrasenya</label>
            <input type="password" name="contrasenya" id="formPassword" class="form-control" />

        </div>

        <!-- Confirm Password input -->
        <div data-mdb-input-init class="form-outline mb-4">
            <label class="form-label" for="formConfirmPassword">Confirma Contrasenya</label>
            <input type="password" name="confirma_contrasenya" id="formConfirmPassword" class="form-control" />
        </div>

        <!-- Submit button -->
        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Registra</button>

    </form>
</div>

<script>
    //pasar php->json a js
    let datos = <?php echo json_encode($retry_registration); ?>;
    if (datos) { //si ha habido otro intento de registro fallido
        alert("Datos incorrectos");
        //solo mostrará el mensaje una vez cuando se produzca el fallo y no lo volverá a mostrar hasta que vuelva a haber otro intento
        <?php $_SESSION['bad_registration_data'] = false; ?>
    }
</script>