<?php
require 'includes/auth.php';
require 'config/database.php';
require 'includes/header.php';
?>
<div class="mt-3">
<h5 class="fw-bold">Nueva Venta <span class="badge bg-primary ms-2"><?= date('d-m-Y') ?></span></h5>
<form id="formVenta" action="guardar_venta.php" method="POST" class="card card-rio p-3 mt-3">
<div class="row g-2">
<div class="col-6"><label class="small fw-bold">N° Ticket *</label><input name="numero_ticket" class="form-control form-control-lg" placeholder="" required></div>
<div class="col-6"><label class="small fw-bold">Fecha</label><input type="date" name="fecha" class="form-control form-control-lg" value="<?= date('Y-m-d') ?>" required></div>
<div class="col-4"><label class="small">Pista</label><select name="pista" class="form-select"><option value="">-</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option></select></div>
<div class="col-8">
<label class="small fw-bold">Placa * <span class="text-muted fw-normal">- buscador frecuente</span></label>
<div class="position-relative">
<input name="placa" id="placa" class="form-control form-control-lg text-uppercase" placeholder="ABC123" required autocomplete="off">
<div id="sugerenciasPlaca" class="sugest" style="display:none"></div>
</div>
<div id="alertaDuplicado" class="alert alert-danger mt-2 py-2 px-3 small fw-bold" style="display:none"></div>
</div>

<div class="col-12" id="clienteFrecuente" style="display:none">
<div class="alert alert-primary py-2 px-3 mb-0 small">
<div class="d-flex justify-content-between"><b id="cfPlaca"></b><span id="cfVisitas"></span></div>
<div id="cfUltimo" class="mt-1"></div>
<div class="mt-2 d-flex gap-2">
<button type="button" class="btn btn-sm btn-primary" id="btnCargarVeh">Cargar vehículo</button>
<button type="button" class="btn btn-sm btn-outline-primary" id="btnVerHist">Ver historial</button>
</div>
</div>
</div>

<div class="col-12 mt-3">
<label class="small fw-bold">1. Tipo Vehículo *</label>
<div class="d-flex flex-wrap gap-2 mt-2" id="chipsVehiculo">
<button type="button" class="chip" data-v="AUTOMOVIL">🚗 AUTOMÓVIL</button>
<button type="button" class="chip" data-v="CAMIONETA">🛻 CAMIONETA</button>
<button type="button" class="chip" data-v="MOTO">🏍️ MOTO</button>
<button type="button" class="chip" data-v="MOTO_ALTO">🏍️ MOTO ALTO CC</button>
<button type="button" class="chip" data-v="BICICLETA">🚲 BICICLETA</button>
<button type="button" class="chip" data-v="CAMION_PEQ">🚚 CAMIÓN PEQ</button>
<button type="button" class="chip" data-v="CAMION_GDE">🚛 CAMIÓN GDE</button>
<button type="button" class="chip" data-v="ALFOMBRA">🧶 ALFOMBRA</button>
<button type="button" class="chip" data-v="PARQUEADERO">🅿️ PARQUEADERO</button>
</div>
<input type="hidden" name="tipo_vehiculo" id="tipo_vehiculo" required>
</div>

<div class="col-12 mt-3" id="serviciosContainer" style="display:none">
<label class="small fw-bold">2. Servicios (marca varios, se suman) *</label>
<div id="listaServicios" class="d-flex flex-column gap-2 mt-2"></div>
<div class="card bg-light p-2 mt-3" id="resumenServ" style="display:none">
<div class="d-flex justify-content-between"><small class="fw-bold">Seleccionados:</small><small id="countServ">0</small></div>
<div id="listaResumen" class="small"></div>
<div class="d-flex justify-content-between mt-2 border-top pt-2"><b>Total:</b><b id="totalPreview" style="color:#1e40af; font-size:1.3rem">$0</b></div>
</div>
</div>

<div class="col-12 mt-3">
<label class="small fw-bold">Valor Total</label>
<div class="input-group input-group-lg"><span class="input-group-text">$</span><input name="valor_total" id="valor_total" type="number" class="form-control fw-bold" placeholder="0" required></div>
<small class="text-muted">Calculado automático, editable si hay descuento</small>
</div>
<div class="col-12"><label class="small">Observaciones</label><textarea name="observaciones" class="form-control" rows="2"></textarea></div>
<div class="col-12 mt-2"><button class="btn btn-rio w-100">💧 GUARDAR VENTA</button></div>
</div>
</form>
</div>

<div class="modal fade" id="modalHist" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h6 class="modal-title">Historial de placa</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="bodyHist"></div></div></div></div>

