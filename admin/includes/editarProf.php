<?php
require_once 'config.php';

// Verificar que se haya proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: selectProf.php");
    exit();
}

$id_profesor = (int)$_GET['id'];
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Validar campos requeridos
        $errores = [];
        
        if (empty($_POST['nombre_completo'])) {
            $errores[] = "El nombre completo es obligatorio";
        }
        
        if (empty($_POST['contacto_profesor'])) {
            $errores[] = "El número de contacto es obligatorio";
        }
        
        if (empty($_POST['domicilio_profesor'])) {
            $errores[] = "La dirección es obligatoria";
        }
        
        // Validar email si se proporciona
        if (!empty($_POST['email']) && !preg_match('/^[\p{L}0-9._%+-]+@[\p{L}0-9.-]+\.[a-z]{2,}$/iu', $_POST['email'])) {
            $errores[] = "El formato del email no es válido";
        }
        
        // Validar fecha de nacimiento si se proporciona
        if (!empty($_POST['fecha_nacimiento'])) {
            $fecha_nac = DateTime::createFromFormat('Y-m-d', $_POST['fecha_nacimiento']);
            if (!$fecha_nac || $fecha_nac->format('Y-m-d') !== $_POST['fecha_nacimiento']) {
                $errores[] = "El formato de la fecha de nacimiento no es válido";
            }
        }
        
        // Validar fecha de contratación si se proporciona
        if (!empty($_POST['fecha_contratacion'])) {
            $fecha_cont = DateTime::createFromFormat('Y-m-d', $_POST['fecha_contratacion']);
            if (!$fecha_cont || $fecha_cont->format('Y-m-d') !== $_POST['fecha_contratacion']) {
                $errores[] = "El formato de la fecha de contratación no es válido";
            }
        }
        
        if (empty($errores)) {
            // Actualizar información básica del profesor en tabla 'profesores'
            $sql_profesor = "UPDATE profesores SET 
                            nombre_completo = :nombre_completo,
                            domicilio_profesor = :domicilio_profesor,
                            contacto_profesor = :contacto_profesor
                            WHERE id_profesor = :id_profesor";
            
            $stmt_profesor = $pdo->prepare($sql_profesor);
            $stmt_profesor->execute([
                ':nombre_completo' => trim($_POST['nombre_completo']),
                ':domicilio_profesor' => trim($_POST['domicilio_profesor']),
                ':contacto_profesor' => trim($_POST['contacto_profesor']),
                ':id_profesor' => $id_profesor
            ]);
            
            // Verificar si ya existe un registro en profesor_detalles
            $sql_check_detalle = "SELECT id_detalle FROM profesor_detalles WHERE id_profesor = :id_profesor";
            $stmt_check = $pdo->prepare($sql_check_detalle);
            $stmt_check->execute([':id_profesor' => $id_profesor]);
            $detalle_existe = $stmt_check->fetch();
            
            if ($detalle_existe) {
                // Actualizar registro existente en profesor_detalles
                $sql_detalle = "UPDATE profesor_detalles SET 
                               fecha_nacimiento = :fecha_nacimiento,
                               email = :email,
                               dni = :dni,
                               nivel_estudios = :nivel_estudios,
                               especializacion = :especializacion,
                               anos_experiencia = :anos_experiencia,
                               fecha_contratacion = :fecha_contratacion,
                               experiencia_profesional = :experiencia_profesional
                               WHERE id_profesor = :id_profesor";
            } else {
                // Crear nuevo registro en profesor_detalles
                $sql_detalle = "INSERT INTO profesor_detalles 
                               (id_profesor, fecha_nacimiento, email, dni, nivel_estudios, 
                                especializacion, anos_experiencia, fecha_contratacion, experiencia_profesional)
                               VALUES 
                               (:id_profesor, :fecha_nacimiento, :email, :dni, :nivel_estudios, 
                                :especializacion, :anos_experiencia, :fecha_contratacion, :experiencia_profesional)";
            }
            
            $stmt_detalle = $pdo->prepare($sql_detalle);
            $params_detalle = [
                ':id_profesor' => $id_profesor,
                ':fecha_nacimiento' => !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null,
                ':email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
                ':dni' => !empty($_POST['dni']) ? trim($_POST['dni']) : null,
                ':nivel_estudios' => !empty($_POST['nivel_estudios']) ? trim($_POST['nivel_estudios']) : null,
                ':especializacion' => !empty($_POST['especializacion']) ? trim($_POST['especializacion']) : null,
                ':anos_experiencia' => !empty($_POST['anos_experiencia']) ? (int)$_POST['anos_experiencia'] : null,
                ':fecha_contratacion' => !empty($_POST['fecha_contratacion']) ? $_POST['fecha_contratacion'] : null,
                ':experiencia_profesional' => !empty($_POST['experiencia_profesional']) ? trim($_POST['experiencia_profesional']) : null
            ];
            
            $stmt_detalle->execute($params_detalle);
            
            // Actualizar materias del profesor
            // Primero eliminar todas las materias actuales del profesor
            $sql_delete_materias = "DELETE FROM profesor_materia WHERE id_profesor = :id_profesor";
            $stmt_delete_materias = $pdo->prepare($sql_delete_materias);
            $stmt_delete_materias->execute([':id_profesor' => $id_profesor]);
            
            // Insertar las nuevas materias seleccionadas
            if (!empty($_POST['materias'])) {
                $sql_insert_materia = "INSERT INTO profesor_materia (id_profesor, id_materia) VALUES (:id_profesor, :id_materia)";
                $stmt_insert_materia = $pdo->prepare($sql_insert_materia);
                
                foreach ($_POST['materias'] as $id_materia) {
                    $stmt_insert_materia->execute([
                        ':id_profesor' => $id_profesor,
                        ':id_materia' => (int)$id_materia
                    ]);
                }
            }
            
            $pdo->commit();
            
            $mensaje = "✅ La información del profesor ha sido actualizada correctamente";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "❌ " . implode("<br>", $errores);
            $tipo_mensaje = "error";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $mensaje = "❌ Error de base de datos: " . $e->getMessage();
        $tipo_mensaje = "error";
    }
}

