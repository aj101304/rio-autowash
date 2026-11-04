<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Rio Autowash</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons local fallback con SVG, no dependemos de fuentes externas -->
<style>
body{background:#f1f5f9; padding-bottom:85px; font-family:system-ui}
.navbar{background:#0f172a !important}
.card-rio{border-radius:16px; border:none; box-shadow:0 4px 12px rgba(0,0,0,.06)}
.btn-rio{background:#1e40af; color:white; border-radius:12px; padding:14px; font-weight:800}
.chip{padding:9px 14px; border-radius:999px; border:2px solid #e2e8f0; background:white; font-weight:700; font-size:.8rem; cursor:pointer}
.chip.active{background:#1e40af; color:white; border-color:#1e40af}
.serv-card{border:2px solid #e2e8f0; border-radius:12px; padding:10px 12px; background:white; display:flex; justify-content:space-between; align-items:center; cursor:pointer}
.serv-card.checked{border-color:#1e40af; background:#eff6ff}
.sugest{position:absolute; top:100%; left:0; right:0; background:white; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.15); z-index:50; max-height:220px; overflow:auto}
.bottom-nav{position:fixed; bottom:0; left:0; right:0; background:white; border-top:1px solid #e2e8f0; display:flex; z-index:1000; max-width:480px; margin:0 auto}
.bottom-nav a{flex:1; text-align:center; padding:8px 0; color:#64748b; text-decoration:none; font-size:.72rem; display:flex; flex-direction:column; align-items:center; gap:2px}
.bottom-nav a.active{color:#1e40af; font-weight:800}
.icon-svg{width:22px; height:22px; fill:currentColor}
</style></head><body>
<nav class="navbar navbar-dark"><div class="container-fluid" style="max-width:480px"><span class="navbar-brand fw-bold">💧 RIO AUTOWASH</span><span class="text-white-50 small"><?= $_SESSION['nombre'] ?? '' ?> | <a href="logout.php" class="text-white text-decoration-none">Salir</a></span></div></nav>
<div class="container" style="max-width:480px">

