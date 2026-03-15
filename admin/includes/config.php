<?php
// Leer archivo INI
$config = parse_ini_file(__DIR__ . '/db_config.ini');

$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

// Ruta al directorio de logs (debe quedar en admin/logs)
$logDir = __DIR__ . '/../logs';

// Normalizar separadores (evita problemas en Windows)
$logDir = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $logDir);

// Intentar crear el directorio si no existe y manejar errores
if (!is_dir($logDir)) {
    if (!mkdir($logDir, 0777, true) && !is_dir($logDir)) {
        // Registrar en el log de errores de PHP y usar carpeta temporal como fallback
        error_log("[config.php] No se pudo crear el directorio de logs: $logDir");
        // Como último recurso, usar el directorio temporal del sistema
        $logDir = sys_get_temp_dir();
    }
}


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $logFile = $logDir . DIRECTORY_SEPARATOR . 'conexion.log';
    @file_put_contents($logFile, date("Y-m-d H:i:s") . " - ✅ Conexión exitosa\n", FILE_APPEND);
} catch (PDOException $e) {
    $logFile = $logDir . DIRECTORY_SEPARATOR . 'conexion.log';
    @file_put_contents($logFile, date("Y-m-d H:i:s") . " - ❌ Error: " . $e->getMessage() . "\n", FILE_APPEND);
    die("❌ Error de conexión: " . $e->getMessage());
}
?>
