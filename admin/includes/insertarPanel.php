<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Inserciones</title>
    <link rel="stylesheet" href="../../css/estilosPanel.css"> <!-- Asegúrate de que este CSS exista -->
    <style>
        /* Puedes poner este estilo directamente si aún no tienes el archivo CSS */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #47855b;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .panel {
            background-color: #64a377;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 400px;
            text-align: center;
        }

        h1 {
            color: green;
            margin-bottom: 20px;
        }

        .btn-link {
            display: block;
            background-color: #00a86b;
            color: white;
            padding: 12px;
            border-radius: 6px;
            text-decoration: none;
            margin: 10px 0;
            transition: background-color 0.3s;
        }

        .btn-link:hover {
            background-color: #bcf4cb;
        }

        .volver {
            display: block;
            text-align: center;
            color: rgb(0, 255, 60);
            text-decoration: none;
            margin-top: 24px;
            font-size: 1.1em;
        }

        .volver:hover {
            text-decoration: underline;
            color: green;
        }
    </style>
</head>

<body>

    <div class="panel">
        <h1>Panel de Inserciones</h1>

        <a class="btn-link" href="insertAlum.php">➕ Insertar Alumno</a>
        <a class="btn-link" href="insertTut.php">➕ Insertar Tutor</a>
        <a class="btn-link" href="insertProf.php">➕ Insertar Profesor</a>
        <a class="btn-link" href="insertCur.php">➕ Insertar Curso</a>
        <a class="btn-link" href="insertMat.php">➕ Insertar Materia</a>
        <a class="btn-link" href="insertPag.php">➕ Insertar Pago</a>
    </div>

    <a href="../bienvenida.php" class="volver">⬅ Volver a Inicio</a>


</body>

</html>