<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_antiguo = $_POST['id_profesor_antiguo'];
    $reasignaciones = $_POST['reasignaciones'] ?? [];

    // Validación básica
    if (!$id_antiguo || !is_numeric($id_antiguo) || empty($reasignaciones)) {
        die("Datos incompletos.");
    }

    // Reasignar cada materia
    foreach ($reasignaciones as $id_materia => $nuevo_profesor) {
        if (is_numeric($id_materia) && is_numeric($nuevo_profesor)) {
            $stmt = $pdo->prepare("UPDATE materias SET id_profesor = :nuevo WHERE id_materia = :materia");
            $stmt->execute(['nuevo' => $nuevo_profesor, 'materia' => $id_materia]);
        }
    }

    // Eliminar profesor
    $stmt = $pdo->prepare("DELETE FROM profesores WHERE id_profesor = :id");
    if ($stmt->execute(['id' => $id_antiguo])) {
        header("Location: selectProf.php");
        exit;
    } else {
        echo "Error al eliminar el profesor.";
    }
} else {
    echo "Método no permitido.";
}
