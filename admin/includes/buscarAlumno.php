<?php
require_once 'config.php';
$materias = $pdo->query("SELECT id_materia, nombre_materia FROM materias")->fetchAll(PDO::FETCH_KEY_PAIR);
$resultados = [];
$terminoSeguro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $termino = trim($_POST['buscar']);
    $terminoSeguro = htmlspecialchars($termino);

    if (!empty($termino)) {
        $sql = "SELECT * FROM alumnos 
                WHERE id_alumno = :termino 
                   OR nombre_completo LIKE :nombre";
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':termino', $termino, is_numeric($termino) ? PDO::PARAM_INT : PDO::PARAM_STR);
        $stmt->bindValue(':nombre', '%' . $termino . '%', PDO::PARAM_STR);

        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda</title>
    <link rel="stylesheet" href="../../css/estilosTabla.css">
</head>

<body>
    <h1>Resultado de la búsqueda</h1>

    <?php if (!empty($resultados)): ?>
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
                <?php foreach ($resultados as $alumno): ?>
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
                                <button type="submit" style="color: white; background-color: #e74c3c; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>❌ No se encontraron resultados para: <strong><?= $terminoSeguro ?></strong></p>
    <?php endif; ?>

    <a href="../bienvenida.php" class="volver">← Volver al panel</a>
</body>

</html>