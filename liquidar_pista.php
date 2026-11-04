<?php
require 'includes/auth.php';
require 'config/database.php';

$fecha = $_GET['fecha'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT v.*, GROUP_CONCAT(CONCAT(d.servicio) SEPARATOR ', ') as servicios_txt 
FROM ventas v LEFT JOIN venta_detalle d ON d.venta_id=v.id 
WHERE v.fecha=? 
GROUP BY v.id ORDER BY v.pista, v.id");
$stmt->execute([$fecha]);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pistas = ['1','2','3','4','5','6'];
$porPista = [];
$totalDia = 0;
$cantDia = 0;
foreach($pistas as $p) $porPista[$p] = ['total'=>0,'cant'=>0,'ventas'=>[]];
$porPista['SIN PISTA'] = ['total'=>0,'cant'=>0,'ventas'=>[]];

foreach($ventas as $v){
  $p = $v['pista'];
  if($p==='' || $p===null) $p='SIN PISTA';
  if(!isset($porPista[$p])) $porPista[$p]=['total'=>0,'cant'=>0,'ventas'=>[]];
  $porPista[$p]['ventas'][] = $v;
  $porPista[$p]['total'] += (int)$v['valor_total'];
  $porPista[$p]['cant']++;
  $totalDia += (int)$v['valor_total'];
  $cantDia++;
}

require 'includes/header.php';
?>
<div class="mt-3">
<div class="d-flex justify-content-between align-items-center">
<h5 class="fw-bold mb-0">💰 Liquidar Pistas</h5>
<form class="d-flex gap-1">
<input type="date" name="fecha" value="<?= $fecha ?>" class="form-control form-control-sm">
<button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
</form>
</div>

<div class="card card-rio p-3 mt-3">
<div class="row text-center align-items-center">
<div class="col-4"><h6 class="fw-bold mb-0" style="color:#1e40af; word-break:break-all">$<?= number_format($totalDia,0,',','.') ?></h6><small class="text-muted">Total día</small></div>
<div class="col-3"><h6 class="fw-bold mb-0"><?= $cantDia ?></h6><small class="text-muted">Vehículos</small></div>
<div class="col-5">
<label class="small fw-bold">% Global</label>
<div class="input-group input-group-sm">
<input id="porcGlobal" type="number" class="form-control" placeholder="40" min="0" max="100">
<span class="input-group-text">%</span>
</div>
<button id="btnAplicarGlobal" class="btn btn-sm btn-outline-primary w-100 mt-1">Aplicar a todas</button>
</div>
</div>
</div>

<div class="mt-3">
<?php foreach($pistas as $p): 
  $data = $porPista[$p];
  $cant = $data['cant'];
  $total = $data['total'];
  $lista = $data['ventas'];
?>
<div class="card card-rio p-3 mb-3" data-pista="<?= $p ?>">
<div class="d-flex justify-content-between align-items-center mb-2">
<div>
<span class="badge bg-dark fs-6">PISTA <?= htmlspecialchars($p) ?></span>
<span class="ms-2 small text-muted"><?= $cant ?> carros</span>
</div>
<div class="fw-bold fs-5" style="color:#1e40af">$<?= number_format($total,0,',','.') ?></div>
</div>

<div class="row g-2 align-items-end mb-2">
<div class="col-4">
<label class="small fw-bold">% Lavador</label>
<div class="input-group">
<input type="number" class="form-control form-control-lg porc-input" data-total="<?= $total ?>" value="40" min="0" max="100">
<span class="input-group-text">%</span>
</div>
</div>
<div class="col-8">
<label class="small fw-bold">A liquidar</label>
<div class="form-control form-control-lg fw-bold bg-light total-liquidar" style="color:#16a34a">$0</div>
</div>
</div>

<?php if($cant>0): ?>
<button class="btn btn-sm btn-outline-dark w-100 mt-1" data-bs-toggle="collapse" data-bs-target="#detalle-<?= $p ?>">
<i class="bi bi-list-ul"></i> Ver placas y tickets (<?= $cant ?>) <i class="bi bi-chevron-down float-end"></i>
</button>
<div class="collapse mt-2" id="detalle-<?= $p ?>">
<div class="table-responsive" style="max-height:300px; overflow:auto">
<table class="table table-sm table-striped small mb-0">
<thead class="table-dark sticky-top"><tr><th># Ticket</th><th>Placa</th><th>Servicio</th><th class="text-end">Valor</th></tr></thead>
<tbody>
<?php foreach($lista as $v): ?>
<tr>
<td><b><?= htmlspecialchars($v['numero_ticket']) ?></b></td>
<td><?= htmlspecialchars($v['placa']) ?></td>
<td style="max-width:130px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis" title="<?= htmlspecialchars($v['servicios_txt']) ?>"><?= htmlspecialchars($v['tipo_vehiculo']) ?></td>
<td class="text-end">$<?= number_format($v['valor_total'],0,',','.') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot class="fw-bold"><tr><td colspan="3">Total Pista <?= $p ?></td><td class="text-end">$<?= number_format($total,0,',','.') ?></td></tr></tfoot>
</table>
</div>
</div>
<?php else: ?>
<div class="text-center text-muted small py-2">Sin ventas en esta pista</div>
<?php endif; ?>

</div>
<?php endforeach; ?>

<?php if($porPista['SIN PISTA']['cant']>0): 
  $data = $porPista['SIN PISTA'];
?>
<div class="card card-rio p-3 mb-3 border-warning" data-pista="SIN PISTA">
<div class="d-flex justify-content-between align-items-center mb-2">
<div>
<span class="badge bg-warning text-dark fs-6">SIN PISTA</span>
<span class="ms-2 small text-muted"><?= $data['cant'] ?> carros</span>
</div>
<div class="fw-bold fs-5" style="color:#1e40af">$<?= number_format($data['total'],0,',','.') ?></div>
</div>
<div class="table-responsive">
<table class="table table-sm table-striped small mb-0">
<thead class="table-dark"><tr><th># Ticket</th><th>Placa</th><th class="text-end">Valor</th></tr></thead>
<tbody>
<?php foreach($data['ventas'] as $v): ?>
<tr><td><?= htmlspecialchars($v['numero_ticket']) ?></td><td><?= htmlspecialchars($v['placa']) ?></td><td class="text-end">$<?= number_format($v['valor_total'],0,',','.') ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
<?php endif; ?>

</div>

<div class="card card-rio p-3 mt-3 bg-dark text-white">
<div class="d-flex justify-content-between align-items-center">
<div>
<h6 class="mb-0">TOTAL A PAGAR LAVADORES</h6>
<small class="text-white-50">Suma de todas las pistas</small>
</div>
<h3 class="mb-0 fw-bold" id="totalGeneral">$0</h3>
</div>
<div class="mt-3 d-grid gap-2">
<button class="btn btn-success" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir liquidación</button>
<a href="ventas_hoy.php?fecha=<?= $fecha ?>" class="btn btn-outline-light">Volver a ventas</a>
</div>
</div>

</div>

<script>
function formatoCOP(n){ return '$' + n.toLocaleString('es-CO'); }
function recalcular(){
  let totalGeneral = 0;
  document.querySelectorAll('.card[data-pista]').forEach(card=>{
    let inp = card.querySelector('.porc-input');
    if(!inp) return;
    let total = parseInt(inp.dataset.total) || 0;
    let porc = parseFloat(inp.value) || 0;
    let liquidar = Math.round(total * porc / 100);
    let el = card.querySelector('.total-liquidar');
    if(el) el.innerText = formatoCOP(liquidar);
    totalGeneral += liquidar;
  });
  document.getElementById('totalGeneral').innerText = formatoCOP(totalGeneral);
}
document.querySelectorAll('.porc-input').forEach(inp=>{ inp.addEventListener('input', recalcular); });
document.getElementById('btnAplicarGlobal').addEventListener('click', ()=>{
  let pg = document.getElementById('porcGlobal').value;
  if(pg==='') return;
  document.querySelectorAll('.porc-input').forEach(i=> i.value = pg);
  recalcular();
});
recalcular();
</script>

<?php require 'includes/footer.php'; ?>
