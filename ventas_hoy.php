<?php
require 'includes/auth.php';
require 'config/database.php';
$fecha=$_GET['fecha'] ?? date('Y-m-d');
$q=$_GET['q'] ?? '';
$sql="SELECT v.*, GROUP_CONCAT(CONCAT(d.servicio, ' ($', FORMAT(d.precio,0,'de_DE'), ')') SEPARATOR ' + ') as servicios_txt FROM ventas v LEFT JOIN venta_detalle d ON d.venta_id=v.id WHERE v.fecha=? ";
$params=[$fecha];
if($q!==''){ $sql.=" AND (v.placa LIKE CONCAT('%', ?, '%') OR v.numero_ticket LIKE CONCAT('%', ?, '%') OR d.servicio LIKE CONCAT('%', ?, '%')) "; $params[]=$q; $params[]=$q; $params[]=$q; }
$sql.=" GROUP BY v.id ORDER BY v.id DESC";
$stmt=$pdo->prepare($sql); $stmt->execute($params); $ventas=$stmt->fetchAll();
$totalStmt=$pdo->prepare("SELECT SUM(valor_total) as t FROM ventas WHERE fecha=?"); $totalStmt->execute([$fecha]); $total=$totalStmt->fetch()['t'] ?? 0;
require 'includes/header.php';
?>
<div class="mt-3">
<div class="d-flex justify-content-between align-items-center">
<h5 class="fw-bold mb-0">Ventas <?= $fecha ?></h5>
<form class="d-flex gap-1"><input type="date" name="fecha" value="<?= $fecha ?>" class="form-control form-control-sm"><input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar placa/ticket" class="form-control form-control-sm" style="max-width:130px"><button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button></form>
</div>
<?php if(isset($_GET['ok'])): ?><div class="alert alert-success py-2 mt-2">Venta guardada ✓</div><?php endif; ?>
<div class="card card-rio p-3 mt-3"><div class="row text-center"><div class="col-6"><h3 class="fw-bold mb-0" style="color:#1e40af">$<?= number_format($total,0,',','.') ?></h3><small class="text-muted">Total día</small></div><div class="col-6"><h3 class="fw-bold mb-0"><?= count($ventas) ?></h3><small class="text-muted">Vehículos</small></div></div>
<a href="generar_pdf.php?fecha=<?= $fecha ?>&q=<?= urlencode($q) ?>" target="_blank" class="btn btn-dark w-100 mt-3"><i class="bi bi-file-earmark-pdf"></i> Generar PDF Cierre</a>
<a href="liquidar_pista.php?fecha=<?= $fecha ?>" class="btn btn-primary w-100 mt-2" style="background:#1e40af"><i class="bi bi-cash-coin"></i> 💰 Liquidar Pista</a></div>
<div class="mt-3"><?php if(empty($ventas)): ?><div class="text-center text-muted py-5">No hay ventas</div><?php else: foreach($ventas as $v): ?>
<div class="card card-rio p-3 mb-2"><div class="d-flex justify-content-between"><div><span class="fw-bold">#<?= htmlspecialchars($v['numero_ticket']) ?></span> - <b><?= htmlspecialchars($v['placa']) ?></b> <span class="badge bg-light text-dark ms-1"><?= $v['tipo_vehiculo'] ?></span><br><small class="text-muted"><?= htmlspecialchars($v['servicios_txt'] ?? 'Sin detalle') ?></small><br><small class="text-muted"><i class="bi bi-clock"></i> <?= $v['hora_llegada'] ?> Pista <?= $v['pista'] ?></small></div><div class="text-end"><div class="fw-bold" style="color:#1e40af">$<?= number_format($v['valor_total'],0,',','.') ?></div><div class="mt-1"><a href="editar_venta.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a> <a href="eliminar_venta.php?id=<?= $v['id'] ?>&fecha=<?= $fecha ?>" onclick="return confirm('¿Eliminar?')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a></div></div></div></div>
<?php endforeach; endif; ?></div></div>
<?php require 'includes/footer.php'; ?>
