<?php
require_once 'config.php';

$message = '';
$messageType = '';
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int) $_POST['id'];

        // Verificar si el alumno existe
        $checkSql = "SELECT COUNT(*) FROM materias WHERE id_materia = :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':id' => $id]);
        $exists = $checkStmt->fetchColumn();

        if ($exists) {
            // Eliminar alumno
            $sql = "DELETE FROM materias WHERE id_materia = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([':id' => $id])) {
                $message = 'Materia eliminada correctamente';
                $messageType = 'success';
                $redirect = true;
            } else {
                $message = 'Error al eliminar la materia';
                $messageType = 'error';
            }
        } else {
            $message = 'No existe una materia con ese ID';
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
    <title>Eliminar Materia</title>
    <link rel="stylesheet" href="../../css/styleWel.css">
</head>
<body>
    <!-- El modal se crea automáticamente por el JavaScript -->
    
    <!-- Scripts -->
    <script src="../../JS/modal.js"></script>
    <script>
        // Esperar a que el modal esté inicializado
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($message): ?>
            // Mostrar mensaje del servidor
            modalSystem.<?php echo $messageType; ?>(
                '<?php echo addslashes($message); ?>', 
                '<?php echo $messageType === 'success' ? 'Éxito' : 'Error'; ?>'<?php if ($redirect): ?>,
                function() {
                    window.location.href = 'selectMat.php';
                }<?php endif; ?>
            );
            <?php endif; ?>
        });
    </script>
</body>
</html>