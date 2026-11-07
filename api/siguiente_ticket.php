<?php
require '../config/database.php';
header('Content-Type: application/json');
$fecha = $_GET['fecha'] ?? date('Y-m-d');
try {
  $stmt = $pdo->prepare("SELECT MAX(CAST(numero_ticket AS UNSIGNED)) as max_t FROM ventas WHERE fecha = ?");
  $stmt->execute([$fecha]);
  $maxHoy = $stmt->fetchColumn();
  if($maxHoy){
    $siguiente = (int)$maxHoy + 1;
  } else {
    $stmt2 = $pdo->query("SELECT MAX(CAST(numero_ticket AS UNSIGNED)) as max_g FROM ventas");
    $maxG = $stmt2->fetchColumn();
    $siguiente = $maxG ? (int)$maxG + 1 : 1;
  }
  echo json_encode(['siguiente'=>$siguiente, 'fecha'=>$fecha]);
} catch(Exception $e){
  echo json_encode(['siguiente'=>'', 'error'=>$e->getMessage()]);
}
?>
