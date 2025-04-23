<?php
//echo json_decode($_SESSION['user']);

use App\Controllers\EntradaController;

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

$entrades = isset($_SESSION['entrades']) ? $_SESSION['entrades'] : null;
//var_dump($entrades);

?>



<p id="userInfo"></p><br>
<p id="concerts"></p>


<script>
    //pasar php->json a js
    let datosUser = <?php echo json_encode($user); ?>;
    //let datosConcerts <?php echo $concerts ?>
    document.getElementById("userInfo").innerHTML = `
            idUsuari: ${datosUser.idUsuari} <br>
            Nom: ${datosUser.nom} <br>
            Email: ${datosUser.email} <br>
            Contrasenya: ${datosUser.contrasenya} <br>
        `;
</script>