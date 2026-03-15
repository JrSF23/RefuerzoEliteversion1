<?php
require_once 'config.php';

// Obtener tutores
$tutores = $pdo->query("SELECT id_tutor, nombre_completo FROM tutores")->fetchAll(PDO::FETCH_ASSOC);

// Obtener materias
$materias = $pdo->query("SELECT id_materia, nombre_materia FROM materias")->fetchAll(PDO::FETCH_ASSOC);

// Obtener profesores
$profesores = $pdo->query("SELECT id_profesor, nombre_completo FROM profesores")->fetchAll(PDO::FETCH_ASSOC);

// Obtener cursos
$cursos = $pdo->query("SELECT id_curso, nombre_curso FROM cursos")->fetchAll(PDO::FETCH_ASSOC);

$mensaje = '';

// Verificar si se pasó un ID por GET
if (!isset($_GET['id'])) {
    echo "ID de alumno no especificado.";
    exit;
}

$id = $_GET['id'];

// Obtener datos del alumno
$sql = "SELECT * FROM alumnos WHERE id_alumno = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$alumno) {
    echo "Alumno no encontrado.";
    exit;
}

// Obtener materias actuales del alumno
$sql_materias = "SELECT id_materia FROM alumno_materias WHERE id_alumno = ?";
$stmt_materias = $pdo->prepare($sql_materias);
$stmt_materias->execute([$id]);
$materias_actuales = $stmt_materias->fetchAll(PDO::FETCH_COLUMN);

