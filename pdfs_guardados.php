<?php
require 'includes/auth.php';
require 'includes/header.php';
$dir = __DIR__ . '/pdfs';
$archivos = [];
if (is_dir($dir)) {
    $archivos = array_diff(scandir($dir, SCANDIR_SORT_DESCENDING), ['.', '..']);
}
?>
<div class="mt-3">
<h5 class="fw-bold">📁 PDFs Guardados</h5>
<p class="small text-muted">Aquí se guardan automáticamente cada vez que generas un PDF. Puedes abrir el de cualquier día.</p>

<?php if(empty($archivos)): ?>
<div class="card card-rio p-4 text-center mt-3">
<div style="font-size:40px">📄</div>
<p>Aún no has guardado ningún PDF.<br>Ve a <b>Ventas Hoy > Generar PDF</b> y se guardará automático.</p>
</div>
<?php else: ?>
<div class="d-flex flex-column gap-2 mt-3">
<?php foreach($archivos as $file):
    $ruta = 'pdfs/' . $file;
    $fechaFile = substr($file,0,10);
    $size = is_file($dir.'/'.$file) ? round(filesize($dir.'/'.$file)/1024,1) . ' KB' : '';
    $esHoy = $fechaFile === date('Y-m-d');
?>
<div class="card card-rio p-3 d-flex flex-row justify-content-between align-items-center">
<div>
<div class="fw-bold">📄 <?= $file ?> <?= $esHoy ? '<span class="badge bg-success ms-1">Hoy</span>' : '' ?></div>
<div class="small text-muted"><?= $size ?> - <?= date('d/m/Y H:i', filemtime($dir.'/'.$file)) ?></div>
</div>
<div class="d-flex gap-2">
<a href="<?= $ruta ?>" target="_blank" class="btn btn-sm btn-primary">Abrir</a>
<a href="<?= $ruta ?>" download class="btn btn-sm btn-outline-primary">⬇️</a>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<div class="mt-4">
<h6 class="fw-bold">Generar PDF de cualquier día anterior</h6>
<form action="generar_pdf.php" method="GET" class="card card-rio p-3 d-flex gap-2 flex-row">
<input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
<button class="btn btn-rio">Generar</button>
</form>
</div>

</div>
<?php require 'includes/footer.php'; ?>