// Obtener información actual del profesor con LEFT JOIN para incluir detalles si existen
$sql = "SELECT p.*, pd.fecha_nacimiento, pd.email, pd.dni, pd.nivel_estudios, 
               pd.especializacion, pd.anos_experiencia, pd.fecha_contratacion, 
               pd.experiencia_profesional
        FROM profesores p 
        LEFT JOIN profesor_detalles pd ON p.id_profesor = pd.id_profesor 
        WHERE p.id_profesor = :id_profesor";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_profesor' => $id_profesor]);
$profesor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profesor) {
    header("Location: selectProf.php");
    exit();
}

// Obtener materias disponibles para el selector
$sql_materias_disponibles = "SELECT m.id_materia, m.nombre_materia, c.nombre_curso 
                            FROM materias m 
                            INNER JOIN cursos c ON m.id_curso = c.id_curso
                            ORDER BY c.nombre_curso, m.nombre_materia";
$stmt_materias_disponibles = $pdo->prepare($sql_materias_disponibles);
$stmt_materias_disponibles->execute();
$materias_disponibles = $stmt_materias_disponibles->fetchAll(PDO::FETCH_ASSOC);

// Obtener materias actuales del profesor
$sql_materias_profesor = "SELECT id_materia FROM profesor_materia WHERE id_profesor = :id_profesor";
$stmt_materias_profesor = $pdo->prepare($sql_materias_profesor);
$stmt_materias_profesor->execute([':id_profesor' => $id_profesor]);
$materias_profesor = $stmt_materias_profesor->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Profesor - <?= htmlspecialchars($profesor['nombre_completo']) ?></title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .form-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="10" cy="90" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .form-title {
            font-size: 2.2em;
            font-weight: 700;
            margin: 0 0 10px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }

        .form-subtitle {
            font-size: 1.1em;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .form-content {
            padding: 30px;
        }

        .mensaje {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .mensaje.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensaje.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .seccion {
            margin-bottom: 30px;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            border-left: 4px solid #3498db;
        }

        .seccion h3 {
            color: #2c3e50;
            font-size: 1.4em;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: white;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-input.required {
            border-color: #e74c3c;
        }

        .form-input.required:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 20px;
        }

        .required-indicator {
            color: #e74c3c;
            font-weight: bold;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #ecf0f1;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 1em;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }

        .materias-grid {
            max-height: 300px;
            overflow-y: auto;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            background: white;
        }

        .curso-group {
            margin-bottom: 20px;
        }

        .curso-titulo {
            color: #2c3e50;
            font-size: 1.1em;
            font-weight: 600;
            margin: 0 0 10px 0;
            padding: 8px 12px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border-radius: 8px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .materia-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .materia-checkbox:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .materia-checkbox input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
            accent-color: #3498db;
        }

        .materia-checkbox .checkmark {
            font-weight: 500;
            color: #34495e;
        }

        .materia-checkbox input[type="checkbox"]:checked + .checkmark {
            color: #2980b9;
            font-weight: 600;
        }

        .materias-seleccionadas {
            background: #e8f5e8;
            border-left: 4px solid #27ae60;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .contador-materias {
            text-align: right;
            margin-top: 10px;
            font-size: 0.9em;
            color: #7f8c8d;
            font-weight: 500;
        }

        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .form-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .form-header {
                padding: 20px;
            }
            
            .form-title {
                font-size: 1.8em;
            }
            
            .form-content {
                padding: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-buttons {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="form-header">
            <h1 class="form-title">✏️ EDITAR PROFESOR</h1>
            <p class="form-subtitle">Actualizar información del docente</p>
        </div>

        <div class="form-content">
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?= $tipo_mensaje ?>">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="formEditarProfesor">
                <!-- Información Personal -->
                <div class="seccion">
                    <h3>👤 INFORMACIÓN PERSONAL</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre_completo" class="form-label">
                                Nombre Completo <span class="required-indicator">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre_completo" 
                                   name="nombre_completo" 
                                   class="form-input required" 
                                   value="<?= htmlspecialchars($profesor['nombre_completo']) ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" 
                                   id="fecha_nacimiento" 
                                   name="fecha_nacimiento" 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($profesor['fecha_nacimiento'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="dni" class="form-label">Documento de Identidad</label>
                            <input type="text" 
                                   id="dni" 
                                   name="dni" 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($profesor['dni'] ?? '') ?>"
                                   placeholder="Ej: 12345678">
                        </div>

                        <div class="form-group">
                            <label for="contacto_profesor" class="form-label">
                                Número de Teléfono <span class="required-indicator">*</span>
                            </label>
                            <input type="tel" 
                                   id="contacto_profesor" 
                                   name="contacto_profesor" 
                                   class="form-input required" 
                                   value="<?= htmlspecialchars($profesor['contacto_profesor']) ?>" 
                                   required
                                   placeholder="Ej: +1234567890">
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="text" 
                                   id="email" 
                                   name="email" 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($profesor['email'] ?? '') ?>"
                                   placeholder="profesor@email.com">
                        </div>

                        <div class="form-group full-width">
                            <label for="domicilio_profesor" class="form-label">
                                Dirección <span class="required-indicator">*</span>
                            </label>
                            <input type="text" 
                                   id="domicilio_profesor" 
                                   name="domicilio_profesor" 
                                   class="form-input required" 
                                   value="<?= htmlspecialchars($profesor['domicilio_profesor']) ?>" 
                                   required
                                   placeholder="Dirección completa">
                        </div>
                    </div>
                </div>

                <!-- Información Profesional -->
                <div class="seccion">
                    <h3>🎓 INFORMACIÓN PROFESIONAL</h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nivel_estudios" class="form-label">Nivel de Estudios</label>
                            <select id="nivel_estudios" name="nivel_estudios" class="form-input form-select">
                                <option value="">Seleccionar nivel</option>
                                <option value="Bachillerato" <?= (isset($profesor['nivel_estudios']) && $profesor['nivel_estudios'] === 'Bachillerato') ? 'selected' : '' ?>>Bachillerato</option>
                                <option value="Técnico" <?= (isset($profesor['nivel_estudios']) && $profesor['nivel_estudios'] === 'Técnico') ? 'selected' : '' ?>>Técnico</option>
                                <option value="Tecnológico" <?= (isset($profesor['nivel_estudios']) && $profesor['nivel_estudios'] === 'Tecnológico') ? 'selected' : '' ?>>Tecnológico</option>
                                <option value="Universitario" <?= (isset($profesor['nivel_estudios']) && $profesor['nivel_estudios'] === 'Universitario') ? 'selected' : '' ?>>Universitario</option>
                                <option value="Especialización" <?= (isset($profesor['nivel_estudios']) && $profesor['nivel_estudios'] === 'Especialización') ? 'selected' : '' ?>>Especialización</option>
                                <option value="Maestría" <?= (isset($profesor['nivel_estudios']) && $profesor['nivel_estudios'] === 'Maestría') ? 'selected' : '' ?>>Maestría</option>
                                <option value="Doctorado" <?= (isset($profesor['nivel_estudios']) && $profesor['nivel_estudios'] === 'Doctorado') ? 'selected' : '' ?>>Doctorado</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="especializacion" class="form-label">Especialización</label>
                            <input type="text" 
                                   id="especializacion" 
                                   name="especializacion" 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($profesor['especializacion'] ?? '') ?>"
                                   placeholder="Ej: Matemáticas, Física, etc.">
                        </div>

                        <div class="form-group">
                            <label for="anos_experiencia" class="form-label">Años de Experiencia</label>
                            <input type="number" 
                                   id="anos_experiencia" 
                                   name="anos_experiencia" 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($profesor['anos_experiencia'] ?? '') ?>"
                                   min="0" 
                                   max="50"
                                   placeholder="Número de años">
                        </div>

                        <div class="form-group">
                            <label for="fecha_contratacion" class="form-label">Fecha de Contratación</label>
                            <input type="date" 
                                   id="fecha_contratacion" 
                                   name="fecha_contratacion" 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($profesor['fecha_contratacion'] ?? '') ?>">
                        </div>

                        <div class="form-group full-width">
                            <label for="experiencia_profesional" class="form-label">Experiencia Profesional</label>
                            <textarea id="experiencia_profesional" 
                                      name="experiencia_profesional" 
                                      class="form-input form-textarea" 
                                      rows="4"
                                      placeholder="Describe tu experiencia profesional, logros, certificaciones, etc."><?= htmlspecialchars($profesor['experiencia_profesional'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Materias Impartidas -->
                <div class="seccion">
                    <h3>📚 MATERIAS IMPARTIDAS</h3>
                    
                    <div class="form-group full-width">
                        <label class="form-label">Seleccionar Materias</label>
                        <?php if (empty($materias_disponibles)): ?>
                            <p style="text-align: center; color: #7f8c8d; padding: 20px;">
                                No hay materias disponibles. 
                                <a href="insertMat.php" style="color: #3498db;">Crear primera materia</a>
                            </p>
                        <?php else: ?>
                            <div class="materias-grid">
                                <?php 
                                $curso_actual = '';
                                foreach ($materias_disponibles as $materia): 
                                    if ($curso_actual !== $materia['nombre_curso']): 
                                        if ($curso_actual !== '') echo '</div>';
                                        $curso_actual = $materia['nombre_curso'];
                                        echo '<div class="curso-group">';
                                        echo '<h4 class="curso-titulo">' . htmlspecialchars($curso_actual) . '</h4>';
                                    endif;
                                ?>
                                    <label class="materia-checkbox">
                                        <input type="checkbox" 
                                               name="materias[]" 
                                               value="<?= $materia['id_materia'] ?>"
                                               <?= in_array($materia['id_materia'], $materias_profesor) ? 'checked' : '' ?>>
                                        <span class="checkmark"><?= htmlspecialchars($materia['nombre_materia']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                                </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-buttons">
                    <a href="infoProf.php?id=<?= $id_profesor ?>" class="btn btn-secondary">
                        ↩️ Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        💾 Guardar Cambios
                    </button>
                    <a href="selectProf.php" class="btn btn-info">
                        📋 Ver Listado
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validación del formulario
        document.getElementById('formEditarProfesor').addEventListener('submit', function(e) {
            const requiredFields = document.querySelectorAll('.form-input.required');
            let hasErrors = false;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e74c3c';
                    hasErrors = true;
                } else {
                    field.style.borderColor = '#e9ecef';
                }
            });

            if (hasErrors) {
                e.preventDefault();
                alert('⚠️ Por favor, completa todos los campos obligatorios marcados con *');
                return false;
            }

            // Mostrar loading
            const btnGuardar = document.getElementById('btnGuardar');
            btnGuardar.innerHTML = '⏳ Guardando...';
            btnGuardar.disabled = true;
            
            document.querySelector('.form-container').classList.add('loading');
        });

        // Validación en tiempo real
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.style.borderColor = '#e74c3c';
                } else {
                    this.style.borderColor = '#e9ecef';
                }
            });

            input.addEventListener('input', function() {
                if (this.style.borderColor === 'rgb(231, 76, 60)' && this.value.trim()) {
                    this.style.borderColor = '#e9ecef';
                }
            });
        });

        // Validación específica para email
        document.getElementById('email').addEventListener('blur', function() {
            if (this.value && !this.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/u)) {
                this.style.borderColor = '#e74c3c';
                alert('⚠️ Por favor, ingresa un email válido');
            }
        });

        // Efecto de animación al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const secciones = document.querySelectorAll('.seccion');
            secciones.forEach((seccion, index) => {
                seccion.style.opacity = '0';
                seccion.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    seccion.style.transition = 'all 0.5s ease';
                    seccion.style.opacity = '1';
                    seccion.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Confirmar antes de salir si hay cambios
        let formModified = false;
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('input', function() {
                formModified = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if (formModified) {
                e.preventDefault();
                e.returnValue = '¿Estás seguro de que quieres salir? Los cambios no guardados se perderán.';
            }
        });

        // Resetear flag cuando se envía el formulario
        document.getElementById('formEditarProfesor').addEventListener('submit', function() {
            formModified = false;
        });

        // Funcionalidad para materias
        function actualizarContadorMaterias() {
            const checkboxes = document.querySelectorAll('input[name="materias[]"]:checked');
            const contador = document.getElementById('contadorMaterias');
            if (contador) {
                contador.textContent = `${checkboxes.length} materia(s) seleccionada(s)`;
            }
        }

        // Agregar contador de materias
        const materiasGrid = document.querySelector('.materias-grid');
        if (materiasGrid) {
            const contadorDiv = document.createElement('div');
            contadorDiv.className = 'contador-materias';
            contadorDiv.innerHTML = '<span id="contadorMaterias"></span>';
            materiasGrid.parentNode.appendChild(contadorDiv);
            
            // Actualizar contador inicial
            actualizarContadorMaterias();
            
            // Escuchar cambios en checkboxes
            document.querySelectorAll('input[name="materias[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    actualizarContadorMaterias();
                    formModified = true;
                });
            });
        }

        // Función para seleccionar/deseleccionar todas las materias de un curso
        document.querySelectorAll('.curso-titulo').forEach(titulo => {
            titulo.addEventListener('click', function() {
                const cursoGroup = this.parentNode;
                const checkboxes = cursoGroup.querySelectorAll('input[type="checkbox"]');
                const todasSeleccionadas = Array.from(checkboxes).every(cb => cb.checked);
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !todasSeleccionadas;
                });
                
                actualizarContadorMaterias();
                formModified = true;
            });
            
            // Cambiar cursor para indicar que es clickeable
            titulo.style.cursor = 'pointer';
            titulo.title = 'Click para seleccionar/deseleccionar todas las materias de este curso';
        });
    </script>
</body>

</html>