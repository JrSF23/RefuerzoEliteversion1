<?php
// insertMat.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['nombre_materia']) || empty($_POST['coeficiente']) || empty($_POST['id_curso'])) {
        $mensaje = "❌ Por favor, complete todos los campos.";
    } else {
        try {
            // Iniciar transacción
            $pdo->beginTransaction();
            
            // Insertar la materia
            $sql = "INSERT INTO materias (nombre_materia, coeficiente, id_curso)
                    VALUES (:nombre_materia, :coeficiente, :id_curso)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_materia', $_POST['nombre_materia']);
            $stmt->bindParam(':coeficiente', $_POST['coeficiente']);
            $stmt->bindParam(':id_curso', $_POST['id_curso']);
            $stmt->execute();
            
            // Obtener el ID de la materia recién insertada
            $id_materia = $pdo->lastInsertId();
            
            // Si se seleccionó un profesor, crear la relación
            if (!empty($_POST['id_profesor'])) {
                $sql_relacion = "INSERT INTO profesor_materia (id_profesor, id_materia) 
                                VALUES (:id_profesor, :id_materia)";
                $stmt_relacion = $pdo->prepare($sql_relacion);
                $stmt_relacion->bindParam(':id_profesor', $_POST['id_profesor']);
                $stmt_relacion->bindParam(':id_materia', $id_materia);
                $stmt_relacion->execute();
            }
            
            // Confirmar transacción
            $pdo->commit();
            
            $mensaje = "✅ ¡La materia ha sido añadida correctamente!";
            header("Location: selectMat.php");
            exit();
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $pdo->rollBack();
            $mensaje = "❌ Error al agregar la materia: " . $e->getMessage();
        }
    }
}

// Obtener profesores disponibles
$profesores = $pdo->query("SELECT id_profesor, nombre_completo FROM profesores")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Insertar Materia</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
</head>

<body>
    <div class="form-container">
        <h2>Registrar Nueva Materia</h2>

        <?php if (isset($mensaje)) : ?>
            <p style="text-align: center; color: <?= str_starts_with($mensaje, '✅') ? 'green' : 'red' ?>;">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="nombre_materia">Nombre de la Materia:</label>
            <input type="text" id="nombre_materia" name="nombre_materia" required>

            <label for="coeficiente">Coeficiente:</label>
            <input type="number" id="coeficiente" name="coeficiente" step="0.01" required>

            <label for="id_curso">ID Curso:</label>
            <input type="number" id="id_curso" name="id_curso" required>
            
            <label>Profesor (opcional):</label>
            <select name="id_profesor">
                <option value="">Asignar profesor más tarde</option>
                <?php foreach ($profesores as $profesor): ?>
                    <option value="<?= $profesor['id_profesor'] ?>"><?= htmlspecialchars($profesor['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Guardar Materia</button>
        </form>

        <a class="volver" href="../bienvenida.php">← Volver al panel de inserciones</a>
    </div>
</body>

</html>