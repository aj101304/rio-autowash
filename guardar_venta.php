<?php
require 'includes/auth.php';
if(isset($_SESSION['rol']) && $_SESSION['rol']=='yanet'){ die('⛔ Acceso denegado: Yanet solo puede consultar y liquidar pistas'); }
require 'config/database.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
 $numero=$_POST['numero_ticket']; $fecha=$_POST['fecha']; $pista=$_POST['pista']??null;
 $placa=strtoupper(trim($_POST['placa'])); $tipo_veh=$_POST['tipo_vehiculo'];
 $valor_total=(int)$_POST['valor_total']; $obs=$_POST['observaciones']??null;
 $hora=date('H:i:s'); $servicios=$_POST['servicios']??[];
 // VERIFICAR SI PLACA YA EXISTE HOY
 $chk = $pdo->prepare("SELECT numero_ticket, hora_llegada FROM ventas WHERE placa=? AND fecha=? LIMIT 1");
 $chk->execute([$placa, $fecha]);
 $existe = $chk->fetch();
 if($existe){
   // No bloqueamos, pero lanzamos alerta con info del ticket existente
   $msg = "⚠️ Esta placa $placa ya fue tiqueteada hoy. Ticket existente: #".$existe['numero_ticket']." a las ".substr($existe['hora_llegada'],0,5);
   // Si viene con confirmación ?forzar=1 dejamos pasar, si no, mostramos error
   if(!isset($_POST['forzar_duplicado'])){
     // Sin rollBack aqui porque aun no hay transaccion - backend safety
     die("<!DOCTYPE html><html><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1'><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'></head><body style='background:#0f172a; display:flex; align-items:center; justify-content:center; min-height:100vh'><div class='card p-4 shadow' style='max-width:420px'><h4 class='text-danger fw-bold'>⛔ Placa duplicada</h4><p class='mt-3'><b>Placa:</b> $placa<br><b>Ticket existente:</b> #{$existe['numero_ticket']}<br><b>Hora:</b> ".substr($existe['hora_llegada'],0,5)."<br><b>Fecha:</b> $fecha</p><div class='alert alert-warning'>Esta placa ya fue tiqueteada hoy.</div><form method='POST'><input type='hidden' name='numero_ticket' value='".htmlspecialchars($numero)."'><input type='hidden' name='fecha' value='".htmlspecialchars($fecha)."'><input type='hidden' name='pista' value='".htmlspecialchars($pista)."'><input type='hidden' name='placa' value='".htmlspecialchars($placa)."'><input type='hidden' name='tipo_vehiculo' value='".htmlspecialchars($tipo_veh)."'><input type='hidden' name='valor_total' value='".htmlspecialchars($valor_total)."'><input type='hidden' name='observaciones' value='".htmlspecialchars($obs)."'><input type='hidden' name='forzar_duplicado' value='1'>".(isset($_POST['servicios']) ? implode('', array_map(function($k,$s){ return "<input type='hidden' name='servicios[$k][nombre]' value='".htmlspecialchars($s['nombre'])."'><input type='hidden' name='servicios[$k][precio]' value='".htmlspecialchars($s['precio'])."'>"; }, array_keys($_POST['servicios']), $_POST['servicios'])) : "")."<button class='btn btn-warning w-100'>⚠️ Guardar de todos modos</button><a href='index.php' class='btn btn-light w-100 mt-2'>Cancelar / Volver</a></form></div></body></html>");
   }
 }

 $pdo->beginTransaction();
 try{
  $stmt=$pdo->prepare("INSERT INTO ventas (numero_ticket, fecha, pista, placa, tipo_vehiculo, valor_total, hora_llegada, observaciones, usuario_id) VALUES (?,?,?,?,?,?,?,?,?)");
  $stmt->execute([$numero,$fecha,$pista,$placa,$tipo_veh,$valor_total,$hora,$obs,$_SESSION['usuario_id']]);
  $venta_id=$pdo->lastInsertId();
  if(!empty($servicios)){
   $stmtD=$pdo->prepare("INSERT INTO venta_detalle (venta_id, servicio, precio) VALUES (?,?,?)");
   foreach($servicios as $s){ $stmtD->execute([$venta_id, $s['nombre'], $s['precio']]); }
  }
  $pdo->commit();
  header('Location: ventas_hoy.php?ok=1&fecha='.$fecha); exit;
 } catch(Exception $e){ if($pdo->inTransaction()) $pdo->rollBack(); die('Error: '.$e->getMessage()); }
}
?>