</div>
<div class="bottom-nav">
<a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>"><i class="bi bi-plus-circle d-block fs-5"></i>Nueva</a>
<a href="ventas_hoy.php" class="<?= basename($_SERVER['PHP_SELF'])=='ventas_hoy.php'?'active':'' ?>"><i class="bi bi-list-ul d-block fs-5"></i>Hoy</a>
<a href="resumen.php" class="<?= basename($_SERVER['PHP_SELF'])=='resumen.php'?'active':'' ?>"><i class="bi bi-bar-chart d-block fs-5"></i>Resumen</a>
<a href="liquidar_pista.php" class="<?= basename($_SERVER['PHP_SELF'])=='liquidar_pista.php'?'active':'' ?>"><i class="bi bi-cash-coin d-block fs-5"></i>Liquidar</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>