<?php
require_once 'config.php';

$sql = "SELECT id_materia, nombre_materia, coeficiente, id_curso FROM materias";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Listado de Materias</title>
    <link rel="stylesheet" href="../../css/estilosTabla.css" />
</head>

<body>

    <h1>Listado de Materias</h1>

    <table class="striped-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Materia</th>
                <th>Coeficiente</th>
                <th>ID Curso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materias as $materia): ?>
                <tr>
                    <td><?= htmlspecialchars($materia['id_materia']) ?></td>
                    <td><?= htmlspecialchars($materia['nombre_materia']) ?></td>
                    <td><?= htmlspecialchars($materia['coeficiente']) ?></td>
                    <td><?= htmlspecialchars($materia['id_curso']) ?></td>
                    <td>
                        <form method="POST" action="eliminarMat.php" onsubmit="return confirm('¿Seguro que quieres eliminar esta materia?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $materia['id_materia'] ?>">
                            <button type="submit" style="color: white; background-color: #e74c3c; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="acciones-enlaces">
        <a href="../bienvenida.php" class="volver">⬅ Volver</a>
        <a href="insertMat.php" class="btn-volver">Añadir</a>
    </div>

</body>

</html>