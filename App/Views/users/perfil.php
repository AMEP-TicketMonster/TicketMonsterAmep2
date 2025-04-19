
    <?php $user = isset($_SESSION['user']) ? $_SESSION['user'] : null; ?>




<?php
$retry_login = isset($_SESSION['bad_login_data']) ? $_SESSION['bad_login_data'] : null;
?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="container">
        <!-- Tarjeta del login -->
        <div class="card shadow-sm p-4 mx-auto" style="max-width: 100%; width: 100%; max-width: 500px;">

            <div class="text-center mb-5">
                <i class="bi bi-person-circle fs-1 text-primary mb-3"></i>
                <br><br>
                <h5 class="fw-bold">Perfil</h5>
            </div>

            <form method="POST" action="/edit-user">
                <div class="mb-4">
                    <input name="nom" class="form-control" id="nom" placeholder="Introduce tu nombre" required>
                </div>

                <div class="mb-4">
                    <input name="cognoms" class="form-control" id="cognoms" placeholder="Introduce tus apellidos" required>
                </div>
                <div class="mb-4">
                    <input name="email" class="form-control" id="email" placeholder="Introduce tus correo electronico" required>
                </div>
                <div class="d-grid mb-2">
                    <button type="submit" class="btn btn-primary" style="background-color:#624DE3;">Guardar cambios</button>
                </div>
            </form>
        </div>
        <br>
        <div class="text-center mt-3">

            <small class="text-muted">Eliminar la cuenta? :(</small>
            <br><br>
            <form action="/delete-user" method="POST" onsubmit="return confirmDelete()">
                <input type="hidden" name="idUsuari" value="<?php echo $user['idUsuari']; ?>">
                
                <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
            </form>
        </div>

    </div>
</div>

<script>
    let datos = <?php echo json_encode($user); ?>;
    
    document.getElementById("nom").value = datos.nom;  
    document.getElementById("cognoms").value = datos.cognom; 
    document.getElementById("email").value = datos.email;

    console.log(datos.nom); 
</script>