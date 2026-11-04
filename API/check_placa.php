<?php
require 'includes/auth.php';
require 'config/database.php';
date_default_timezone_set('America/Bogota');
$placa = strtoupper(trim($_GET['placa'] ?? ''));
$fecha = $_GET['fecha'] ?? date('Y-m-d');
header('Content-Type: application/json');
if(!$placa || strlen($placa)<3){ echo json_encode(['existe'=>false]); exit; }
$stmt = $pdo->prepare("SELECT numero_ticket, hora_llegada, pista, placa FROM ventas WHERE placa=? AND fecha=? ORDER BY hora_llegada DESC LIMIT 5");
$stmt->execute([$placa, $fecha]);
$rows = $stmt->fetchAll();
if($rows){
    echo json_encode(['existe'=>true, 'tickets'=>$rows]);
} else {
    echo json_encode(['existe'=>false]);
}
