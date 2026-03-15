<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['nombre_completo']) || empty($_POST['contacto_tutor'])) {
        $mensaje = "❌ Por favor, complete todos los campos.";
    } else {
        $sql = "INSERT INTO tutores (nombre_completo, contacto_tutor)
                VALUES (:nombre_completo, :contacto_tutor)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre_completo', $_POST['nombre_completo']);
        $stmt->bindParam(':contacto_tutor', $_POST['contacto_tutor']);

        try {
            $stmt->execute();
            $mensaje = "✅ ¡Tutor registrado correctamente!";
            header("Location: selectTut.php");
            exit();
        } catch (PDOException $e) {
            $mensaje = "❌ Error al registrar al tutor: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Tutor</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
</head>

<body>
    <div class="form-container">
        <h2>Registrar Nuevo Tutor</h2>

        <?php if (isset($mensaje)) : ?>
            <p style="text-align: center; color: <?= str_starts_with($mensaje, '✅') ? 'green' : 'red' ?>;">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" required>

            <label for="contacto_tutor">Contacto:</label>
            <input type="text" id="contacto_tutor" name="contacto_tutor" required>

            <button type="submit">Guardar Tutor</button>
        </form>

        <a class="volver" href="../bienvenida.php">← Volver al panel de inserciones</a>
    </div>
</body>

</html>