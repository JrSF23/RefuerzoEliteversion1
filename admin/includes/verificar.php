<?php
session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que los campos no estén vacíos
    if (!empty(trim($_POST["usuario"])) && !empty($_POST["contrasena"])) {
        $usuario = trim($_POST["usuario"]);
        $contrasena = $_POST["contrasena"];

        $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($contrasena, $usuario_db["contrasena"])) {
                $_SESSION["usuario"] = $usuario_db["usuario"];
                $_SESSION["rol"] = $usuario_db["rol"];
                header("Location: ../bienvenida.php");
                exit;
            } else {
                header("Location: login_error.php");
                exit;
            }
        } else {
            header("Location: login_error.php");
            exit;
        }
    }else {
        header("Location: login_error.php");
        exit;
    }
}