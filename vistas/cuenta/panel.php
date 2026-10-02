<h2>Bienvenido, <?= e($nombreCliente) ?></h2>

<div class="tarjeta">
  <p>Cuenta: <strong><?= e($numeroCuenta) ?></strong></p>
  <p>Saldo disponible</p>
  <p class="saldo"><?= e(formatoMoneda($saldo)) ?></p>
</div>

<p>
  <a class="boton" href="index.php?ruta=retiros/formulario">Retirar dinero</a>
  <a class="boton" href="index.php?ruta=transferencias/formulario">Transferir dinero</a>
</p>
