<?php
// gestionarRelaciones.php
require_once 'config.php';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'agregar':
                    if (!empty($_POST['id_profesor']) && !empty($_POST['id_materia'])) {
                        $sql = "INSERT INTO profesor_materia (id_profesor, id_materia) VALUES (:id_profesor, :id_materia)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id_profesor', $_POST['id_profesor']);
                        $stmt->bindParam(':id_materia', $_POST['id_materia']);
                        $stmt->execute();
                        $mensaje = "✅ Relación agregada correctamente.";
                    }
                    break;
                    
                case 'eliminar':
                    if (!empty($_POST['relacion_id'])) {
                        $sql = "DELETE FROM profesor_materia WHERE id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $_POST['relacion_id']);
                        $stmt->execute();
                        $mensaje = "✅ Relación eliminada correctamente.";
                    }
                    break;
            }
        } catch (PDOException $e) {
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    }
}

// Obtener todas las relaciones actuales
$relaciones = $pdo->query("
    SELECT 
        pm.id,
        p.nombre_completo as profesor,
        m.nombre_materia as materia,
        pm.fecha_asignacion
    FROM profesor_materia pm
    JOIN profesores p ON pm.id_profesor = p.id_profesor
    JOIN materias m ON pm.id_materia = m.id_materia
    ORDER BY p.nombre_completo, m.nombre_materia
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener profesores y materias para los selectores
$profesores = $pdo->query("SELECT id_profesor, nombre_completo FROM profesores ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
$materias = $pdo->query("SELECT id_materia, nombre_materia FROM materias ORDER BY nombre_materia")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Relaciones Profesor-Materia</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
    <style>
        .relaciones-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .relaciones-table th, .relaciones-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .relaciones-table th {
            background-color: #f2f2f2;
        }
        .btn-eliminar {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-eliminar:hover {
            background-color: #cc0000;
        }
        .agregar-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Gestionar Relaciones Profesor-Materia</h2>

        <?php if (isset($mensaje)) : ?>
            <p style="text-align: center; color: <?= str_starts_with($mensaje, '✅') ? 'green' : 'red' ?>;">
                <?= $mensaje ?>
            </p>
        <?php endif; ?>

        <!-- Formulario para agregar nueva relación -->
        <div class="agregar-form">
            <h3>Asignar Materia a Profesor</h3>
            <form method="POST">
                <input type="hidden" name="action" value="agregar">
                
                <label>Profesor:</label>
                <select name="id_profesor" required>
                    <option value="">Selecciona un profesor</option>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?= $profesor['id_profesor'] ?>"><?= htmlspecialchars($profesor['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Materia:</label>
                <select name="id_materia" required>
                    <option value="">Selecciona una materia</option>
                    <?php foreach ($materias as $materia): ?>
                        <option value="<?= $materia['id_materia'] ?>"><?= htmlspecialchars($materia['nombre_materia']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Asignar Materia</button>
            </form>
        </div>

        <!-- Tabla de relaciones actuales -->
        <h3>Relaciones Actuales</h3>
        <?php if (empty($relaciones)): ?>
            <p>No hay relaciones profesor-materia configuradas.</p>
        <?php else: ?>
            <table class="relaciones-table">
                <thead>
                    <tr>
                        <th>Profesor</th>
                        <th>Materia</th>
                        <th>Fecha Asignación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($relaciones as $relacion): ?>
                        <tr>
                            <td><?= htmlspecialchars($relacion['profesor']) ?></td>
                            <td><?= htmlspecialchars($relacion['materia']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($relacion['fecha_asignacion'])) ?></td>
                            <td>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta relación?')">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="relacion_id" value="<?= $relacion['id'] ?>">
                                    <button type="submit" class="btn-eliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a class="volver" href="insertarPanel.php">← Volver al panel de inserciones</a>
    </div>
</body>

</html>