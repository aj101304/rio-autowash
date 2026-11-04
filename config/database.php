<?php
date_default_timezone_set('America/Bogota');

// Lee variables de Clever Cloud (MYSQL_ADDON_*) o de Render (DB_*) o DATABASE_URL
if(getenv('DATABASE_URL')){
    $url = parse_url(getenv('DATABASE_URL'));
    $host = ($url['host'] ?? 'localhost') . ':' . ($url['port'] ?? 3306);
    $user = $url['user'] ?? '';
    $pass = $url['pass'] ?? '';
    $db   = ltrim($url['path'] ?? '', '/');
} else {
    // Soporta ambos nombres: DB_HOST y MYSQL_ADDON_HOST (Clever)
    $host_raw = getenv('DB_HOST') ?: getenv('MYSQL_ADDON_HOST') ?: '';
    $port = getenv('MYSQL_ADDON_PORT') ?: getenv('DB_PORT') ?: '';
    $host = $host_raw;
    if($port && strpos($host_raw, ':')===false){
        $host = $host_raw . ':' . $port;
    }
    $db   = getenv('DB_NAME') ?: getenv('MYSQL_ADDON_DB') ?: 'TU_BASE_AQUI';
    $user = getenv('DB_USER') ?: getenv('MYSQL_ADDON_USER') ?: 'TU_USUARIO_AQUI';
    $pass = getenv('DB_PASS') ?: getenv('MYSQL_ADDON_PASSWORD') ?: 'TU_CLAVE_AQUI';
}

$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
try { $pdo = new PDO($dsn, $user, $pass, $options); } 
catch (PDOException $e) { die('Error DB: '.$e->getMessage().'<br>Host: '.htmlspecialchars($host).'<br>User: '.htmlspecialchars($user).'<br>Revisa variables en Render'); }
if(session_status()===PHP_SESSION_NONE) session_start();
?>

