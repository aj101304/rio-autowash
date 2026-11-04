<?php
require 'config/database.php';
session_start();

// 1. Bloquea a paola
file_put_contents(__DIR__.'/paola_bloqueada.txt','1');
$pdo->prepare("UPDATE usuarios SET clave=? WHERE usuario='paola'")->execute([password_hash('BLOQUEADO_'.time(), PASSWORD_DEFAULT)]);

// 2. Borra TODAS las sesiones del servidor (la de paola y la tuya también, te toca volver a entrar)
$path = session_save_path();
if(!$path) $path = sys_get_temp_dir();
if(is_dir($path)){
  foreach(glob($path."/sess_*") as $file){
    // Lee el contenido para borrar solo la de paola si quieres ser más fino
    $content = @file_get_contents($file);
    if($content && (strpos($content,'paola')!==false || strpos($content,'"usuario";s:5:"paola"')!==false)){
        @unlink($file);
    }
  }
}

echo "<h1>✅ Sesión de Paola cerrada</h1><p>Ya está bloqueada y su sesión tumbada. Cuando intente entrar le saldrá: <b>saluda al amor de tu vida</b></p><a href='control_paola.php' style='display:block;padding:15px;background:#1e40af;color:white;text-align:center;border-radius:10px;text-decoration:none'>Volver al control</a>";
