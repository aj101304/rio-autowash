<?php
require 'config/database.php';
$usuario='yanet';
$clave='yanet1';
$hash=password_hash($clave, PASSWORD_DEFAULT);

// Verifica si ya existe
$stmt=$pdo->prepare("SELECT id FROM usuarios WHERE usuario=?");
$stmt->execute([$usuario]);
if($stmt->fetch()){
  // Actualiza clave y rol
  $pdo->prepare("UPDATE usuarios SET clave=?, rol='yanet', nombre='Yanet - Liquidación' WHERE usuario=?")->execute([$hash,$usuario]);
  echo "✅ Usuario yanet actualizado: clave yanet1, rol yanet";
} else {
  $pdo->prepare("INSERT INTO usuarios (usuario, clave, rol, nombre) VALUES (?,?,?,?)")->execute([$usuario,$hash,'yanet','Yanet - Liquidación']);
  echo "✅ Usuario yanet creado: yanet / yanet1 con rol yanet (solo consulta + liquidar)";
}
echo "<br><a href='login.php'>Ir al login</a>";
