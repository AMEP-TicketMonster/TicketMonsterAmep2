<?php
$usuarios = json_decode($_SESSION['usuaris'], true); // Asegúrate de que $_SESSION['usuaris'] es un string JSON
?>


  <div class="container">
    <h2 class="mb-4">Administrar Roles de Usuario</h2>

    <form method="POST" action="/guardar_roles">
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Email</th>
            <th>Rol</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $i => $usuario): ?>
            <tr>
              <td><?= $usuario['idUsuari'] ?></td>
              <td><?= $usuario['nom'] ?></td>
              <td><?= $usuario['cognom'] ?></td>
              <td><?= $usuario['email'] ?></td>
              <td>
                <input type="hidden" name="usuarios[<?= $i ?>][idUsuari]" value="<?= $usuario['idUsuari'] ?>">
                <select name="usuarios[<?= $i ?>][idRol]" class="form-select form-select-sm">
                  <option value="1" <?= $usuario['idRol'] == 1 ? 'selected' : '' ?>>Usuario</option>
                  <option value="3" <?= $usuario['idRol'] == 3 ? 'selected' : '' ?>>Administrador</option>
                </select>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </form>
  </div>

