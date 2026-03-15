<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <title>Interface de centro de control</title>
    <link rel="stylesheet" href="../css/styleWel.css" />
</head>

<body>
    <h3 class="bienvenida">Bienvenido a tu centro de trabajo  <?php echo $_SESSION["usuario"]; ?> !</h3>
    <header>
        <div class="nav-container">
            <nav class="nav">
                <ul>
                    <li><a href="includes/selectAlum.php">ALUMNOS</a></li>
                    <li><a href="includes/selectProf.php">PROFESORES</a></li>
                    <li><a href="includes/selectMat.php">MATERIAS</a></li>
                    <li><a href="includes/selectCur.php">CURSOS</a></li>
                    <li><a href="includes/selectTut.php">TUTORES</a></li>
                    <li><a href="includes/selectPag.php">PAGOS</a></li>
                </ul>
            </nav>
            <div class="menu-wrapper">
                <button class="menu-btn" title="selecciona alguna operación" id="menuBtn"><i class="bi bi-list"></i></button>
                <div class="dropdown-menu hidden" id="dropdownMenuHidden">
                    <a href="includes/insertarPanel.php">AÑADIR</a>
                    <a href="includes/dashboard.php">CONSULTAR</a>
                    <a href="includes/logout.php">CERRAR SESION</a>
                </div>
            </div>
        </div>
    </header>
    <main>
        <h1>CENTRO DE ADMINISTRACION</h1>
        <form action="includes/buscarAlumno.php" method="post" class="buscador">
            <div class="inputBus">
                <input type="text" class="int" name="buscar"  placeholder="ID o nombre del alumno!">
                <button type="submit" class="icon" name="btnSeacrch" title="click"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </main>
    <div class="footer">
        <a href="includes/ayuda.php" title="cliquea para informarte">Acerca del uso</a>
    </div>
    <div class="copyright">
        <p>© 2025 - Todos los derechos reservados</p>
    </div>

    <script src="../js/app.js"></script>

</body>

</html>