<?php
require_once 'config.php';

// 1. Verificar si se envió el ID por GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de pago no especificado.");
}

$id = $_GET['id'];

// 2. Obtener datos del pago
$sql = "SELECT * FROM pagos WHERE id_pago = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$pago = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pago) {
    die("Pago no encontrado.");
}

// 3. Obtener tutores y alumnos para los selects
$tutores = $pdo->query("SELECT id_tutor, nombre_completo FROM tutores")->fetchAll(PDO::FETCH_ASSOC);
$alumnos = $pdo->query("SELECT id_alumno, nombre_completo FROM alumnos")->fetchAll(PDO::FETCH_ASSOC);

// 4. Si se envió el formulario, procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monto = $_POST['monto_pagar'];
    $fecha = $_POST['fecha_pago'];
    $id_tutor = $_POST['id_tutor'];
    $id_alumno = $_POST['id_alumno'];

    // Validación básica
    if (empty($monto) || empty($fecha) || empty($id_tutor) || empty($id_alumno)) {
        echo "<p style='color:red;'>Todos los campos son obligatorios.</p>";
    } else {
        $sql = "UPDATE pagos SET monto_pagar = ?, fecha_pago = ?, id_tutor = ?, id_alumno = ? WHERE id_pago = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$monto, $fecha, $id_tutor, $id_alumno, $id]);
        echo "<p style='color:green;'>Pago actualizado correctamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Pago</title>
    <link rel="stylesheet" href="../../css/formularioPag.css"> <!-- opcional -->
</head>

<body>

    <h1>Modificar datos del Pago</h1>

    <form method="POST">
        <label>Monto a Pagar:</label><br>
        <input type="number" step="0.01" name="monto_pagar" value="<?= htmlspecialchars($pago['monto_pagar']) ?>" required><br><br>

        <label>Fecha de Pago:</label><br>
        <input type="date" name="fecha_pago" value="<?= htmlspecialchars($pago['fecha_pago']) ?>" required><br><br>

        <label>Seleccionar Tutor:</label><br>
        <select name="id_tutor" required>
            <option value="">--Selecciona un tutor--</option>
            <?php foreach ($tutores as $tutor): ?>
                <option value="<?= $tutor['id_tutor'] ?>" <?= $tutor['id_tutor'] == $pago['id_tutor'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tutor['nombre_completo']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Seleccionar Alumno:</label><br>
        <select name="id_alumno" required>
            <option value="">--Selecciona un alumno--</option>
            <?php foreach ($alumnos as $alumno): ?>
                <option value="<?= $alumno['id_alumno'] ?>" <?= $alumno['id_alumno'] == $pago['id_alumno'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($alumno['nombre_completo']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Guardar Cambios</button>
        <a href="selectPag.php" style="margin-left: 10px;">Cancelar</a>
    </form>

</body>

</html>