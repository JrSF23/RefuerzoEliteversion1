<?php
require_once 'config.php';

$mensaje = '';

// Verificar si se pasó un ID por GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID de tutor no especificado.";
    exit;
}

$id = $_GET['id'];

// Obtener datos actuales del tutor
$stmt = $pdo->prepare("SELECT * FROM tutores WHERE id_tutor = ?");
$stmt->execute([$id]);
$tutor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tutor) {
    echo "Tutor no encontrado.";
    exit;
}

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['nombre_completo']) || empty($_POST['contacto_tutor'])) {
        $mensaje = "❌ Por favor, complete todos los campos.";
    } else {
        $sql = "UPDATE tutores SET nombre_completo = :nombre_completo, contacto_tutor = :contacto_tutor WHERE id_tutor = :id_tutor";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre_completo', $_POST['nombre_completo']);
        $stmt->bindParam(':contacto_tutor', $_POST['contacto_tutor']);
        $stmt->bindParam(':id_tutor', $id);

        try {
            $stmt->execute();
            $mensaje = "✅ ¡Tutor actualizado correctamente!";
            header("Location: selectTut.php");
            exit();
        } catch (PDOException $e) {
            $mensaje = "❌ Error al actualizar el tutor: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Tutor</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
</head>

<body>
    <div class="form-container">
        <h2>✏️ Editar Tutor</h2>

        <?php if (!empty($mensaje)) : ?>
            <p style="text-align: center; color: <?= str_starts_with($mensaje, '✅') ? 'green' : 'red' ?>;">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= $tutor['id_tutor'] ?>">

            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" value="<?= htmlspecialchars($tutor['nombre_completo']) ?>" required>

            <label for="contacto_tutor">Contacto:</label>
            <input type="text" id="contacto_tutor" name="contacto_tutor" value="<?= htmlspecialchars($tutor['contacto_tutor']) ?>" required>

            <button type="submit">Actualizar Tutor</button>
        </form>

        <a class="volver" href="selectTut.php">← Volver al listado de tutores</a>
    </div>
</body>

</html>