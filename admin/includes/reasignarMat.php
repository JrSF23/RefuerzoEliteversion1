<?php
require_once 'config.php';

$id_profesor = $_GET['id'] ?? null;
if (!$id_profesor || !is_numeric($id_profesor)) {
    die("ID de profesor no válido.");
}

// Obtener nombre del profesor
$stmt = $pdo->prepare("SELECT nombre_completo FROM profesores WHERE id_profesor = :id");
$stmt->execute(['id' => $id_profesor]);
$profesor = $stmt->fetch();

if (!$profesor) {
    die("Profesor no encontrado.");
}

// Obtener materias del profesor
$stmt = $pdo->prepare("SELECT id_materia, nombre_materia FROM materias WHERE id_profesor = :id");
$stmt->execute(['id' => $id_profesor]);
$materias = $stmt->fetchAll();

// Obtener lista de otros profesores
$stmt = $pdo->prepare("SELECT id_profesor, nombre_completo FROM profesores WHERE id_profesor != :id");
$stmt->execute(['id' => $id_profesor]);
$otrosProfesores = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reasignar Materias</title>
    <link rel="stylesheet" href="../../css/estilosReasignar.css">
</head>

<body>
    <h2>Reasignar materias del profesor: <?= htmlspecialchars($profesor['nombre_completo']) ?></h2>

    <form method="POST" action="guardarReasig.php">
        <input type="hidden" name="id_profesor_antiguo" value="<?= $id_profesor ?>">
        <?php foreach ($materias as $materia): ?>
            <p>
                Materia: <strong><?= htmlspecialchars($materia['nombre_materia']) ?></strong><br>
                Reasignar a:
                <select name="reasignaciones[<?= $materia['id_materia'] ?>]" required>
                    <option value="">Seleccione un nuevo profesor</option>
                    <?php foreach ($otrosProfesores as $otro): ?>
                        <option value="<?= $otro['id_profesor'] ?>"><?= htmlspecialchars($otro['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <hr>
        <?php endforeach; ?>
        <button type="submit">Guardar reasignaciones y eliminar profesor</button>
    </form>
    <br>
    <a href="selectProf.php">Cancelar y volver</a>
</body>

</html>