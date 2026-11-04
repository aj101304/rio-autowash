<?php
require 'includes/auth.php';
require 'config/database.php';

$fecha = $_GET['fecha'] ?? date('Y-m-d');
$q = $_GET['q'] ?? '';
$params = [$fecha];
$sqlWhere = " WHERE v.fecha=? ";
if($q !== ''){
    $sqlWhere .= " AND (v.placa LIKE CONCAT('%', ?, '%') OR v.numero_ticket LIKE CONCAT('%', ?, '%')) ";
    $params[] = $q;
    $params[] = $q;
}

$stmt = $pdo->prepare("SELECT v.*, GROUP_CONCAT(CONCAT(d.servicio, ' ($', FORMAT(d.precio,0,'de_DE'), ')') SEPARATOR ' + ') as servicios_txt FROM ventas v LEFT JOIN venta_detalle d ON d.venta_id=v.id $sqlWhere GROUP BY v.id ORDER BY v.numero_ticket ASC");
$stmt->execute($params);
$ventas = $stmt->fetchAll();

$totalStmt = $pdo->prepare("SELECT SUM(valor_total) as t FROM ventas v $sqlWhere");
$totalStmt->execute($params);
$total = $totalStmt->fetch()['t'] ?? 0;

$porVeh = $pdo->prepare("SELECT tipo_vehiculo, COUNT(*) as cant, SUM(valor_total) as total FROM ventas v $sqlWhere GROUP BY tipo_vehiculo");
$porVeh->execute($params);
$porVeh = $porVeh->fetchAll();

$porServ = $pdo->prepare("SELECT d.servicio, COUNT(*) as cant, SUM(d.precio) as total FROM venta_detalle d JOIN ventas v ON v.id=d.venta_id $sqlWhere GROUP BY d.servicio");
$porServ->execute($params);
$porServ = $porServ->fetchAll();

// --- GUARDAR PDF AUTOMATICAMENTE ---
// Creamos carpeta pdfs si no existe
$dirPdfs = __DIR__ . '/pdfs';
if (!is_dir($dirPdfs)) {
    mkdir($dirPdfs, 0777, true);
}
// Nombre del archivo: 2026-10-21.pdf o 2026-10-21_BUSQUEDA.pdf si hay filtro
$safeQ = $q ? '_' . preg_replace('/[^A-Za-z0-9]/','', $q) : '';
$nombreArchivo = $fecha . $safeQ . '.html'; // lo guardamos como HTML imprimible (se ve como PDF y abre en cualquier celular)
$rutaArchivo = $dirPdfs . '/' . $nombreArchivo;

ob_start();
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Rio <?= $fecha ?></title>
<style>body{font-family:Arial; font-size:11px; padding:15px} h2{margin:0} table{width:100%; border-collapse:collapse; margin-top:8px} th,td{border:1px solid #333; padding:5px; text-align:left; font-size:10px} th{background:#1e40af; color:white} .total{font-weight:bold; font-size:14px; margin-top:12px; text-align:right} @media print{ .no-print{display:none} }</style></head><body>
<div style="display:flex; justify-content:space-between; border-bottom:2px solid #1e40af; padding-bottom:8px"><div><h2 style="color:#1e40af">RIO AUTOWASH</h2><div>Reporte de ventas - <?= date('d/m/Y', strtotime($fecha)) ?> - <?= $_SESSION['nombre'] ?></div><div><?= $q? "Filtro: $q - " : "" ?><?= count($ventas) ?> vehículos</div></div><div style="text-align:right"><div style="font-size:18px; font-weight:bold">Total: $<?= number_format($total,0,',','.') ?></div></div></div>
<table><thead><tr><th>Ticket</th><th>Placa</th><th>Vehículo</th><th>Servicios</th><th>Valor</th><th>Hora</th></tr></thead><tbody><?php foreach($ventas as $v): ?><tr><td><?= $v['numero_ticket'] ?></td><td><b><?= $v['placa'] ?></b></td><td><?= $v['tipo_vehiculo'] ?></td><td><?= htmlspecialchars($v['servicios_txt']) ?></td><td style="text-align:right"><b>$<?= number_format($v['valor_total'],0,',','.') ?></b></td><td><?= $v['hora_llegada'] ?></td></tr><?php endforeach; ?></tbody></table>
<div class="total">TOTAL: $<?= number_format($total,0,',','.') ?></div>
<div style="display:flex; gap:20px; margin-top:20px"><div style="flex:1"><h3>Por Vehículo</h3><table><tr><th>Vehículo</th><th>Cant</th><th>Total</th></tr><?php foreach($porVeh as $r): ?><tr><td><?= $r['tipo_vehiculo'] ?></td><td><?= $r['cant'] ?></td><td>$<?= number_format($r['total'],0,',','.') ?></td></tr><?php endforeach; ?></table></div><div style="flex:1"><h3>Por Servicio</h3><table><tr><th>Servicio</th><th>Cant</th><th>Total</th></tr><?php foreach($porServ as $r): ?><tr><td><?= $r['servicio'] ?></td><td><?= $r['cant'] ?></td><td>$<?= number_format($r['total'],0,',','.') ?></td></tr><?php endforeach; ?></table></div></div>
<div class="no-print" style="margin-top:25px; text-align:center"><button onclick="window.print()" style="padding:14px 28px; background:#1e40af; color:white; border:none; border-radius:10px; cursor:pointer; font-weight:bold">🖨️ Imprimir / Guardar como PDF</button><br><a href="ventas_hoy.php?fecha=<?= $fecha ?>" style="display:inline-block; margin-top:10px; color:#1e40af">← Volver a ventas</a> | <a href="pdfs_guardados.php" style="display:inline-block; margin-top:10px; color:#1e40af">📁 Ver todos los PDFs guardados</a></div>
</body></html>
<?php
$htmlContent = ob_get_clean();
file_put_contents($rutaArchivo, $htmlContent);
echo $htmlContent;
