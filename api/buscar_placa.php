<?php
require '../config/database.php';
$q = $_GET['q'] ?? '';
$q = trim($q);
if(strlen($q) < 2){ echo json_encode([]); exit; }

// Buscar placas similares
$stmt = $pdo->prepare("SELECT v.*, GROUP_CONCAT(d.servicio SEPARATOR ' + ') as servicios FROM ventas v LEFT JOIN venta_detalle d ON d.venta_id=v.id WHERE v.placa LIKE CONCAT('%', ?, '%') GROUP BY v.id ORDER BY v.fecha DESC, v.id DESC LIMIT 5");
$stmt->execute([$q]);
$rows = $stmt->fetchAll();

// También total histórico de esa placa exacta
$hist = $pdo->prepare("SELECT COUNT(*) as visitas, SUM(valor_total) as total_gastado, MAX(fecha) as ultima FROM ventas WHERE placa = ?");
$hist->execute([strtoupper($q)]);
$historial = $hist->fetch();

echo json_encode(['resultados'=>$rows, 'historial'=>$historial, 'query'=>$q]);
?>