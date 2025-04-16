<div class="container mt-4">
    <h2>Mi Perfil</h2>
    <br>
    <?php $user = isset($_SESSION['user']) ? $_SESSION['user'] : null; ?>
    <div class="card">
        <div class="card-header">
            Información de usuario
        </div>
        <div class="card-body">
            <p class="card-text">idUsuari:</p><h3 id ="user-id"></h3>
            <p class="card-text">Nom:</p><h3 id="user-nom"></h3>
            <p class="card-text">Correo electrónico:</p><h3 id ="user-email"></h3>
            <br>
            <form action="/delete-user" method="POST" onsubmit="return confirmDelete()">
                <input type="hidden" name="idUsuari" value="<?php echo $user['idUsuari']; ?>">
                <button type="submit" class="btn btn-danger">Eliminar Cuenta</button>
            </form>
        </div>
    </div>
</div>
<script>
    let datos = <?php echo json_encode($user); ?>;
    document.getElementById("user-nom").innerHTML = datos.nom + " " + datos.cognom;
    document.getElementById("user-email").innerHTML = datos.email;
    document.getElementById("user-id").innerHTML = datos.idUsuari;
    console.log(datos.nom);
</script>