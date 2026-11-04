<?php
require '../includes/auth.php';
require '../config/database.php';
date_default_timezone_set('America/Bogota');
$placa = strtoupper(trim($_GET['placa'] ?? ''));
header('Content-Type: application/json');
if(strlen($placa)<2){ echo json_encode([]); exit; }
$stmt = $pdo->prepare("SELECT placa, tipo_vehiculo, COUNT(*) as veces, MAX(fecha) as ultima_fecha FROM ventas WHERE placa LIKE ? GROUP BY placa, tipo_vehiculo ORDER BY veces DESC LIMIT 10");
$stmt->execute(["%$placa%"]);
echo json_encode($stmt->fetchAll());
