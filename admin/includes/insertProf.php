<?php
// insertProf.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['nombre_completo']) ||
        empty($_POST['contacto_profesor']) ||
        empty($_POST['domicilio_profesor'])
    ) {
        $mensaje = "❌ Por favor, complete todos los campos obligatorios.";
    } else {
        try {
            // Iniciar transacción
            $pdo->beginTransaction();
            
            // Insertar el profesor (sin numero_alumnos)
            $sql = "INSERT INTO profesores (nombre_completo, contacto_profesor, domicilio_profesor)
                    VALUES (:nombre_completo, :contacto_profesor, :domicilio_profesor)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_completo', $_POST['nombre_completo']);
            $stmt->bindParam(':contacto_profesor', $_POST['contacto_profesor']);
            $stmt->bindParam(':domicilio_profesor', $_POST['domicilio_profesor']);
            $stmt->execute();
            
            // Obtener el ID del profesor recién insertado
            $id_profesor = $pdo->lastInsertId();
            
            // Si se seleccionaron materias, crear las relaciones
            if (!empty($_POST['materias'])) {
                $sql_relacion = "INSERT INTO profesor_materia (id_profesor, id_materia) 
                                VALUES (:id_profesor, :id_materia)";
                $stmt_relacion = $pdo->prepare($sql_relacion);
                
                foreach ($_POST['materias'] as $id_materia) {
                    $stmt_relacion->bindParam(':id_profesor', $id_profesor);
                    $stmt_relacion->bindParam(':id_materia', $id_materia);
                    $stmt_relacion->execute();
                }
            }
            
            // Confirmar transacción
            $pdo->commit();
            
            $mensaje = "✅ ¡Profesor registrado correctamente!";
            header("Location: selectProf.php");
            exit();
        } catch (PDOException $e) {
            // Revertir transacción en caso de error
            $pdo->rollBack();
            $mensaje = "❌ Error al registrar al profesor: " . $e->getMessage();
        }
    }
}

// Obtener materias disponibles
$materias = $pdo->query("SELECT id_materia, nombre_materia FROM materias ORDER BY nombre_materia")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Profesor</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
    <style>
        .materias-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }
        .materia-checkbox {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Registrar Nuevo Profesor</h2>

        <?php if (isset($mensaje)) : ?>
            <p style="text-align: center; color: <?= str_starts_with($mensaje, '✅') ? 'green' : 'red' ?>;">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" required>

            <label for="contacto_profesor">Contacto:</label>
            <input type="text" id="contacto_profesor" name="contacto_profesor" required>

            <label for="domicilio_profesor">Domicilio:</label>
            <input type="text" id="domicilio_profesor" name="domicilio_profesor" required>

            <label>Materias que impartirá (opcional):</label>
            <div class="materias-container">
                <?php if (empty($materias)): ?>
                    <p>No hay materias disponibles. <a href="insertMat.php">Crear primera materia</a></p>
                <?php else: ?>
                    <?php foreach ($materias as $materia): ?>
                        <label class="materia-checkbox">
                            <input type="checkbox" name="materias[]" value="<?= $materia['id_materia'] ?>">
                            <?= htmlspecialchars($materia['nombre_materia']) ?>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="submit">Guardar Profesor</button>
        </form>

        <a class="volver" href="../bienvenida.php">← Volver al panel de inserciones</a>
    </div>
</body>

</html>