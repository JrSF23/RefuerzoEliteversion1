<?php
require_once 'config.php';

// Consulta modificada para incluir el conteo de alumnos por tutor
$sql = "SELECT t.id_tutor, t.nombre_completo, t.contacto_tutor, 
               COUNT(a.id_alumno) as num_alumnos
        FROM tutores t
        LEFT JOIN alumnos a ON t.id_tutor = a.id_tutor
        GROUP BY t.id_tutor, t.nombre_completo, t.contacto_tutor
        ORDER BY t.id_tutor";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$tutores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Listado de Tutores</title>
    <link rel="stylesheet" href="../../css/estilosTabla.css" />
</head>

<body>

    <h1>Listado de Tutores</h1>

    <table class="striped-hover">
        <thead>
            <tr>
                <th>ID Tutor</th>
                <th>Nombre Completo</th>
                <th>Contacto Tutor</th>
                <th>Núm. Alumnos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tutores as $tutor): ?>
                <tr>
                    <td><?= htmlspecialchars($tutor['id_tutor']) ?></td>
                    <td><?= htmlspecialchars($tutor['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($tutor['contacto_tutor']) ?></td>
                    <td>
                        <span style="background-color: #3498db; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.9em;">
                            <?= htmlspecialchars($tutor['num_alumnos']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="editarTut.php?id=<?= $tutor['id_tutor'] ?>" style="margin-right: 10px; color: #2980b9; text-decoration: none;">Editar</a>
                        <form method="POST" action="eliminarTut.php" onsubmit="return confirm('¿Seguro que quieres eliminar este tutor?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $tutor['id_tutor'] ?>">
                            <button type="submit" style="color: white; background-color: #e74c3c; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="acciones-enlaces">
        <a href="../bienvenida.php" class="volver">⬅ Volver</a>
        <a href="insertTut.php" class="btn-volver">Añadir</a>
    </div>

</body>

</html>