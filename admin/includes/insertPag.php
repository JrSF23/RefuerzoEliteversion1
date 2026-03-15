<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['monto_pagar']) ||
        empty($_POST['id_tutor']) ||
        empty($_POST['id_alumno'])
    ) {
        $mensaje = "❌ Por favor, complete todos los campos.";
    } else {
        $sql = "INSERT INTO pagos (monto_pagar, id_tutor, id_alumno)
                VALUES (:monto_pagar, :id_tutor, :id_alumno)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':monto_pagar', $_POST['monto_pagar']);
        $stmt->bindParam(':id_tutor', $_POST['id_tutor']);
        $stmt->bindParam(':id_alumno', $_POST['id_alumno']);

        try {
            $stmt->execute();
            $mensaje = "✅ ¡Pago registrado correctamente!";
            header("Location: selectPag.php");
            exit();
        } catch (PDOException $e) {
            $mensaje = "❌ Error al registrar el pago: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Pago</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
</head>

<body>
    <div class="form-container">
        <h2>Registrar Nuevo Pago</h2>

        <?php if (isset($mensaje)) : ?>
            <p style="text-align: center; color: <?= str_starts_with($mensaje, '✅') ? 'green' : 'red' ?>;">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="monto_pago">Monto:</label>
            <input type="number" id="monto_pagar" name="monto_pagar" step="0.01" required>

            <label for="id_tutor">ID Tutor:</label>
            <input type="number" id="id_tutor" name="id_tutor" required>

            <label for="id_alumno">ID Alumno:</label>
            <input type="number" id="id_alumno" name="id_alumno" required>

            <button type="submit">Guardar Pago</button>
        </form>

        <a class="volver" href="../bienvenida.php">← Volver al panel de inserciones</a>
    </div>
</body>

</html>