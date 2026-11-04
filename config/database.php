<?php
date_default_timezone_set('America/Bogota');
if(getenv('DATABASE_URL')){
    $url = parse_url(getenv('DATABASE_URL'));
    $host = ($url['host'] ?? 'localhost') . ':' . ($url['port'] ?? 3306);
    $user = $url['user'] ?? '';
    $pass = $url['pass'] ?? '';
    $db   = ltrim($url['path'] ?? '', '/');
} else {
    $host_raw = getenv('DB_HOST') ?: getenv('MYSQL_ADDON_HOST') ?: '';
    $port = getenv('MYSQL_ADDON_PORT') ?: getenv('DB_PORT') ?: '';
    $host = $host_raw;
    if($port && strpos($host_raw, ':')===false){
        $host = $host_raw . ':' . $port;
    }
    $db   = getenv('DB_NAME') ?: getenv('MYSQL_ADDON_DB') ?: '';
    $user = getenv('DB_USER') ?: getenv('MYSQL_ADDON_USER') ?: '';
    $pass = getenv('DB_PASS') ?: getenv('MYSQL_ADDON_PASSWORD') ?: '';
}
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
try { $pdo = new PDO($dsn, $user, $pass, $options); } 
catch (PDOException $e) { die('Error DB: '.$e->getMessage().'<br>Host: '.htmlspecialchars($host).'<br>User: '.htmlspecialchars($user)); }
if(session_status()===PHP_SESSION_NONE) session_start();
