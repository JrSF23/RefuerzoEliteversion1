<?php
require_once 'config.php';

// Verificar que se haya proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: selectProf.php");
    exit();
}

$id_profesor = (int)$_GET['id'];

// Obtener información básica del profesor y sus detalles
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

// Obtener materias que imparte el profesor
$sql_materias = "SELECT m.nombre_materia, c.nombre_curso 
                 FROM materias m 
                 INNER JOIN cursos c ON m.id_curso = c.id_curso
                 INNER JOIN profesor_materia pm ON m.id_materia = pm.id_materia 
                 WHERE pm.id_profesor = :id_profesor
                 ORDER BY c.nombre_curso, m.nombre_materia";
$stmt_materias = $pdo->prepare($sql_materias);
$stmt_materias->execute([':id_profesor' => $id_profesor]);
$materias = $stmt_materias->fetchAll(PDO::FETCH_ASSOC);

// Obtener alumnos suscritos
$sql_alumnos = "SELECT a.nombre_completo, a.contacto_alumno, a.colegio_alumno, a.cantidad_materias
                FROM alumnos a 
                WHERE a.id_profesor = :id_profesor
                ORDER BY a.nombre_completo";
$stmt_alumnos = $pdo->prepare($sql_alumnos);
$stmt_alumnos->execute([':id_profesor' => $id_profesor]);
$alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);

// Calcular estadísticas
$total_alumnos = count($alumnos);
$total_materias = count($materias);