// Obtener cursos actuales del alumno
$sql_cursos = "SELECT id_curso FROM alumnos_cursos WHERE id_alumno = ?";
$stmt_cursos = $pdo->prepare($sql_cursos);
$stmt_cursos->execute([$id]);
$cursos_actuales = $stmt_cursos->fetchAll(PDO::FETCH_COLUMN);

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['nombre_completo']) ||
        empty($_POST['contacto_alumno']) ||
        empty($_POST['domicilio_alumno']) ||
        empty($_POST['colegio_alumno']) ||
        empty($_POST['cantidad_materias']) ||
        empty($_POST['materias_seleccionadas']) ||
        empty($_POST['cursos_seleccionados']) ||
        empty($_POST['id_tutor']) ||
        empty($_POST['id_profesor'])
    ) {
        $mensaje = "❌ Por favor, complete todos los campos.";
    } else {
        // Validar que la cantidad de materias coincida con las seleccionadas
        $materias_seleccionadas = $_POST['materias_seleccionadas'];
        $cantidad_materias = intval($_POST['cantidad_materias']);
        
        if (count($materias_seleccionadas) !== $cantidad_materias) {
            $mensaje = "❌ La cantidad de materias seleccionadas no coincide con la cantidad especificada.";
        } else {
            try {
                // Comenzar transacción
                $pdo->beginTransaction();
                
                // Actualizar datos del alumno
                $sql = "UPDATE alumnos SET 
                        nombre_completo = :nombre_completo, 
                        contacto_alumno = :contacto_alumno, 
                        domicilio_alumno = :domicilio_alumno, 
                        colegio_alumno = :colegio_alumno, 
                        cantidad_materias = :cantidad_materias, 
                        materias_seleccionadas = :materias_seleccionadas, 
                        id_tutor = :id_tutor, 
                        id_profesor = :id_profesor 
                        WHERE id_alumno = :id_alumno";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre_completo' => $_POST['nombre_completo'],
                    ':contacto_alumno' => $_POST['contacto_alumno'],
                    ':domicilio_alumno' => $_POST['domicilio_alumno'],
                    ':colegio_alumno' => $_POST['colegio_alumno'],
                    ':cantidad_materias' => $cantidad_materias,
                    ':materias_seleccionadas' => implode(',', $materias_seleccionadas),
                    ':id_tutor' => $_POST['id_tutor'],
                    ':id_profesor' => $_POST['id_profesor'],
                    ':id_alumno' => $id
                ]);
                
                // Eliminar materias actuales del alumno
                $sql_delete_materias = "DELETE FROM alumno_materias WHERE id_alumno = :id_alumno";
                $stmt_delete_materias = $pdo->prepare($sql_delete_materias);
                $stmt_delete_materias->execute([':id_alumno' => $id]);
                
                // Insertar las nuevas materias del alumno
                $sql_materia = "INSERT INTO alumno_materias (id_alumno, id_materia) VALUES (:id_alumno, :id_materia)";
                $stmt_materia = $pdo->prepare($sql_materia);
                
                foreach ($materias_seleccionadas as $id_materia) {
                    $stmt_materia->execute([
                        ':id_alumno' => $id,
                        ':id_materia' => $id_materia
                    ]);
                }
                
                // Eliminar cursos actuales del alumno
                $sql_delete_cursos = "DELETE FROM alumnos_cursos WHERE id_alumno = :id_alumno";
                $stmt_delete_cursos = $pdo->prepare($sql_delete_cursos);
                $stmt_delete_cursos->execute([':id_alumno' => $id]);
                
                // Insertar los nuevos cursos del alumno
                $cursos_seleccionados = $_POST['cursos_seleccionados'];
                $sql_curso = "INSERT INTO alumnos_cursos (id_alumno, id_curso) VALUES (:id_alumno, :id_curso)";
                $stmt_curso = $pdo->prepare($sql_curso);
                
                foreach ($cursos_seleccionados as $id_curso) {
                    $stmt_curso->execute([
                        ':id_alumno' => $id,
                        ':id_curso' => $id_curso
                    ]);
                }
                
                // Confirmar transacción
                $pdo->commit();
                
                header("Location: selectAlum.php");
                exit();
            } catch (PDOException $e) {
                // Revertir transacción en caso de error
                $pdo->rollback();
                $mensaje = "❌ Error al actualizar el alumno: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Alumno</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
    <style>
        .materias-container, .cursos-container {
            margin: 10px 0;
        }
        .materia-selector, .curso-selector {
            margin: 5px 0;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .materia-info, .curso-info {
            font-size: 0.9em;
            color: #666;
            margin-left: 10px;
        }
        .materias-seleccionadas, .cursos-seleccionados {
            background-color: #f0f8ff;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .cursos-seleccionados {
            background-color: #f0fff0;
        }
        .materia-item, .curso-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
            margin: 2px 0;
            background-color: #e6f3ff;
            border-radius: 3px;
        }
        .curso-item {
            background-color: #e6ffe6;
        }
        .btn-remove {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 2px 8px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.8em;
        }
        .btn-remove:hover {
            background-color: #cc0000;
        }
        .seccion-cursos {
            border-top: 2px solid #27ae60;
            padding-top: 15px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="formulario-contenedor">
        <h2>✏️ Editar Alumno</h2>

        <?php if (!empty($mensaje)) : ?>
            <p class="mensaje-error"><?= $mensaje ?></p>
        <?php endif; ?>

        <form method="POST" id="alumnoForm">
            <input type="hidden" name="id" value="<?= $alumno['id_alumno'] ?>">

            <label>Nombre Completo:</label>
            <input type="text" name="nombre_completo" value="<?= htmlspecialchars($alumno['nombre_completo']) ?>" required>

            <label>Contacto del Alumno:</label>
            <input type="text" name="contacto_alumno" value="<?= htmlspecialchars($alumno['contacto_alumno']) ?>" required>

            <label>Domicilio:</label>
            <input type="text" name="domicilio_alumno" value="<?= htmlspecialchars($alumno['domicilio_alumno']) ?>" required>

            <label>Colegio:</label>
            <input type="text" name="colegio_alumno" value="<?= htmlspecialchars($alumno['colegio_alumno']) ?>" required>

            <label>Cantidad de Materias:</label>
            <input type="number" name="cantidad_materias" id="cantidadMaterias" min="1" max="10" value="<?= $alumno['cantidad_materias'] ?>" required onchange="actualizarSelectoresMaterias()">

            <label>Tutor:</label>
            <select name="id_tutor" required>
                <option value="">Selecciona un tutor</option>
                <?php foreach ($tutores as $tutor): ?>
                    <option value="<?= $tutor['id_tutor'] ?>" <?= $tutor['id_tutor'] == $alumno['id_tutor'] ? 'selected' : '' ?>><?= htmlspecialchars($tutor['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Profesor:</label>
            <select name="id_profesor" required>
                <option value="">Selecciona un profesor</option>
                <?php foreach ($profesores as $profesor): ?>
                    <option value="<?= $profesor['id_profesor'] ?>" <?= $profesor['id_profesor'] == $alumno['id_profesor'] ? 'selected' : '' ?>><?= htmlspecialchars($profesor['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>

            <div id="materiasContainer">
                <label>Materias:</label>
                <div id="materiasSelectores"></div>
            </div>

            <div class="materias-seleccionadas" id="materiasSeleccionadas" style="display: none;">
                <h4>Materias Seleccionadas:</h4>
                <div id="listaMaterias"></div>
            </div>

            <div class="seccion-cursos">
                <label>📚 Cursos (puede seleccionar múltiples):</label>
                <div class="cursos-container">
                    <?php foreach ($cursos as $curso): ?>
                        <div style="margin: 5px 0;">
                            <label style="display: flex; align-items: center; font-weight: normal;">
                                <input type="checkbox" name="cursos_seleccionados[]" value="<?= $curso['id_curso'] ?>" 
                                       <?= in_array($curso['id_curso'], $cursos_actuales) ? 'checked' : '' ?> 
                                       onchange="actualizarCursosSeleccionados()">
                                <span style="margin-left: 8px;"><?= htmlspecialchars($curso['nombre_curso']) ?> (ID: <?= $curso['id_curso'] ?>)</span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="cursos-seleccionados" id="cursosSeleccionados" style="display: none;">
                <h4>📚 Cursos Seleccionados:</h4>
                <div id="listaCursos"></div>
            </div>

            <button type="submit">Actualizar Alumno</button>
        </form>

        <a href="selectAlum.php" class="volver">⬅ Volver</a>
    </div>

    <script>
        const materias = <?= json_encode($materias) ?>;
        const cursos = <?= json_encode($cursos) ?>;
        const materiasActuales = <?= json_encode($materias_actuales) ?>;
        const cursosActuales = <?= json_encode($cursos_actuales) ?>;
        
        let materiasSeleccionadas = [];
        let cursosSeleccionados = [];

        // Inicializar con datos actuales
        window.addEventListener('load', function() {
            actualizarSelectoresMaterias();
            actualizarCursosSeleccionados();
        });

        function actualizarSelectoresMaterias() {
            const cantidad = parseInt(document.getElementById('cantidadMaterias').value) || 0;
            const container = document.getElementById('materiasSelectores');
            
            container.innerHTML = '';
            materiasSeleccionadas = [];
            
            for (let i = 0; i < cantidad; i++) {
                const div = document.createElement('div');
                div.className = 'materia-selector';
                
                // Determinar si hay una materia preseleccionada para este índice
                const materiaPreseleccionada = materiasActuales[i] || '';
                
                div.innerHTML = `
                    <label>Materia ${i + 1}:</label>
                    <select onchange="seleccionarMateria(this, ${i})" required>
                        <option value="">Selecciona una materia</option>
                        ${materias.map(materia => 
                            `<option value="${materia.id_materia}" ${materia.id_materia == materiaPreseleccionada ? 'selected' : ''}>${materia.nombre_materia} (ID: ${materia.id_materia})</option>`
                        ).join('')}
                    </select>
                    <input type="hidden" name="materias_seleccionadas[]" id="materia_${i}" value="${materiaPreseleccionada}">
                `;
                container.appendChild(div);
                
                // Agregar a materiasSeleccionadas si hay una preseleccionada
                if (materiaPreseleccionada) {
                    const materia = materias.find(m => m.id_materia == materiaPreseleccionada);
                    if (materia) {
                        materiasSeleccionadas.push({
                            id: materiaPreseleccionada,
                            nombre: materia.nombre_materia,
                            index: i
                        });
                    }
                }
            }
            
            actualizarListaMaterias();
        }

        function seleccionarMateria(select, index) {
            const valor = select.value;
            const hiddenInput = document.getElementById(`materia_${index}`);
            
            if (valor) {
                // Verificar si la materia ya está seleccionada
                const yaSeleccionada = materiasSeleccionadas.find(m => m.id === valor);
                if (yaSeleccionada && yaSeleccionada.index !== index) {
                    alert('Esta materia ya ha sido seleccionada. Por favor, elija otra.');
                    select.value = '';
                    hiddenInput.value = '';
                    return;
                }
                
                // Remover selección anterior de este índice
                materiasSeleccionadas = materiasSeleccionadas.filter(m => m.index !== index);
                
                // Agregar nueva selección
                const materia = materias.find(m => m.id_materia === valor);
                materiasSeleccionadas.push({
                    id: valor,
                    nombre: materia.nombre_materia,
                    index: index
                });
                
                hiddenInput.value = valor;
            } else {
                // Remover selección
                materiasSeleccionadas = materiasSeleccionadas.filter(m => m.index !== index);
                hiddenInput.value = '';
            }
            
            actualizarListaMaterias();
        }

        function actualizarListaMaterias() {
            const container = document.getElementById('materiasSeleccionadas');
            const lista = document.getElementById('listaMaterias');
            
            if (materiasSeleccionadas.length > 0) {
                container.style.display = 'block';
                lista.innerHTML = materiasSeleccionadas
                    .sort((a, b) => a.index - b.index)
                    .map(materia => `
                        <div class="materia-item">
                            <span>${materia.nombre} (ID: ${materia.id})</span>
                            <button type="button" class="btn-remove" onclick="removerMateria(${materia.index})">✕</button>
                        </div>
                    `).join('');
            } else {
                container.style.display = 'none';
            }
        }

        function removerMateria(index) {
            // Limpiar el select correspondiente
            const selects = document.querySelectorAll('#materiasSelectores select');
            if (selects[index]) {
                selects[index].value = '';
            }
            
            // Limpiar el input hidden
            const hiddenInput = document.getElementById(`materia_${index}`);
            if (hiddenInput) {
                hiddenInput.value = '';
            }
            
            // Remover de la lista
            materiasSeleccionadas = materiasSeleccionadas.filter(m => m.index !== index);
            actualizarListaMaterias();
        }

        function actualizarCursosSeleccionados() {
            const checkboxes = document.querySelectorAll('input[name="cursos_seleccionados[]"]:checked');
            cursosSeleccionados = [];
            
            checkboxes.forEach(checkbox => {
                const curso = cursos.find(c => c.id_curso == checkbox.value);
                if (curso) {
                    cursosSeleccionados.push(curso);
                }
            });
            
            actualizarListaCursos();
        }

        function actualizarListaCursos() {
            const container = document.getElementById('cursosSeleccionados');
            const lista = document.getElementById('listaCursos');
            
            if (cursosSeleccionados.length > 0) {
                container.style.display = 'block';
                lista.innerHTML = cursosSeleccionados
                    .map(curso => `
                        <div class="curso-item">
                            <span>${curso.nombre_curso} (ID: ${curso.id_curso})</span>
                            <button type="button" class="btn-remove" onclick="removerCurso(${curso.id_curso})">✕</button>
                        </div>
                    `).join('');
            } else {
                container.style.display = 'none';
            }
        }

        function removerCurso(idCurso) {
            // Desmarcar el checkbox correspondiente
            const checkbox = document.querySelector(`input[name="cursos_seleccionados[]"][value="${idCurso}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
            
            actualizarCursosSeleccionados();
        }

        // Validación del formulario
        document.getElementById('alumnoForm').addEventListener('submit', function(e) {
            // Sincroniza materiasSeleccionadas con los selects actuales
            materiasSeleccionadas = [];
            const selects = document.querySelectorAll('#materiasSelectores select');
            selects.forEach((select, i) => {
                const valor = select.value;
                const materia = materias.find(m => m.id_materia == valor);
                const hiddenInput = document.getElementById(`materia_${i}`);
                if (valor && materia) {
                    materiasSeleccionadas.push({
                        id: valor,
                        nombre: materia.nombre_materia,
                        index: i
                    });
                    hiddenInput.value = valor;
                } else {
                    hiddenInput.value = '';
                }
            });

            const cantidadEsperada = parseInt(document.getElementById('cantidadMaterias').value);
            const cantidadSeleccionada = materiasSeleccionadas.length;

            // Elimina los inputs hidden vacíos antes de enviar
            document.querySelectorAll('input[name="materias_seleccionadas[]"]').forEach(input => {
                if (!input.value) input.parentNode.removeChild(input);
            });

            // Validar materias
            if (cantidadSeleccionada !== cantidadEsperada) {
                e.preventDefault();
                alert(`Debe seleccionar exactamente ${cantidadEsperada} materias. Actualmente tiene ${cantidadSeleccionada} seleccionadas.`);
                return;
            }

            // Validar que al menos un curso esté seleccionado
            const cursosChecked = document.querySelectorAll('input[name="cursos_seleccionados[]"]:checked');
            if (cursosChecked.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un curso para el alumno.');
                return;
            }
        });

    </script>
</body>

</html>