<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que el campo no esté vacío
    if (empty($_POST['nombre_curso'])) {
        $mensaje = "❌ Por favor, complete el nombre del curso.";
    } else {
        // Solo insertamos el nombre del curso, la cantidad se calcula automáticamente
        $sql = "INSERT INTO cursos (nombre_curso) VALUES (:nombre_curso)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre_curso', $_POST['nombre_curso']);

        try {
            $stmt->execute();
            $mensaje = "✅ ¡El nuevo curso ha sido añadido correctamente!";
            header("Location: selectCur.php");
            exit();
        } catch (PDOException $e) {
            $mensaje = "❌ Error al agregar el curso: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Insertar Curso</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
</head>

<body>

    <div class="form-container">
        <h2>Registrar Nuevo Curso</h2>

        <?php if (isset($mensaje)) : ?>
            <p style="text-align: center; color: <?= str_starts_with($mensaje, '✅') ? 'green' : 'red' ?>;">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="nombre_curso">Nombre del Curso:</label>
            <input type="text" id="nombre_curso" name="nombre_curso" required>

            <div style="background-color: #e8f4fd; padding: 10px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #3498db;">
                <p style="margin: 0; font-size: 0.9em; color: #2c3e50;">
                    <strong>ℹ️ Nota:</strong> La cantidad de alumnos se calculará automáticamente basándose en las inscripciones reales.
                </p>
            </div>

            <button type="submit">Guardar Curso</button>
        </form>

        <a class="volver" href="../bienvenida.php">← Volver al panel de inserciones</a>
    </div>

</body>

</html>