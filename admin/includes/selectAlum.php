<?php
require_once 'config.php';
// Obtener todas las materias para mapear ID => Nombre
$materias = $pdo->query("SELECT id_materia, nombre_materia FROM materias")->fetchAll(PDO::FETCH_KEY_PAIR);

if (!empty($_POST['materias_seleccionadas'])) {
    $materia = $_POST['materias_seleccionadas'];

    $sql = "SELECT id_alumno, nombre_completo, contacto_alumno, domicilio_alumno, 
            colegio_alumno, cantidad_materias, materias_seleccionadas, id_tutor, id_profesor 
            FROM alumnos 
            WHERE materias_seleccionadas LIKE :materia";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['materia' => '%' . $materia . '%']);
} else {
    $sql = "SELECT id_alumno, nombre_completo, contacto_alumno, domicilio_alumno, 
            colegio_alumno, cantidad_materias, materias_seleccionadas, id_tutor, id_profesor 
            FROM alumnos";

    $stmt = $pdo->query($sql);
}

$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Alumnos</title>
    <link rel="stylesheet" href="../../css/estilosTabla.css" />
</head>

<body>
    <h1>Listado de Alumnos</h1>
    <table class="striped-hover">
        <thead>
            <tr>
                <th>ID Alumno</th>
                <th>Nombre Completo</th>
                <th>Contacto</th>
                <th>Domicilio</th>
                <th>Colegio</th>
                <th>Cant. Materias</th>
                <th>Mat.Seleccionadas</th>
                <th>ID Tutor</th>
                <th>ID Profesor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($alumnos as $alumno): ?>
                <tr>
                    <td><?= htmlspecialchars($alumno['id_alumno']) ?></td>
                    <td><?= htmlspecialchars($alumno['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($alumno['contacto_alumno']) ?></td>
                    <td><?= htmlspecialchars($alumno['domicilio_alumno']) ?></td>
                    <td><?= htmlspecialchars($alumno['colegio_alumno']) ?></td>
                    <td><?= htmlspecialchars($alumno['cantidad_materias']) ?></td>
                    <td>
                        <?php
                            $ids = explode(',', $alumno['materias_seleccionadas']);
                            $nombres = [];
                            foreach ($ids as $id) {
                                $id = trim($id);
                                if (isset($materias[$id])) {
                                    $nombres[] = $materias[$id];
                                }
                            }
                            echo htmlspecialchars(implode(', ', $nombres));
                        ?>
                    </td>
                    <td><?= htmlspecialchars($alumno['id_tutor']) ?></td>
                    <td><?= htmlspecialchars($alumno['id_profesor']) ?></td>
                    <td class="acciones">
                        <a style="color: #007BFF; text-decoration:none" href="editarAlumno.php?id=<?= $alumno['id_alumno'] ?>">Editar</a>
                        <form action="eliminarAlumno.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este alumno?');">
                            <input type="hidden" name="id" value="<?= $alumno['id_alumno'] ?>">
                            <button type="submit" style="color: white; background-color: #e74c3c; border: none; padding: 5px 10px; border-radius: 3px;margin-left:10px; cursor: pointer;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="acciones-enlaces">
        <a href="../bienvenida.php" class="volver">⬅ Volver</a>
        <a href="insertAlum.php" class="btn-volver">Añadir</a>
    </div>
    <script src="../../js/app.js"></script>
</body>

</html>