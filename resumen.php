<?php
require 'includes/auth.php';
require 'config/database.php';
$fecha=$_GET['fecha']??date('Y-m-d');
$ventas=$pdo->prepare("SELECT COUNT(*) as cant, SUM(valor_total) as total FROM ventas WHERE fecha=?"); $ventas->execute([$fecha]); $ventas=$ventas->fetch();
$porVeh=$pdo->prepare("SELECT tipo_vehiculo, COUNT(*) as cant, SUM(valor_total) as total FROM ventas WHERE fecha=? GROUP BY tipo_vehiculo"); $porVeh->execute([$fecha]); $porVeh=$porVeh->fetchAll();
$porServ=$pdo->prepare("SELECT d.servicio, COUNT(*) as cant, SUM(d.precio) as total FROM venta_detalle d JOIN ventas v ON v.id=d.venta_id WHERE v.fecha=? GROUP BY d.servicio ORDER BY total DESC"); $porServ->execute([$fecha]); $porServ=$porServ->fetchAll();
require 'includes/header.php';
?>
<div class="mt-3">
<h5 class="fw-bold">Resumen <?= $fecha ?> <a href="resumen.php?fecha=<?= date('Y-m-d', strtotime($fecha.' -1 day')) ?>" class="btn btn-sm btn-light"><</a> <a href="resumen.php?fecha=<?= date('Y-m-d', strtotime($fecha.' +1 day')) ?>" class="btn btn-sm btn-light">></a></h5>
<div class="card card-rio p-3 mt-3 text-center"><h2 class="fw-bold mb-0" style="color:#1e40af">$<?= number_format($ventas['total']??0,0,',','.') ?></h2><small><?= $ventas['cant']??0 ?> vehículos</small><br><a href="generar_pdf.php?fecha=<?= $fecha ?>" target="_blank" class="btn btn-dark btn-sm mt-2">Ver PDF Cierre</a></div>
<div class="card card-rio p-3 mt-3"><h6 class="fw-bold">Por Tipo Vehículo</h6><?php foreach($porVeh as $r): ?><div class="d-flex justify-content-between border-bottom py-2"><span><?= $r['tipo_vehiculo'] ?> (<?= $r['cant'] ?>)</span><b>$<?= number_format($r['total'],0,',','.') ?></b></div><?php endforeach; ?></div>
<div class="card card-rio p-3 mt-3"><h6 class="fw-bold">Por Servicio (desglosado)</h6><?php foreach($porServ as $r): ?><div class="d-flex justify-content-between border-bottom py-2"><div><div class="fw-semibold small"><?= $r['servicio'] ?></div><small class="text-muted"><?= $r['cant'] ?> veces</small></div><b>$<?= number_format($r['total'],0,',','.') ?></b></div><?php endforeach; ?></div>
</div>
<?php require 'includes/footer.php'; ?>
