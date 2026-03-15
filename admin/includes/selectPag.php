<?php
require_once 'config.php';

$sql = "SELECT id_pago, monto_pagar, fecha_pago, id_tutor, id_alumno FROM pagos"; // Corrige aquí el nombre de la tabla
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Listado de Pagos</title>
    <link rel="stylesheet" href="../../css/estilosTabla.css" />
</head>

<body>

    <h1>Listado de Pagos</h1>

    <table class="striped-hover">
        <thead>
            <tr>
                <th>ID Pago</th>
                <th>Monto Pagar</th>
                <th>Fecha Pago</th>
                <th>ID Tutor</th>
                <th>ID Alumno</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pagos as $pago): ?>
                <tr>
                    <td><?= htmlspecialchars($pago['id_pago']) ?></td>
                    <td><?= htmlspecialchars($pago['monto_pagar']) ?></td>
                    <td><?= htmlspecialchars($pago['fecha_pago']) ?></td>
                    <td><?= htmlspecialchars($pago['id_tutor']) ?></td>
                    <td><?= htmlspecialchars($pago['id_alumno']) ?></td>
                    <td>
                        <a href="editarPag.php?id=<?= $pago['id_pago'] ?>" style="margin-right: 10px; color: #2980b9; text-decoration: none;">Editar</a>
                        <form method="POST" action="eliminarPag.php" onsubmit="return confirm('¿Seguro que quieres eliminar este pago?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $pago['id_pago'] ?>">
                            <button type="submit" style="color: white; background-color: #e74c3c; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="acciones-enlaces">
        <a href="../bienvenida.php" class="volver">⬅ Volver</a>
        <a href="insertPag.php" class="btn-volver">Añadir</a>
    </div>

</body>

</html>