<script>
const preciosBase = {
 "Lavado General": {AUTOMOVIL:25000, CAMIONETA:30000},
 "Lavado Motor": {AUTOMOVIL:25000, CAMIONETA:30000},
 "Lavado Chasis": {AUTOMOVIL:25000, CAMIONETA:30000},
 "Adicional Camioneta": {CAMIONETA:6000},
 "Grafitado": {AUTOMOVIL:22000, CAMIONETA:25000},
 "Restauración Partes Negras": {AUTOMOVIL:25000, CAMIONETA:30000},
 "Encerado Manual": {AUTOMOVIL:68000, CAMIONETA:79000},
 "Polichado a Máquina": {AUTOMOVIL:150000, CAMIONETA:180000},
 "Lavado de Tapicería": {AUTOMOVIL:190000, CAMIONETA:230000},
 "Lavado de Cojinería": {AUTOMOVIL:110000, CAMIONETA:121000},
 "Lavado Alfombra Auto": {AUTOMOVIL:83000, CAMIONETA:88000},
 "Lavado Puertas y Tablero": {AUTOMOVIL:50000, CAMIONETA:56000},
 "Lavado de Techo": {AUTOMOVIL:58000, CAMIONETA:64000},
 "Cristalizado Rio 3 Pasos 3M": {AUTOMOVIL:260000, CAMIONETA:285000},
 "COMBO RIO 1": {AUTOMOVIL:85000, CAMIONETA:98000},
 "COMBO RIO 2": {AUTOMOVIL:145000, CAMIONETA:175000},
 "COMBO RIO 3": {AUTOMOVIL:300000, CAMIONETA:350000},
 "COMBO RIO 4": {AUTOMOVIL:375000, CAMIONETA:450000},
 "COMBO RIO 5": {AUTOMOVIL:330000, CAMIONETA:365000},
 "COMBO LAVADO FRR": {AUTOMOVIL:160000, CAMIONETA:160000},
 "Lavado y Pulida Motos": {MOTO:20000, MOTO_ALTO:20000},
 "Alto Cilindraje": {MOTO_ALTO:25000, MOTO:25000},
 "Alfombra Pequeña": {AUTOMOVIL:36000, CAMIONETA:36000, ALFOMBRA:36000, DEFAULT:36000},
 "Alfombra Mediana": {AUTOMOVIL:43000, CAMIONETA:43000, ALFOMBRA:43000, DEFAULT:43000},
 "Alfombra Grande": {AUTOMOVIL:46000, CAMIONETA:46000, ALFOMBRA:46000, DEFAULT:46000},
 "Bicicleta": {BICICLETA:10000, DEFAULT:10000},
 "Lavado Camión Pequeño": {CAMION_PEQ:100000, DEFAULT:100000},
 "Lavado Camión Grande": {CAMION_GDE:110000, DEFAULT:110000},
 "Lavado Camión Grande 2": {CAMION_GDE:130000, DEFAULT:130000},
 "Parqueadero": {PARQUEADERO:8000, DEFAULT:8000},
 "Parqueadero X Horas": {PARQUEADERO:4000, DEFAULT:4000}
};
const serviciosPorVehiculo = {
 AUTOMOVIL: ["Lavado General","Lavado Motor","Lavado Chasis","Grafitado","Restauración Partes Negras","Encerado Manual","Polichado a Máquina","Lavado de Tapicería","Lavado de Cojinería","Lavado Alfombra Auto","Lavado Puertas y Tablero","Lavado de Techo","Cristalizado Rio 3 Pasos 3M","COMBO RIO 1","COMBO RIO 2","COMBO RIO 3","COMBO RIO 4","COMBO RIO 5","COMBO LAVADO FRR","Alfombra Pequeña","Alfombra Mediana","Alfombra Grande"],
 CAMIONETA: ["Lavado General","Lavado Motor","Lavado Chasis","Adicional Camioneta","Grafitado","Restauración Partes Negras","Encerado Manual","Polichado a Máquina","Lavado de Tapicería","Lavado de Cojinería","Lavado Alfombra Auto","Lavado Puertas y Tablero","Lavado de Techo","Cristalizado Rio 3 Pasos 3M","COMBO RIO 1","COMBO RIO 2","COMBO RIO 3","COMBO RIO 4","COMBO RIO 5","COMBO LAVADO FRR","Alfombra Pequeña","Alfombra Mediana","Alfombra Grande"],
 MOTO: ["Lavado y Pulida Motos","Grafitado","Encerado Manual"],
 MOTO_ALTO: ["Lavado y Pulida Motos","Alto Cilindraje","Grafitado","Encerado Manual"],
 BICICLETA: ["Bicicleta"],
 CAMION_PEQ: ["Lavado Camión Pequeño"],
 CAMION_GDE: ["Lavado Camión Grande", "Lavado Camión Grande 2"],
    ALFOMBRA: ["Alfombra Pequeña","Alfombra Mediana","Alfombra Grande","Lavado Alfombra Auto"],
 PARQUEADERO: ["Parqueadero","Parqueadero X Horas"]
};

