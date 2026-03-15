<!doctype html>
<html lang="es">
<head>
  <title>Webleb</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="../css/styleLog.css" rel="stylesheet">
</head>
<body>
  <header>
    <div class="container">
      <div  class="logo">
        <img src="../images/IMGRFE.jpg" height="300px" alt="Logo">
      </div>
  </header>
  <div class="login-box">

    <form action = "includes/verificar.php" method = "POST">
      <div class="user-box">
        <input type="text" name="usuario" title="usuario" required="">
        <label>Usuario</label>
      </div>
      <div class="user-box">
        <input type="password" name="contrasena" title="contraseña" required="">
        <label>Contraseña</label>
      </div>
      <div class="btn-box">
        <button type="submit" id="btn-box" title="button">ENTRAR</button>
      </div>
    </form>
  </div>
</body>
</html>
