<?php include_once __DIR__ . '/../partials/navbar_user.php'; ?>

<style>
    .contenedor-salas {
        max-width: 900px;
        margin: 40px auto;
        font-family: Arial, sans-serif;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        background: white;
    }

    th, td {
        text-align: center;
        padding: 12px 8px;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:hover {
        background-color: #f9f9f9;
    }

    .btn-reservar {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-reservar:hover {
        background-color: #218838;
    }

    .btn-cancelar {
        background-color: #dc3545;
        color: white;
    }

    .btn-editar {
        background-color: #ffc107;
        color: black;
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
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($_SESSION['salas_con_slots'] as $slot): ?>
                <tr>
                    <td><?= htmlspecialchars($slot['nom']) ?></td>
                    <td><?= htmlspecialchars($slot['ciutat']) ?></td>
                    <td><?= htmlspecialchars($slot['capacitat']) ?></td>
                    <td><?= htmlspecialchars($slot['dia']) ?></td>
                    <td><?= htmlspecialchars($slot['hora_inici']) ?></td>
                    <td><?= htmlspecialchars($slot['hora_fi']) ?></td>
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
</div>
