<?php
require 'includes/auth.php';
if(isset($_SESSION['rol']) && $_SESSION['rol']=='yanet'){ die('⛔ Acceso denegado: Yanet solo puede consultar y liquidar pistas'); }
require 'config/database.php';
$id=$_GET['id']; $fecha=$_GET['fecha']??date('Y-m-d');
$pdo->prepare("DELETE FROM ventas WHERE id=?")->execute([$id]);
header("Location: ventas_hoy.php?fecha=$fecha");
?>