// Calcular años en la institución de forma segura
$anos_institucion = 0;
if (!empty($profesor['fecha_contratacion'])) {
    $fecha_contratacion = new DateTime($profesor['fecha_contratacion']);
    $fecha_actual = new DateTime();
    $anos_institucion = $fecha_actual->diff($fecha_contratacion)->y;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha del Profesor - <?= htmlspecialchars($profesor['nombre_completo']) ?></title>
    <link rel="stylesheet" href="../../css/estilosFormulario.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .ficha-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .ficha-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .ficha-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="10" cy="90" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .ficha-title {
            font-size: 2.2em;
            font-weight: 700;
            margin: 0 0 10px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }

        .ficha-subtitle {
            font-size: 1.1em;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .ficha-content {
            padding: 0;
        }

        .seccion {
            margin: 0;
            padding: 30px;
            border-bottom: 1px solid #ecf0f1;
        }

        .seccion:last-child {
            border-bottom: none;
        }

        .seccion-personal {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .seccion-profesional {
            background: linear-gradient(135deg, #fff3e0 0%, #ffecb3 100%);
        }

        .seccion-estadisticas {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        }

        .seccion-alumnos {
            background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
        }

        .seccion h2 {
            color: #2c3e50;
            font-size: 1.8em;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.8);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #3498db;
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        .info-label {
            font-weight: 600;
            color: #34495e;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.1em;
            color: #2c3e50;
            font-weight: 500;
        }

        .materias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .materia-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #f39c12;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .materia-card:hover {
            transform: translateY(-5px);
        }

        .materia-nombre {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .materia-curso {
            color: #7f8c8d;
            font-size: 0.9em;
        }

        .estadisticas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .estadistica-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .estadistica-card:hover {
            transform: scale(1.05);
        }

        .estadistica-numero {
            font-size: 2.5em;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .estadistica-label {
            color: #7f8c8d;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 1px;
        }

        .alumnos-lista {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .alumno-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #9b59b6;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .alumno-nombre {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1.1em;
        }

        .alumno-info {
            color: #7f8c8d;
            font-size: 0.9em;
            margin: 3px 0;
        }

        .foto-placeholder {
            width: 150px;
            height: 200px;
            background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
            font-size: 3em;
            margin: 0 auto 20px auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .botones-accion {
            padding: 30px;
            background: #f8f9fa;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
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
        }

        .btn-volver {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            color: white;
        }

        .btn-volver:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-editar {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }

        .btn-editar:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .btn-imprimir {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
        }

        .btn-imprimir:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }

        .no-data {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 20px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .ficha-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .ficha-header {
                padding: 20px;
            }
            
            .ficha-title {
                font-size: 1.8em;
            }
            
            .seccion {
                padding: 20px;
            }
            
            .info-grid,
            .materias-grid,
            .estadisticas-grid,
            .alumnos-lista {
                grid-template-columns: 1fr;
            }
            
            .botones-accion {
                padding: 20px;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .ficha-container {
                box-shadow: none;
                border-radius: 0;
            }
            
            .botones-accion {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="ficha-container">
        <div class="ficha-header">
            <h1 class="ficha-title">📋 FICHA DE PROFESOR</h1>
            <p class="ficha-subtitle">Información Completa del Docente</p>
        </div>

        <div class="ficha-content">
            <!-- Información Personal -->
            <div class="seccion seccion-personal">
                <h2>👤 INFORMACIÓN PERSONAL</h2>
                
                <div class="foto-placeholder">
                    📷
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">NOMBRE COMPLETO</div>
                        <div class="info-value"><?= htmlspecialchars($profesor['nombre_completo']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">FECHA DE NACIMIENTO</div>
                        <div class="info-value"><?= $profesor['fecha_nacimiento'] ? htmlspecialchars(date('d/m/Y', strtotime($profesor['fecha_nacimiento']))) : 'No especificada' ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">DIRECCIÓN</div>
                        <div class="info-value"><?= htmlspecialchars($profesor['domicilio_profesor']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">CORREO ELECTRÓNICO</div>
                        <div class="info-value"><?= $profesor['email'] ? htmlspecialchars($profesor['email']) : 'No especificado' ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">NÚMERO DE TELÉFONO</div>
                        <div class="info-value"><?= htmlspecialchars($profesor['contacto_profesor']) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">DOCUMENTO DE IDENTIDAD</div>
                        <div class="info-value"><?= $profesor['dni'] ? htmlspecialchars($profesor['dni']) : 'No especificado' ?></div>
                    </div>
                </div>
            </div>

            <!-- Información Profesional -->
            <div class="seccion seccion-profesional">
                <h2>🎓 INFORMACIÓN PROFESIONAL</h2>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">NIVEL DE ESTUDIOS</div>
                        <div class="info-value"><?= $profesor['nivel_estudios'] ? htmlspecialchars($profesor['nivel_estudios']) : 'No especificado' ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">ESPECIALIZACIÓN</div>
                        <div class="info-value"><?= $profesor['especializacion'] ? htmlspecialchars($profesor['especializacion']) : 'No especificada' ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">AÑOS DE EXPERIENCIA</div>
                        <div class="info-value"><?= $profesor['anos_experiencia'] ? htmlspecialchars($profesor['anos_experiencia']) . ' años' : 'No especificado' ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">FECHA DE CONTRATACIÓN</div>
                        <div class="info-value"><?= $profesor['fecha_contratacion'] ? htmlspecialchars(date('d/m/Y', strtotime($profesor['fecha_contratacion']))) : 'No especificada' ?></div>
                    </div>
                </div>

                <!-- Materias que imparte -->
                <h3 style="color: #2c3e50; margin: 30px 0 15px 0;">📚 MATERIAS QUE IMPARTE</h3>
                <?php if (!empty($materias)): ?>
                    <div class="materias-grid">
                        <?php foreach ($materias as $materia): ?>
                            <div class="materia-card">
                                <div class="materia-nombre"><?= htmlspecialchars($materia['nombre_materia']) ?></div>
                                <div class="materia-curso"><?= htmlspecialchars($materia['nombre_curso']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        No hay materias asignadas a este profesor
                    </div>
                <?php endif; ?>

                <!-- Experiencia Profesional -->
                <h3 style="color: #2c3e50; margin: 30px 0 15px 0;">💼 EXPERIENCIA PROFESIONAL</h3>
                <div class="info-item">
                    <div class="info-value" style="font-style: italic; line-height: 1.6;">
                        <?= $profesor['experiencia_profesional'] ? htmlspecialchars($profesor['experiencia_profesional']) : 'He impartido clases a estudiantes de diferentes niveles educativos. Como profesional me considero responsable, puntual, comprometido y paciente con mis alumnos.' ?>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="seccion seccion-estadisticas">
                <h2>📊 ESTADÍSTICAS</h2>
                
                <div class="estadisticas-grid">
                    <div class="estadistica-card">
                        <div class="estadistica-numero"><?= $total_alumnos ?></div>
                        <div class="estadistica-label">Alumnos Suscritos</div>
                    </div>
                    
                    <div class="estadistica-card">
                        <div class="estadistica-numero"><?= $total_materias ?></div>
                        <div class="estadistica-label">Materias Asignadas</div>
                    </div>
                    
                    <div class="estadistica-card">
                        <div class="estadistica-numero"><?= $anos_institucion ?></div>
                        <div class="estadistica-label">Años en la Institución</div>
                    </div>
                </div>
            </div>

            <!-- Lista de Alumnos -->
            <div class="seccion seccion-alumnos">
                <h2>🎓 ALUMNOS SUSCRITOS</h2>
                
                <?php if (!empty($alumnos)): ?>
                    <div class="alumnos-lista">
                        <?php foreach ($alumnos as $alumno): ?>
                            <div class="alumno-card">
                                <div class="alumno-nombre"><?= htmlspecialchars($alumno['nombre_completo']) ?></div>
                                <div class="alumno-info">📱 <?= htmlspecialchars($alumno['contacto_alumno']) ?></div>
                                <div class="alumno-info">🏫 <?= htmlspecialchars($alumno['colegio_alumno']) ?></div>
                                <div class="alumno-info">📚 <?= htmlspecialchars($alumno['cantidad_materias']) ?> materias</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        Este profesor no tiene alumnos suscritos actualmente
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="botones-accion">
            <a href="selectProf.php" class="btn btn-volver">⬅ Volver al Listado</a>
            <a href="editarProf.php?id=<?= $profesor['id_profesor'] ?>" class="btn btn-editar">✏️ Editar Información</a>
            <button onclick="window.print()" class="btn btn-imprimir">🖨️ Imprimir Ficha</button>
        </div>
    </div>

    <script>
        // Efecto de animación al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.info-item, .materia-card, .estadistica-card, .alumno-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Funcionalidad de impresión mejorada
        function imprimirFicha() {
            window.print();
        }
    </script>
</body>

</html>