let vehSel = '';
let seleccionados = [];
let ultimoRegistro = null;

document.querySelectorAll('#chipsVehiculo .chip').forEach(c=>{
 c.addEventListener('click', ()=>{
  document.querySelectorAll('#chipsVehiculo .chip').forEach(x=>x.classList.remove('active'));
  c.classList.add('active');
  vehSel = c.dataset.v;
  document.getElementById('tipo_vehiculo').value = vehSel;
  renderServicios();
 });
});

function getPrecio(serv, veh){ let p=preciosBase[serv]; if(!p) return 0; return p[veh] || p.DEFAULT || p.AUTOMOVIL || p.CAMIONETA || 0; }

function renderServicios(){
 if(!vehSel) return;
 document.getElementById('serviciosContainer').style.display='block';
 const lista = serviciosPorVehiculo[vehSel] || [];
 const cont = document.getElementById('listaServicios');
 cont.innerHTML='';
 lista.forEach(serv=>{
  let precio = getPrecio(serv, vehSel);
  let isChecked = seleccionados.some(s=>s.serv===serv);
  let div = document.createElement('div');
  div.className='serv-card' + (isChecked?' checked':'');
  div.innerHTML=`<div class="d-flex align-items-center gap-2"><input type="checkbox" class="form-check-input m-0" style="width:20px;height:20px" data-serv="${serv}" data-precio="${precio}" ${isChecked?'checked':''}><span class="fw-semibold small">${serv}</span></div><b class="small" style="color:#1e40af">$${precio.toLocaleString('es-CO')}</b>`;
  div.addEventListener('click', (e)=>{ if(e.target.type!=='checkbox'){ let cb=div.querySelector('input'); cb.checked=!cb.checked; } toggleServ(div.querySelector('input')); });
  div.querySelector('input').addEventListener('change', function(){ toggleServ(this); });
  cont.appendChild(div);
 });
 updateResumen();
}
function toggleServ(cb){
 let serv=cb.dataset.serv; let precio=parseInt(cb.dataset.precio);
 let card=cb.closest('.serv-card');
 if(cb.checked){ card.classList.add('checked'); if(!seleccionados.some(s=>s.serv===serv)) seleccionados.push({serv, precio}); }
 else { card.classList.remove('checked'); seleccionados=seleccionados.filter(s=>s.serv!==serv); }
 updateResumen();
}
function updateResumen(){
 let total=seleccionados.reduce((a,b)=>a+b.precio,0);
 document.getElementById('valor_total').value=total;
 if(seleccionados.length>0){
  document.getElementById('resumenServ').style.display='block';
  document.getElementById('countServ').innerText=seleccionados.length+' servicios';
  document.getElementById('listaResumen').innerHTML=seleccionados.map(s=>`${s.serv} - $${s.precio.toLocaleString('es-CO')}`).join('<br>');
  document.getElementById('totalPreview').innerText='$'+total.toLocaleString('es-CO');
 } else { document.getElementById('resumenServ').style.display='none'; }
 document.querySelectorAll('.hidden-serv').forEach(e=>e.remove());
 let form=document.getElementById('formVenta');
 seleccionados.forEach((s,i)=>{
  let inp=document.createElement('input'); inp.type='hidden'; inp.name=`servicios[${i}][nombre]`; inp.value=s.serv; inp.className='hidden-serv'; form.appendChild(inp);
  let inp2=document.createElement('input'); inp2.type='hidden'; inp2.name=`servicios[${i}][precio]`; inp2.value=s.precio; inp2.className='hidden-serv'; form.appendChild(inp2);
 });
}

// BUSCADOR DE PLACA
let timeoutBusqueda;
const inputPlaca = document.getElementById('placa');
const sugerencias = document.getElementById('sugerenciasPlaca');
const clienteDiv = document.getElementById('clienteFrecuente');

inputPlaca.addEventListener('input', e=>{
 e.target.value=e.target.value.toUpperCase();
 let q=e.target.value.trim();
 if(q.length<2){ sugerencias.style.display='none'; clienteDiv.style.display='none'; return; }
 clearTimeout(timeoutBusqueda);
 timeoutBusqueda=setTimeout(()=> buscarPlaca(q), 400);
});

