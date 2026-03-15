<?php
require_once 'config.php';

$message = '';
$messageType = '';
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int) $_POST['id'];

        // Verificar si el curso existe
        $checkSql = "SELECT COUNT(*) FROM cursos WHERE id_curso = :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':id' => $id]);
        $exists = $checkStmt->fetchColumn();

        if ($exists) {
            try {
                // Verificar si tiene registros relacionados
                $dependencies = [];
                
                // Verificar materias
                $materiasStmt = $pdo->prepare("SELECT COUNT(*) FROM materias WHERE id_curso = :id");
                $materiasStmt->execute([':id' => $id]);
                $materiasCount = $materiasStmt->fetchColumn();
                if ($materiasCount > 0) {
                    $dependencies[] = "$materiasCount materia(s)";
                }
                
                // Verificar alumnos asignados
                $alumnosStmt = $pdo->prepare("SELECT COUNT(*) FROM alumnos_cursos WHERE id_curso = :id");
                $alumnosStmt->execute([':id' => $id]);
                $alumnosCount = $alumnosStmt->fetchColumn();
                if ($alumnosCount > 0) {
                    $dependencies[] = "$alumnosCount alumno(s)";
                }

                if (!empty($dependencies)) {
                    // Si tiene dependencias, preguntar si quiere eliminar en cascada
                    if (isset($_POST['force_delete']) && $_POST['force_delete'] === 'yes') {
                        // ELIMINAR EN CASCADA
                        $pdo->beginTransaction();
                        
                        try {
                            // 1. Eliminar alumnos del curso
                            if ($alumnosCount > 0) {
                                $deleteAlumnosStmt = $pdo->prepare("DELETE FROM alumnos_cursos WHERE id_curso = :id");
                                $deleteAlumnosStmt->execute([':id' => $id]);
                            }
                            
                            // 2. Eliminar materias del curso
                            if ($materiasCount > 0) {
                                $deleteMateriasStmt = $pdo->prepare("DELETE FROM materias WHERE id_curso = :id");
                                $deleteMateriasStmt->execute([':id' => $id]);
                            }
                            
                            // 3. Finalmente eliminar el curso
                            $deleteCursoStmt = $pdo->prepare("DELETE FROM cursos WHERE id_curso = :id");
                            $deleteCursoStmt->execute([':id' => $id]);
                            
                            $pdo->commit();
                            
                            $message = "Curso eliminado correctamente junto con $materiasCount materia(s) y $alumnosCount asignación(es) de alumno(s)";
                            $messageType = 'success';
                            $redirect = true;
                            
                        } catch (Exception $e) {
                            $pdo->rollback();
                            $message = 'Error al eliminar el curso y sus dependencias: ' . $e->getMessage();
                            $messageType = 'error';
                        }
                        
                    } else {
                        // Mostrar advertencia sobre las dependencias
                        $dependencyText = implode(' y ', $dependencies);
                        $message = "No se puede eliminar el curso porque tiene $dependencyText asociadas. ¿Desea eliminar el curso y todas sus dependencias?";
                        $messageType = 'warning';
                        // No redirigir, mostrar formulario de confirmación
                    }
                } else {
                    // No tiene dependencias, eliminar normalmente
                    $sql = "DELETE FROM cursos WHERE id_curso = :id";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([':id' => $id])) {
                        $message = 'Curso eliminado correctamente';
                        $messageType = 'success';
                        $redirect = true;
                    } else {
                        $message = 'Error al eliminar el curso';
                        $messageType = 'error';
                    }
                }
                
            } catch (PDOException $e) {
                // Capturar errores de integridad referencial
                if ($e->getCode() === '23000') {
                    $message = 'No se puede eliminar el curso porque tiene registros relacionados (materias, alumnos, etc.). Elimine primero los registros relacionados o use la eliminación forzada.';
                    $messageType = 'error';
                } else {
                    $message = 'Error en la base de datos: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
        } else {
            $message = 'No existe un curso con ese ID';
            $messageType = 'error';
        }
    } else {
        $message = 'ID inválido';
        $messageType = 'error';
    }
} else {
    $message = 'Método no permitido';
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Curso</title>
    <link rel="stylesheet" href="../../css/styleWel.css">
    <style>
        .warning-container {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .warning-title {
            color: #856404;
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .warning-icon {
            font-size: 1.5em;
            margin-right: 10px;
        }
        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <?php if ($messageType === 'warning' && isset($_POST['id'])): ?>
    <div class="warning-container">
        <div class="warning-title">
            <span class="warning-icon">⚠️</span>
            Confirmación de eliminación
        </div>
        <p><?php echo $message; ?></p>
        <div class="action-buttons">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
                <input type="hidden" name="force_delete" value="yes">
                <button type="submit" class="btn btn-danger">
                    🗑️ Sí, eliminar todo
                </button>
            </form>
            <a href="selectCur.php" class="btn btn-secondary">
                ❌ Cancelar
            </a>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Scripts -->
    <script src="../../JS/modal.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($message && $messageType !== 'warning'): ?>
            // Solo mostrar modal si no es advertencia (la advertencia se muestra en HTML)
            modalSystem.<?php echo $messageType; ?>(
                '<?php echo addslashes($message); ?>', 
                '<?php echo $messageType === 'success' ? 'Éxito' : 'Error'; ?>'<?php if ($redirect): ?>,
                function() {
                    window.location.href = 'selectCur.php';
                }<?php endif; ?>
            );
            <?php endif; ?>
        });
    </script>
</body>
</html>