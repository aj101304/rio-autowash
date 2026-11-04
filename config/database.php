<?php
date_default_timezone_set('America/Bogota'); // Hora Colombia
// Soporte para Render con variables de entorno, fallback a InfinityFree
$host = getenv('DB_HOST') ?: 'TU_HOST_AQUI';
$db   = getenv('DB_NAME') ?: 'TU_BASE_AQUI';
$user = getenv('DB_USER') ?: 'TU_USUARIO_AQUI';
$pass = getenv('DB_PASS') ?: 'TU_CLAVE_AQUI';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
try { $pdo = new PDO($dsn, $user, $pass, $options); } 
catch (PDOException $e) { die('Error DB: '.$e->getMessage().'<br>Revisa config/database.php o variables de entorno en Render'); }
if(session_status()===PHP_SESSION_NONE) session_start();
?>
