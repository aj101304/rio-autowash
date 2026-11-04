<?php
require 'config/database.php';
$hash = password_hash('paolita8', PASSWORD_DEFAULT);
$pdo->prepare("UPDATE usuarios SET clave=? WHERE usuario='paola'")->execute([$hash]);
$flag = __DIR__.'/paola_bloqueada.txt';
if(file_exists($flag)) unlink($flag);
echo "<h1>✅ Clave de paola restablecida a paolita8 y desbloqueada</h1><p>Ya puede entrar</p><a href='login.php'>Ir al login</a>";
