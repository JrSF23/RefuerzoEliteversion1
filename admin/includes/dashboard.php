<?php
require_once 'config.php'; // Asegúrate de que este archivo contiene la conexión PDO correcta

// Funciones para contar registros de tablas
function contarRegistros($pdo, $tabla)
{
    $sql = "SELECT COUNT(*) AS total FROM $tabla";
    $stmt = $pdo->query($sql); // query es seguro aquí porque no se usan datos de usuario
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Contadores
$totalAlumnos = contarRegistros($pdo, 'alumnos');
$totalProfesores = contarRegistros($pdo, 'profesores');
$totalTutores = contarRegistros($pdo, 'tutores');
$totalMaterias = contarRegistros($pdo, 'materias');
$totalCursos = contarRegistros($pdo, 'cursos');
$totalPagos = contarRegistros($pdo, 'pagos');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Totales</title>
    <link rel="stylesheet" href="../../css/estilosTabla.css">
    <style>
        .resumen-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            padding: 20px;
            border-radius: 10px;
            background-color: #f0f8ff;
            box-shadow: 0 2px 8px green;
            text-align: center;
        }

        .card h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: green
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            color: green;
        }
        h1{
            color: #00ff80;
        }
        
    </style>
</head>

<body>
    <h1>Resumen General del Centro</h1>

    <div class="resumen-container">
        <div class="card">
            <h2>Alumnos</h2>
            <p><?= $totalAlumnos ?></p>
        </div>
        <div class="card">
            <h2>Profesores</h2>
            <p><?= $totalProfesores ?></p>
        </div>
        <div class="card">
            <h2>Tutores</h2>
            <p><?= $totalTutores ?></p>
        </div>
        <div class="card">
            <h2>Materias</h2>
            <p><?= $totalMaterias ?></p>
        </div>
        <div class="card">
            <h2>Cursos</h2>
            <p><?= $totalCursos ?></p>
        </div>
        <div class="card">
            <h2>Pagos</h2>
            <p><?= $totalPagos ?></p>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <a href="../bienvenida.php" class="volver">← Volver al panel</a>
    </div>
</body>

</html>