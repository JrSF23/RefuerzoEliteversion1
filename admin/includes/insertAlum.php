<?php
require_once 'config.php';

// Obtener tutores
$tutores = $pdo->query("SELECT id_tutor, nombre_completo FROM tutores")->fetchAll(PDO::FETCH_ASSOC);

// Obtener materias con información del curso
$materias = $pdo->query("
    SELECT m.id_materia, m.nombre_materia, m.id_curso, c.nombre_curso 
    FROM materias m 
    INNER JOIN cursos c ON m.id_curso = c.id_curso 
    ORDER BY c.nombre_curso, m.nombre_materia
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener profesores
$profesores = $pdo->query("SELECT id_profesor, nombre_completo FROM profesores")->fetchAll(PDO::FETCH_ASSOC);

// Obtener cursos
$cursos = $pdo->query("SELECT id_curso, nombre_curso FROM cursos ORDER BY nombre_curso")->fetchAll(PDO::FETCH_ASSOC);

$mensaje = '';

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
            // Validar que las materias seleccionadas pertenezcan a los cursos seleccionados
            $cursos_seleccionados = $_POST['cursos_seleccionados'];
            $materias_validas = $pdo->prepare("
                SELECT id_materia FROM materias 
                WHERE id_materia IN (" . str_repeat('?,', count($materias_seleccionadas) - 1) . "?) 
                AND id_curso IN (" . str_repeat('?,', count($cursos_seleccionados) - 1) . "?)
            ");
            $params = array_merge($materias_seleccionadas, $cursos_seleccionados);
            $materias_validas->execute($params);
            $materias_validas_ids = $materias_validas->fetchAll(PDO::FETCH_COLUMN);
            
            if (count($materias_validas_ids) !== count($materias_seleccionadas)) {
                $mensaje = "❌ Algunas materias seleccionadas no pertenecen a los cursos elegidos.";
            } else {
                try {
                    // Comenzar transacción
                    $pdo->beginTransaction();
                    
                    // Insertar el alumno
                    $sql = "INSERT INTO alumnos (nombre_completo, contacto_alumno, domicilio_alumno, colegio_alumno, cantidad_materias, materias_seleccionadas, id_tutor, id_profesor) 
                            VALUES (:nombre_completo, :contacto_alumno, :domicilio_alumno, :colegio_alumno, :cantidad_materias, :materias_seleccionadas, :id_tutor, :id_profesor)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':nombre_completo' => $_POST['nombre_completo'],
                        ':contacto_alumno' => $_POST['contacto_alumno'],
                        ':domicilio_alumno' => $_POST['domicilio_alumno'],
                        ':colegio_alumno' => $_POST['colegio_alumno'],
                        ':cantidad_materias' => $cantidad_materias,
                        ':materias_seleccionadas' => implode(',', $materias_seleccionadas),
                        ':id_tutor' => $_POST['id_tutor'],
                        ':id_profesor' => $_POST['id_profesor']
                    ]);
                    
                    // Obtener el ID del alumno recién insertado
                    $id_alumno = $pdo->lastInsertId();
                    
                    // Insertar las materias del alumno en la tabla intermedia
                    $sql_materia = "INSERT INTO alumno_materias (id_alumno, id_materia) VALUES (:id_alumno, :id_materia)";
                    $stmt_materia = $pdo->prepare($sql_materia);
                    
                    foreach ($materias_seleccionadas as $id_materia) {
                        $stmt_materia->execute([
                            ':id_alumno' => $id_alumno,
                            ':id_materia' => $id_materia
                        ]);
                    }
                    
                    // Insertar los cursos del alumno en la tabla intermedia alumnos_cursos
                    $sql_curso = "INSERT INTO alumnos_cursos (id_alumno, id_curso) VALUES (:id_alumno, :id_curso)";
                    $stmt_curso = $pdo->prepare($sql_curso);
                    
                    foreach ($cursos_seleccionados as $id_curso) {
                        $stmt_curso->execute([
                            ':id_alumno' => $id_alumno,
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
                    $mensaje = "❌ Error al registrar al alumno: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Alumno</title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
    <style>
        /* Estilos mejorados */
        .formulario-contenedor {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .seccion {
            background: #f8f9fa;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .seccion-cursos {
            border-left-color: #28a745;
        }

        .seccion-materias {
            border-left-color: #ffc107;
        }

        .seccion h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1.2em;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cursos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }

        .curso-checkbox {
            display: flex;
            align-items: center;
            padding: 10px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .curso-checkbox:hover {
            border-color: #28a745;
            background: #f8fff9;
        }

        .curso-checkbox.selected {
            border-color: #28a745;
            background: #d4edda;
        }

        .curso-checkbox input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .materias-container {
            margin: 20px 0;
        }

        .materia-selector {
            margin: 10px 0;
            padding: 15px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 6px;
        }

        .materia-selector label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }

        .materia-selector select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            background: white;
        }

        .materia-selector select:focus {
            border-color: #ffc107;
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.25);
        }

        .selecciones-resumen {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .resumen-seccion {
            margin: 15px 0;
        }

        .resumen-seccion h4 {
            margin: 0 0 10px 0;
            font-size: 1.1em;
        }

        .item-lista {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            margin: 5px 0;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s ease;
        }

        .btn-remove:hover {
            background: #c82333;
        }

        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
            font-weight: 500;
        }

        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }

        .form-group {
            margin: 15px 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #495057;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
            width: 100%;
            margin: 20px 0;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .volver {
            display: inline-block;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        .volver:hover {
            background: #545b62;
        }

        .contador-materias {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        @media (max-width: 768px) {
            .cursos-grid {
                grid-template-columns: 1fr;
            }
            
            .formulario-contenedor {
                margin: 10px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="formulario-contenedor">
        <h2>👨‍🎓 Registrar Nuevo Alumno</h2>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert <?= strpos($mensaje, '❌') !== false ? 'alert-danger' : 'alert-success' ?>">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="alumnoForm">
            <!-- Información básica del alumno -->
            <div class="seccion">
                <h3>👤 Información Personal</h3>
                
                <div class="form-group">
                    <label>Nombre Completo:</label>
                    <input type="text" name="nombre_completo" required value="<?= htmlspecialchars($_POST['nombre_completo'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Contacto del Alumno:</label>
                    <input type="text" name="contacto_alumno" required value="<?= htmlspecialchars($_POST['contacto_alumno'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Domicilio:</label>
                    <input type="text" name="domicilio_alumno" required value="<?= htmlspecialchars($_POST['domicilio_alumno'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Colegio:</label>
                    <input type="text" name="colegio_alumno" required value="<?= htmlspecialchars($_POST['colegio_alumno'] ?? '') ?>">
                </div>
            </div>

            <!-- Asignaciones -->
            <div class="seccion">
                <h3>👥 Asignaciones</h3>
                
                <div class="form-group">
                    <label>Tutor:</label>
                    <select name="id_tutor" required>
                        <option value="">Selecciona un tutor</option>
                        <?php foreach ($tutores as $tutor): ?>
                            <option value="<?= $tutor['id_tutor'] ?>" <?= (($_POST['id_tutor'] ?? '') == $tutor['id_tutor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tutor['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Profesor:</label>
                    <select name="id_profesor" required>
                        <option value="">Selecciona un profesor</option>
                        <?php foreach ($profesores as $profesor): ?>
                            <option value="<?= $profesor['id_profesor'] ?>" <?= (($_POST['id_profesor'] ?? '') == $profesor['id_profesor']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($profesor['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Selección de cursos -->
            <div class="seccion seccion-cursos">
                <h3>📚 Selección de Cursos</h3>
                <div class="alert alert-info">
                    <strong>Paso 1:</strong> Primero selecciona los cursos. Las materias disponibles se filtrarán según los cursos elegidos.
                </div>
                
                <div class="cursos-grid">
                    <?php foreach ($cursos as $curso): ?>
                        <label class="curso-checkbox">
                            <input type="checkbox" name="cursos_seleccionados[]" value="<?= $curso['id_curso'] ?>" onchange="actualizarCursosYMaterias()">
                            <span><?= htmlspecialchars($curso['nombre_curso']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Configuración de materias -->
            <div class="seccion seccion-materias" id="seccionMaterias" style="display: none;">
                <h3>📖 Configuración de Materias <span class="contador-materias" id="contadorMaterias">0 seleccionadas</span></h3>
                
                <div class="form-group">
                    <label>Cantidad de Materias a Seleccionar:</label>
                    <input type="number" name="cantidad_materias" id="cantidadMaterias" min="1" max="20" required onchange="actualizarSelectoresMaterias()">
                </div>

                <div class="alert alert-warning" id="alertaMaterias" style="display: none;">
                    <strong>Atención:</strong> Selecciona primero los cursos para ver las materias disponibles.
                </div>

                <div id="materiasContainer">
                    <div id="materiasSelectores"></div>
                </div>
            </div>

            <!-- Resumen de selecciones -->
            <div class="selecciones-resumen" id="resumenSelecciones" style="display: none;">
                <h3>📋 Resumen de Selecciones</h3>
                
                <div class="resumen-seccion">
                    <h4>📚 Cursos Seleccionados:</h4>
                    <div id="listaCursos"></div>
                </div>
                
                <div class="resumen-seccion">
                    <h4>📖 Materias Seleccionadas:</h4>
                    <div id="listaMaterias"></div>
                </div>
            </div>

            <button type="submit" class="btn-primary">✅ Registrar Alumno</button>
        </form>

        <a href="../bienvenida.php" class="volver">⬅ Volver</a>
    </div>

    <script>
        const materias = <?= json_encode($materias) ?>;
        const cursos = <?= json_encode($cursos) ?>;
        let materiasSeleccionadas = [];
        let cursosSeleccionados = [];
        let materiasFiltradas = [];

        function actualizarCursosYMaterias() {
            const checkboxes = document.querySelectorAll('input[name="cursos_seleccionados[]"]:checked');
            cursosSeleccionados = [];
            
            // Actualizar lista de cursos seleccionados
            checkboxes.forEach(checkbox => {
                const curso = cursos.find(c => c.id_curso == checkbox.value);
                if (curso) {
                    cursosSeleccionados.push(curso);
                }
                
                // Actualizar apariencia visual del checkbox
                const label = checkbox.closest('.curso-checkbox');
                label.classList.toggle('selected', checkbox.checked);
            });
            
            // Filtrar materias según cursos seleccionados
            if (cursosSeleccionados.length > 0) {
                const idsCursosSeleccionados = cursosSeleccionados.map(c => c.id_curso);
                materiasFiltradas = materias.filter(m => idsCursosSeleccionados.includes(parseInt(m.id_curso)));
                
                document.getElementById('seccionMaterias').style.display = 'block';
                document.getElementById('alertaMaterias').style.display = 'none';
                
                // Limpiar selecciones anteriores de materias
                materiasSeleccionadas = [];
                document.getElementById('cantidadMaterias').value = '';
                document.getElementById('materiasSelectores').innerHTML = '';
                actualizarContadorMaterias();
            } else {
                materiasFiltradas = [];
                document.getElementById('seccionMaterias').style.display = 'none';
                materiasSeleccionadas = [];
                document.getElementById('materiasSelectores').innerHTML = '';
            }
            
            actualizarResumen();
        }

        function actualizarSelectoresMaterias() {
            const cantidad = parseInt(document.getElementById('cantidadMaterias').value) || 0;
            const container = document.getElementById('materiasSelectores');
            
            if (materiasFiltradas.length === 0) {
                document.getElementById('alertaMaterias').style.display = 'block';
                return;
            } else {
                document.getElementById('alertaMaterias').style.display = 'none';
            }
            
            container.innerHTML = '';
            materiasSeleccionadas = [];
            
            for (let i = 0; i < cantidad; i++) {
                const div = document.createElement('div');
                div.className = 'materia-selector';
                div.innerHTML = `
                    <label>Materia ${i + 1}:</label>
                    <select onchange="seleccionarMateria(this, ${i})" required>
                        <option value="">Selecciona una materia</option>
                        ${materiasFiltradas.map(materia => 
                            `<option value="${materia.id_materia}">${materia.nombre_materia} (${materia.nombre_curso})</option>`
                        ).join('')}
                    </select>
                    <input type="hidden" name="materias_seleccionadas[]" id="materia_${i}">
                `;
                container.appendChild(div);
            }
            
            actualizarContadorMaterias();
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
                const materia = materiasFiltradas.find(m => m.id_materia == valor);
                if (materia) {
                    materiasSeleccionadas.push({
                        id: valor,
                        nombre: materia.nombre_materia,
                        curso: materia.nombre_curso,
                        index: index
                    });
                }
                
                hiddenInput.value = valor;
            } else {
                // Remover selección
                materiasSeleccionadas = materiasSeleccionadas.filter(m => m.index !== index);
                hiddenInput.value = '';
            }
            
            actualizarContadorMaterias();
            actualizarResumen();
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
            actualizarContadorMaterias();
            actualizarResumen();
        }

        function removerCurso(idCurso) {
            // Desmarcar el checkbox correspondiente
            const checkbox = document.querySelector(`input[name="cursos_seleccionados[]"][value="${idCurso}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
            
            actualizarCursosYMaterias();
        }

        function actualizarContadorMaterias() {
            const contador = document.getElementById('contadorMaterias');
            const cantidad = parseInt(document.getElementById('cantidadMaterias').value) || 0;
            const seleccionadas = materiasSeleccionadas.length;
            contador.textContent = `${seleccionadas}/${cantidad} seleccionadas`;
            contador.style.backgroundColor = seleccionadas === cantidad && cantidad > 0 ? '#28a745' : '#007bff';
        }

        function actualizarResumen() {
            const container = document.getElementById('resumenSelecciones');
            const listaCursos = document.getElementById('listaCursos');
            const listaMaterias = document.getElementById('listaMaterias');
            
            if (cursosSeleccionados.length > 0 || materiasSeleccionadas.length > 0) {
                container.style.display = 'block';
                
                // Actualizar lista de cursos
                listaCursos.innerHTML = cursosSeleccionados
                    .map(curso => `
                        <div class="item-lista">
                            <span>${curso.nombre_curso}</span>
                            <button type="button" class="btn-remove" onclick="removerCurso(${curso.id_curso})">✕</button>
                        </div>
                    `).join('');
                
                // Actualizar lista de materias
                listaMaterias.innerHTML = materiasSeleccionadas
                    .sort((a, b) => a.index - b.index)
                    .map(materia => `
                        <div class="item-lista">
                            <span>${materia.nombre} <small>(${materia.curso})</small></span>
                            <button type="button" class="btn-remove" onclick="removerMateria(${materia.index})">✕</button>
                        </div>
                    `).join('');
            } else {
                container.style.display = 'none';
            }
        }

        // Validación del formulario
        document.getElementById('alumnoForm').addEventListener('submit', function(e) {
            // Validar que hay cursos seleccionados
            const cursosChecked = document.querySelectorAll('input[name="cursos_seleccionados[]"]:checked');
            if (cursosChecked.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un curso para el alumno.');
                return;
            }

            // Sincronizar materiasSeleccionadas con los selects actuales
            materiasSeleccionadas = [];
            const selects = document.querySelectorAll('#materiasSelectores select');
            selects.forEach((select, i) => {
                const valor = select.value;
                const materia = materiasFiltradas.find(m => m.id_materia == valor);
                const hiddenInput = document.getElementById(`materia_${i}`);
                if (valor && materia) {
                    materiasSeleccionadas.push({
                        id: valor,
                        nombre: materia.nombre_materia,
                        curso: materia.nombre_curso,
                        index: i
                    });
                    hiddenInput.value = valor;
                } else {
                    hiddenInput.value = '';
                }
            });

            const cantidadEsperada = parseInt(document.getElementById('cantidadMaterias').value);
            const cantidadSeleccionada = materiasSeleccionadas.length;

            // Eliminar los inputs hidden vacíos antes de enviar
            document.querySelectorAll('input[name="materias_seleccionadas[]"]').forEach(input => {
                if (!input.value) input.parentNode.removeChild(input);
            });

            // Validar materias
            if (cantidadSeleccionada !== cantidadEsperada) {
                e.preventDefault();
                alert(`Debe seleccionar exactamente ${cantidadEsperada} materias. Actualmente tiene ${cantidadSeleccionada} seleccionadas.`);
                return;
            }

            // Validar que las materias pertenezcan a los cursos seleccionados
            const idsCursosSeleccionados = cursosSeleccionados.map(c => c.id_curso);
            const materiasInvalidas = materiasSeleccionadas.filter(m => {
                const materia = materiasFiltradas.find(mf => mf.id_materia == m.id);
                return !materia || !idsCursosSeleccionados.includes(parseInt(materia.id_curso));
            });

            if (materiasInvalidas.length > 0) {
                e.preventDefault();
                alert('Algunas materias seleccionadas no pertenecen a los cursos elegidos. Por favor, revise sus selecciones.');
                return;
            }
        });

        // Actualizar checkboxes de cursos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.curso-checkbox').forEach(label => {
                const checkbox = label.querySelector('input[type="checkbox"]');
                label.classList.toggle('selected', checkbox.checked);
            });
        });

    </script>
</body>

</html>