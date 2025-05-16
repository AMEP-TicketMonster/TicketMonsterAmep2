<?php
$sales = $_SESSION['datosConcierto_Salas'];
$generes =  $_SESSION['datosConciert_Genero'];
$grups =  $_SESSION['datosConcierto_Grups'];
?>

<div class="container py-5">
  <div class="col-md-8 mx-auto">


    <div class="card shadow">
      <div class="card-header text-bg-primary text-center">
        <h4>🎤 Crear nuevo concierto</h4>
      </div>
      <div class="card-body">
        <form action="/crea-concert-backend" method="POST">
          <input type="text" class="form-control mb-3" name="nombre_concierto" placeholder="Nombre del concierto" required>

          <input type="date" class="form-control mb-3" name="fecha" required>

          <input type="time" class="form-control mb-3" name="hora" required>

          <select class="form-control mb-3" name="lugar" id="selector-salas" required>
            <option value="">Selecciona una sala</option>
            <!-- esto lo cargo con el javascript de más abajo-->
          </select>
          <select class="form-control mb-3" name="grupo_musical" id="selector-grups" required>
            <option value="">Selecciona una grupo</option>
            <!-- esto lo cargo con el javascript de más abajo-->
          </select>
          <input type="number" class="form-control mb-3" name="precio" placeholder="Precio (€)" step="0.01" required>

          <select class="form-control mb-3" name="genero" id="selector-genero" required>
            <option value="">Selecciona una género</option>
            <!-- esto lo cargo con el javascript de más abajo-->
          </select>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Crear concierto</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  let salas = JSON.parse('<?php echo addslashes($sales); ?>');
  console.log(salas)
  let grupos = JSON.parse('<?php echo addslashes($grups); ?>');
  console.log(grupos)
  let generos = JSON.parse('<?php echo addslashes($generes); ?>');
  console.log(generos)
  /*const salas = [
    { id: 1, nombre: "Sala Apolo" },
    { id: 2, nombre: "Razzmatazz" },
    { id: 3, nombre: "Teatro Barceló" }
  ];*/

  const selectorSalas = document.getElementById("selector-salas");
  const selectorGrupos = document.getElementById("selector-grups");
  const selectorGenero = document.getElementById("selector-genero");

  salas.forEach(sala => {
    const option = document.createElement("option");
    option.value = sala.idSala;
    option.textContent = sala.nom;
    selectorSalas.appendChild(option);
  });

  grupos.forEach(grup => {
    const option = document.createElement("option");
    option.value = grup.idGrup;
    option.textContent = grup.nomGrup;
    selectorGrupos.appendChild(option);
  });


    generos.forEach(genero => {
    const option = document.createElement("option");
    option.value = genero.idGenere;
    option.textContent = genero.nomGenere;
    selectorGenero.appendChild(option);
  });
</script>