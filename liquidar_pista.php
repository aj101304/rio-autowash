<?php
require 'includes/auth.php';
require 'config/database.php';

$fecha = $_GET['fecha'] ?? date('Y-m-d');

// Totales por pista
$stmt = $pdo->prepare("SELECT pista, COUNT(*) as cantidad, SUM(valor_total) as total FROM ventas WHERE fecha=? GROUP BY pista ORDER BY pista");
$stmt->execute([$fecha]);
$porPista = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mapear por pista 1-6 + vacías
$map = [];
foreach($porPista as $r){
  $p = $r['pista'] ?: 'SIN PISTA';
  if($p==='') $p='SIN PISTA';
  $map[$p] = $r;
}

// Asegurar pistas 1-6 existan aunque sea en 0
$pistas = ['1','2','3','4','5','6','SIN PISTA'];
foreach($pistas as $p){
  if(!isset($map[$p])) $map[$p] = ['pista'=>$p, 'cantidad'=>0, 'total'=>0];
}

// Total del día
$totalDiaStmt = $pdo->prepare("SELECT COUNT(*) as cant, SUM(valor_total) as total FROM ventas WHERE fecha=?");
$totalDiaStmt->execute([$fecha]);
$totalDia = $totalDiaStmt->fetch();

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
<div class="row text-center">
<div class="col-4"><h4 class="fw-bold mb-0" style="color:#1e40af">$<?= number_format($totalDia['total'] ?? 0,0,',','.') ?></h4><small class="text-muted">Total día</small></div>
<div class="col-4"><h4 class="fw-bold mb-0"><?= $totalDia['cant'] ?? 0 ?></h4><small class="text-muted">Vehículos</small></div>
<div class="col-4">
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
  $data = $map[$p];
  $cant = $data['cantidad'] ?? 0;
  $total = $data['total'] ?? 0;
  if($p=='SIN PISTA' && $cant==0) continue; // no mostrar si no hay sin pista
?>
<div class="card card-rio p-3 mb-2" data-pista="<?= $p ?>">
<div class="d-flex justify-content-between align-items-center mb-2">
<div>
<span class="badge bg-dark fs-6">PISTA <?= htmlspecialchars($p) ?></span>
<span class="ms-2 small text-muted"><?= $cant ?> carros</span>
</div>
<div class="fw-bold fs-5" style="color:#1e40af">$<?= number_format($total,0,',','.') ?></div>
</div>

<div class="row g-2 align-items-end">
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
<div class="mt-2">
<div class="d-flex justify-content-between small text-muted">
<span>Detalle: <?= $cant ?> servicios</span>
<span class="total-pista" data-total="<?= $total ?>"><?= number_format($total,0,',','.') ?></span>
</div>
</div>
</div>
<?php endforeach; ?>
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
function formatoCOP(n){
  return '$' + n.toLocaleString('es-CO');
}

function recalcular(){
  let totalGeneral = 0;
  document.querySelectorAll('.card[data-pista]').forEach(card=>{
    let total = parseInt(card.querySelector('.porc-input').dataset.total) || 0;
    let porc = parseFloat(card.querySelector('.porc-input').value) || 0;
    let liquidar = Math.round(total * porc / 100);
    card.querySelector('.total-liquidar').innerText = formatoCOP(liquidar);
    totalGeneral += liquidar;
  });
  document.getElementById('totalGeneral').innerText = formatoCOP(totalGeneral);
}

document.querySelectorAll('.porc-input').forEach(inp=>{
  inp.addEventListener('input', recalcular);
});

document.getElementById('btnAplicarGlobal').addEventListener('click', ()=>{
  let pg = document.getElementById('porcGlobal').value;
  if(pg==='') return;
  document.querySelectorAll('.porc-input').forEach(i=> i.value = pg);
  recalcular();
});

// Calculo inicial
recalcular();
</script>

<?php require 'includes/footer.php'; ?>
