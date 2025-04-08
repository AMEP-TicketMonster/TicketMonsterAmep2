<!-- Esto es un código sphagetti, así que queda para cambiar -->
<!-- está sacado de aquí: 
  https://mdbootstrap.com/docs/standard/extended/login/
-->
<?php
$retry_login = isset($_SESSION['bad_login_data']) ? $_SESSION['bad_login_data'] : null;
?>





<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
  <form action="/login" method="POST">
    <!-- Email input -->
    <div data-mdb-input-init class="form-outline mb-4">
      <label class="form-label" for="form2Example1">Email</label>
      <input type="email" name="email" id="form2Example1" class="form-control" />

    </div>

    <!-- Password input -->
    <div data-mdb-input-init class="form-outline mb-4">
      <label class="form-label" for="form2Example2">Contrasenya</label>
      <input type="password" name="password" id="form2Example2" class="form-control" />
    </div>
    <br>
    <!-- 2 column grid layout for inline styling -->
    <!--
    <div class="row mb-4">
      <div class="col d-flex justify-content-center">
       
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" id="form2Example31" checked />
          <label class="form-check-label" for="form2Example31"> Remember me </label>
        </div>
      </div>

      <div class="col">
       Simple link 
        <a href="#!">Forgot password?</a>
      </div>
    </div>
  -->
    <!-- Submit button -->
    <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Log in</button>

    <!-- Register buttons -->
    <div class="text-center">
      <p>Crea un compte? <a href="/register">Registra</a></p>
    </div>
  </form>
</div>

<script>
  //pasar php->json a js
  let datos = <?php echo json_encode($retry_login); ?>;
  if (datos) { //si ha habido otro intento de sesió fallido
    alert("Datos incorrectos");
    //solo mostrará el mensaje una vez cuando se produzca e fallo y no lo volverá a mostrar hasta que vuelva a haber otro intento
    <?php $_SESSION['bad_login_data'] = false; ?>
  }
</script>