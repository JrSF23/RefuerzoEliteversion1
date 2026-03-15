<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alumno</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f8;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            background: white;
            max-width: 600px;
            margin: 0 auto;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #555;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: inline-block;
            margin-left: 15px;
            color: #555;
            text-decoration: none;
            font-size: 16px;
            vertical-align: middle;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body>
    <h1>Editar Alumno ID </h1>
    <form action="editarAlumno.php" method="POST">
        <label>Nombre Completo:<br>
            <input type="text" name="nombre_completo" value="<?= htmlspecialchars($alumno['nombre_completo']) ?>" required>
        </label><br><br>

        <label>Contacto:<br>
            <input type="text" name="contacto_alumno" value="<?= htmlspecialchars($alumno['contacto_alumno']) ?>" required>
        </label><br><br>

        <label>Domicilio:<br>
            <input type="text" name="domicilio_alumno" value="<?= htmlspecialchars($alumno['domicilio_alumno']) ?>" required>
        </label><br><br>

        <label>Colegio:<br>
            <input type="text" name="colegio_alumno" value="<?= htmlspecialchars($alumno['colegio_alumno']) ?>" required>
        </label><br><br>

        <label>Cantidad Materias:<br>
            <input type="number" name="cantidad_materias" value="<?= htmlspecialchars($alumno['cantidad_materias']) ?>" required>
        </label><br><br>

        <label>ID Tutor:<br>
            <input type="number" name="id_tutor" value="<?= htmlspecialchars($alumno['id_tutor']) ?>" required>
        </label><br><br>

        <label>ID Materia:<br>
            <input type="number" name="id_materia" value="<?= htmlspecialchars($alumno['id_materia']) ?>" required>
        </label><br><br>

        <label>ID Profesor:<br>
            <input type="number" name="id_profesor" value="<?= htmlspecialchars($alumno['id_profesor']) ?>" required>
        </label><br><br>

        <button type="submit">Guardar Cambios</button>
        <a href="selectAlum.php" style="margin-left: 20px;">Cancelar</a>
    </form>
</body>

</html>