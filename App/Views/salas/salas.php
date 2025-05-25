<style>
    .contenedor-salas {
    max-width: 900px;
    margin: 40px auto;
    font-family: 'Lexend', sans-serif;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 2.8rem;
    font-weight: 700;
    color: #2d5d2d;
}

/* Tabla con borde redondeado y fondo verdoso */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #e6f2e6;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 100, 0, 0.1);
    overflow: hidden;
}

th, td {
    text-align: center;
    padding: 14px 10px;
    border-bottom: 1px solid #c7d9c7;
    background-color: #f7fcf7;
}

th {
    background-color: #b8d8b8;
    color: #2d5d2d;
    font-weight: bold;
}
tr {
    transition: background-color 0.3s ease;
}
tr:hover {
    background-color: #d7f0d7; /* verde más marcado */
    transition: background-color 0.3s ease;
}

/* Botones: mismo tamaño, padding, fuente, borde y transición */
.btn-reservar, .btn-editar, .btn-cancelar {
    padding: 8px 20px;
    font-size: 14px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: inline-block;
    min-width: 100px; /* igual ancho mínimo para uniformidad */
    text-align: center;
    box-sizing: border-box;
    font-family: 'Lexend', sans-serif;
    transition: background-color 0.3s ease;
}

/* Colores */
.btn-reservar {
    background-color: #28a745;
    color: white;
}
.btn-reservar:hover {
    background-color: #218838;
}

.btn-editar {
    background-color: #17a2b8;
    color: white;
}
.btn-editar:hover {
    background-color: #138496;
}

.btn-cancelar {
    background-color: #f28b82;
    color: white;
}
.btn-cancelar:hover {
    background-color: #d95c56;
}

.mensaje {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
}

.mensaje.success {
    color: green;
}

.mensaje.error {
    color: red;
}

/* Separación entre el select y los botones Guardar / Cancelar */
form[action="/guardar-edicion"] select {
    margin-bottom: 10px;
}

form[action="/guardar-edicion"] button,
form[action="/guardar-edicion"] a {
    margin-top: 10px;
}
</style>

<div class="contenedor-salas">
    <h2>Salas disponibles</h2>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="mensaje success"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mensaje error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['salas_con_slots'])): ?>
        <table>
            <tr>
                <th>Sala</th>
                <th>Ciudad</th>
                <th>Capacidad</th>

                <th>Estado</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($_SESSION['salas_con_slots'] as $slot): ?>
                <tr>
                    <td><?= htmlspecialchars($slot['nom']) ?></td>
                    <td><?= htmlspecialchars($slot['ciutat']) ?></td>
                    <td><?= htmlspecialchars($slot['capacitat']) ?></td>
    
                    <td><?= $slot['idAssajos'] ? 'Reservada' : 'Libre' ?></td>
                    <td>
                        <?php if (!$slot['idAssajos']): ?>
                            <form method="post" action="/reservar-sala">
                                <input type="hidden" name="idSala" value="<?= $slot['idSala'] ?>">
                                <input type="hidden" name="idDataSala" value="<?= $slot['idDataSala'] ?>">
                                <button class="btn-reservar" type="submit">Reservar</button>
                            </form>
                        <?php elseif (isset($_SESSION['user']) && $_SESSION['user']['idUsuari'] === $slot['idUsuari']): ?>
                            <?php if (isset($_GET['editar']) && $_GET['editar'] == $slot['idAssajos']): ?>
                                <form method="post" action="/guardar-edicion">
                                    <input type="hidden" name="idAssajosEditar" value="<?= $slot['idAssajos'] ?>">
                                    <select name="nuevoIdDataSala" required>
                                        <?php foreach ($_SESSION['salas_con_slots'] as $nuevoSlot): ?>
                                            <?php if (!$nuevoSlot['idAssajos']): ?>
                                                <option value="<?= $nuevoSlot['idDataSala'] ?>">
                                                    <?= $nuevoSlot['dia'] ?> | <?= $nuevoSlot['hora_inici'] ?> - <?= $nuevoSlot['hora_fi'] ?> (<?= $nuevoSlot['nom'] ?>)
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn-reservar btn-editar">Guardar</button>
                                    <a href="/salas" class="btn-reservar btn-cancelar">Cancelar</a>
                                </form>
                            <?php else: ?>
                                <form method="post" action="/eliminar-reserva" style="display:inline;">
                                    <input type="hidden" name="idAssajos" value="<?= $slot['idAssajos'] ?>">
                                    <button class="btn-reservar btn-cancelar" type="submit">Cancelar</button>
                                </form>
                                <a href="/salas?editar=<?= $slot['idAssajos'] ?>" class="btn-reservar btn-editar">Editar</a>
                            <?php endif; ?>
                        <?php else: ?>
                            Reservada
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="mensaje error">No hay información de salas o slots.</p>
    <?php endif; ?>
