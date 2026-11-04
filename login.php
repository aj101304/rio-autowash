<?php
require 'config/database.php';
if(isset($_SESSION['usuario_id'])){ header('Location: index.php'); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $u=trim($_POST['usuario']); $p=$_POST['clave'];

 // AVISO SI ESTA BLOQUEADA
 if(strtolower($u)=='paola' && file_exists(__DIR__.'/paola_bloqueada.txt')){
   $error = "saluda al amor de tu vida 😘";
 } else {
   $stmt=$pdo->prepare("SELECT * FROM usuarios WHERE usuario=?");
   $stmt->execute([$u]); $user=$stmt->fetch();
   if($user && password_verify($p, $user['clave'])){
    $_SESSION['usuario_id']=$user['id']; $_SESSION['rol']=$user['rol']; $_SESSION['nombre']=$user['nombre'];
    header('Location: index.php'); exit;
   } else $error='Usuario o clave incorrecta';
 }
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - Rio</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#0f172a; display:flex; align-items:center; justify-content:center; min-height:100vh} .card{border-radius:20px; max-width:360px; width:100%}</style></head><body>
<div class="card p-4 shadow"><h4 class="fw-bold text-center mb-1" style="color:#1e40af">RIO AUTOWASH</h4><p class="text-center text-muted small">Control de ventas</p>
<?php if($error): ?><div class="alert alert-danger py-2 text-center fw-bold" style="font-size:18px"><?= $error ?></div><?php endif; ?>
<form method="post"><input name="usuario" class="form-control mb-2 form-control-lg" placeholder="Usuario" required><input name="clave" type="password" class="form-control mb-3 form-control-lg" placeholder="Clave" required><button class="btn btn-primary w-100 btn-lg" style="background:#1e40af">Entrar</button></form>
<p class="small text-muted mt-3 text-center">admin  <br>paola </p></div></body></html>
