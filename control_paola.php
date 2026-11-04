<?php
require 'config/database.php';
$accion = $_GET['a'] ?? '';

if($accion === 'bloquear'){
  $pdo->prepare("UPDATE usuarios SET clave=? WHERE usuario='paola'")->execute([password_hash('BLOQUEADO_'.time(), PASSWORD_DEFAULT)]);
  echo "<h1>⛔ Paola BLOQUEADA</h1><p>Ahora no puede entrar.</p>";
} elseif($accion === 'desbloquear'){
  $hash = password_hash('paolita8', PASSWORD_DEFAULT);
  $pdo->prepare("UPDATE usuarios SET clave=? WHERE usuario='paola'")->execute([$hash]);
  echo "<h1>✅ Paola DESBLOQUEADA</h1><p>Ya puede entrar con paolita8</p>";
} else {
  echo "<h2>Control Paola</h2>
  <a href='?a=bloquear' style='display:block;padding:20px;background:#ef4444;color:white;text-align:center;border-radius:10px;margin:10px 0;text-decoration:none;font-weight:bold'>BLOQUEAR A PAOLA</a>
  <a href='?a=desbloquear' style='display:block;padding:20px;background:#22c55e;color:white;text-align:center;border-radius:10px;margin:10px 0;text-decoration:none;font-weight:bold'>DESBLOQUEAR A LAS 8AM</a>";
}