function buscarPlaca(q){
 fetch(`api/buscar_placa.php?q=${encodeURIComponent(q)}`)
 .then(r=>r.json()).then(data=>{
  if(data.resultados && data.resultados.length>0){
   ultimoRegistro = data.resultados[0];
   sugerencias.innerHTML = data.resultados.map(r=>`<div class="p-2 border-bottom" style="cursor:pointer" onclick="seleccionarSugerencia('${r.placa}')"><b>${r.placa}</b> - ${r.tipo_vehiculo} - ${r.fecha} - $${parseInt(r.valor_total).toLocaleString('es-CO')}</div>`).join('');
   sugerencias.style.display='block';
   clienteDiv.style.display='block';
   document.getElementById('cfPlaca').innerText = ultimoRegistro.placa;
   document.getElementById('cfVisitas').innerText = data.historial.visitas ? `${data.historial.visitas} visitas` : '';
   document.getElementById('cfUltimo').innerHTML = `Último: <b>${ultimoRegistro.tipo_vehiculo}</b> - ${ultimoRegistro.servicios || ''} - $${parseInt(ultimoRegistro.valor_total).toLocaleString('es-CO')} el ${ultimoRegistro.fecha}`;
  } else {
   sugerencias.style.display='none';
   clienteDiv.style.display='none';
  }
 });
}

function seleccionarSugerencia(placa){
 document.getElementById('placa').value=placa;
 sugerencias.style.display='none';
 buscarPlaca(placa);
}

document.getElementById('btnCargarVeh').addEventListener('click', ()=>{
 if(!ultimoRegistro) return;
 let veh = ultimoRegistro.tipo_vehiculo;
 document.getElementById('tipo_vehiculo').value=veh;
 document.querySelectorAll('#chipsVehiculo .chip').forEach(c=>{ c.classList.toggle('active', c.dataset.v===veh); });
 vehSel=veh;
 renderServicios();
});

document.getElementById('btnVerHist').addEventListener('click', ()=>{
 let q = document.getElementById('placa').value;
 fetch(`api/buscar_placa.php?q=${encodeURIComponent(q)}`).then(r=>r.json()).then(data=>{
  let html = `<p><b>Placa ${q}</b> - ${data.historial.visitas} visitas - Total: $${parseInt(data.historial.total_gastado||0).toLocaleString('es-CO')}</p>`;
  html += data.resultados.map(r=>`<div class="border-bottom py-2 small"><b>${r.fecha} #${r.numero_ticket}</b> - ${r.tipo_vehiculo}<br>${r.servicios || ''}<br><b>$${parseInt(r.valor_total).toLocaleString('es-CO')}</b></div>`).join('');
  document.getElementById('bodyHist').innerHTML=html;
  new bootstrap.Modal(document.getElementById('modalHist')).show();
 });
});

document.addEventListener('click', (e)=>{ if(!e.target.closest('#placa') && !e.target.closest('#sugerenciasPlaca')) sugerencias.style.display='none'; });

// ALERTA PLACA DUPLICADA MISMO DIA
let timeoutDuplicado;
function verificarPlacaDuplicada(placa){
  let fecha = document.querySelector('input[name=fecha]').value || new Date().toISOString().split('T')[0];
  if(placa.length<3) { document.getElementById('alertaDuplicado').style.display='none'; return; }
  fetch(`api/check_placa.php?placa=${encodeURIComponent(placa)}&fecha=${fecha}`)
  .then(r=>r.json()).then(data=>{
    let alerta = document.getElementById('alertaDuplicado');
    if(data.existe && data.tickets && data.tickets.length>0){
      let t = data.tickets[0];
      alerta.innerHTML = `⚠️ Esta placa <b>${placa}</b> ya fue tiqueteada hoy<br>Ticket: <b>#${t.numero_ticket}</b> a las ${t.hora_llegada.substring(0,5)} - Pista ${t.pista || '-'}<br><small>Verifica antes de guardar</small>`;
      alerta.style.display='block';
      // vibrar o sonido si quieres
    } else {
      alerta.style.display='none';
    }
  });
}

const placaInput = document.getElementById('placa');
if(placaInput){
  placaInput.addEventListener('blur', function(){
    verificarPlacaDuplicada(this.value.trim().toUpperCase());
  });
  placaInput.addEventListener('input', function(){
    clearTimeout(timeoutDuplicado);
    timeoutDuplicado = setTimeout(()=> verificarPlacaDuplicada(this.value.trim().toUpperCase()), 800);
  });
}

</script>
<?php require 'includes/footer.php'; ?>
