<?php
require_once 'config.php';

// Consulta que cuenta automáticamente los alumnos por curso usando tabla intermedia
$sql = "SELECT 
            c.id_curso, 
            c.nombre_curso, 
            COUNT(ac.id_alumno) as cantidad_alumnos
        FROM cursos c
        LEFT JOIN alumnos_cursos ac ON c.id_curso = ac.id_curso
        GROUP BY c.id_curso, c.nombre_curso
        ORDER BY c.id_curso";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Listado de Cursos</title>
    <link rel="stylesheet" href="../../css/estilosTabla.css" />
</head>

<body>

    <h1>Listado de Cursos</h1>

    <table class="striped-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Curso</th>
                <th>Cantidad Alumnos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cursos as $curso): ?>
                <tr>
                    <td><?= htmlspecialchars($curso['id_curso']) ?></td>
                    <td><?= htmlspecialchars($curso['nombre_curso']) ?></td>
                    <td>
                        <span style="font-weight: bold; color: <?= $curso['cantidad_alumnos'] > 0 ? '#27ae60' : '#e74c3c' ?>;">
                            <?= $curso['cantidad_alumnos'] ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="eliminarCur.php" onsubmit="return confirm('¿Seguro que quieres eliminar este curso?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $curso['id_curso'] ?>">
                            <button type="submit" style="color: white; background-color: #e74c3c; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="acciones-enlaces">
        <a href="../bienvenida.php" class="volver">⬅ Volver</a>
        <a href="insertCur.php" class="btn-volver">Añadir</a>
    </div>

</body>

</html>