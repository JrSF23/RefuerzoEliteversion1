<?php
require_once 'config.php';

// Modificar la consulta para contar los alumnos reales suscritos a cada profesor
$sql = "SELECT 
            p.id_profesor, 
            p.nombre_completo, 
            p.contacto_profesor, 
            p.domicilio_profesor,
            COUNT(a.id_alumno) as alumnos_suscritos
        FROM profesores p
        LEFT JOIN alumnos a ON p.id_profesor = a.id_profesor
        GROUP BY p.id_profesor, p.nombre_completo, p.contacto_profesor, p.domicilio_profesor
        ORDER BY p.nombre_completo";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Listado Profesores</title>
    <link rel="stylesheet" href="../../css/estilosTabla.css" />
    <style>
        .btn-info {
            color: white;
            background-color: #3498db;
            border: none;
            padding: 7px 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-info:hover {
            background-color: #2980b9;
        }
        
        .btn-eliminar {
            color: white;
            font-size: 14px;
            background-color: #e74c3c;
            border: none;
            padding: 7px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn-eliminar:hover {
            background-color: #c0392b;
        }
        
        .acciones-container {
            display: flex;
            gap: 5px;
            align-items: center;
        }
    </style>
</head>

<body>

    <h1>Listado de Profesores</h1>

    <table class="striped-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Contacto</th>
                <th>Domicilio</th>
                <th>Alumnos Suscritos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($profesores as $profesor): ?>
                <tr>
                    <td><?= htmlspecialchars($profesor['id_profesor']) ?></td>
                    <td><?= htmlspecialchars($profesor['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($profesor['contacto_profesor']) ?></td>
                    <td><?= htmlspecialchars($profesor['domicilio_profesor']) ?></td>
                    <td><?= htmlspecialchars($profesor['alumnos_suscritos']) ?></td>
                    <td>
                        <div class="acciones-container">
                            <a href="infoProf.php?id=<?= $profesor['id_profesor'] ?>" class="btn-info"> Info </a>
                            <form method="POST" action="eliminarProf.php" onsubmit="return confirm('¿Seguro que quieres eliminar este profesor?');" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $profesor['id_profesor'] ?>">
                                <button type="submit" class="btn-eliminar"> Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="acciones-enlaces">
        <a href="../bienvenida.php" class="volver">⬅ Volver</a>
        <a href="insertProf.php" class="btn-volver">Añadir</a>
    </div>

</body>

</html>