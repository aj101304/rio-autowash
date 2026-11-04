<?php
$flag = __DIR__.'/paola_bloqueada.txt';
$a = $_GET['a'] ?? '';

if($a === 'bloquear'){
  file_put_contents($flag, date('Y-m-d H:i:s'));
  $msg = "⛔ Paola BLOQUEADA - se le cerrará la sesión cuando toque algo";
  $color = "#ef4444";
} elseif($a === 'desbloquear'){
  if(file_exists($flag)) unlink($flag);
  $msg = "✅ Paola DESBLOQUEADA - ya puede entrar";
  $color = "#22c55e";
} else {
  $bloqueada = file_exists($flag);
  $estado = $bloqueada ? "🔴 BLOQUEADA" : "🟢 DESBLOQUEADA";
  $msg = $estado;
  $color = $bloqueada ? "#ef4444" : "#1e40af";
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Control Paola</title></head>
<body style="font-family:system-ui; padding:20px; max-width:400px; margin:0 auto; background:#f1f5f9">
<h2 style="background:<?= $color ?>; color:white; padding:20px; border-radius:15px; text-align:center"><?= $msg ?></h2>
<?php if($a===''): ?>
<p style="text-align:center">Estado actual: <?= file_exists($flag) ? "BLOQUEADA" : "Activa" ?></p>
<?php endif; ?>
<a href="?a=bloquear" style="display:block; padding:20px; background:#ef4444; color:white; text-align:center; border-radius:12px; margin:15px 0; text-decoration:none; font-weight:bold; font-size:18px">BLOQUEAR Y CERRAR SESION</a>
<a href="?a=desbloquear" style="display:block; padding:20px; background:#22c55e; color:white; text-align:center; border-radius:12px; margin:15px 0; text-decoration:none; font-weight:bold; font-size:18px">DESBLOQUEAR</a>
<a href="index.php" style="display:block; text-align:center; margin-top:20px; color:#64748b">Volver al sistema</a>
</body></html>
