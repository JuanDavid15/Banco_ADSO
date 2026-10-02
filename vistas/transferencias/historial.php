<h2>Historial de transferencias enviadas</h2>

<p>Total de transferencias enviadas: <strong><?= e((string) $cantidad) ?></strong></p>
<p>Suma total transferida: <strong><?= e(formatoMoneda($total)) ?></strong></p>

<?php if ($transferencias === []): ?>
  <p>Aún no ha realizado transferencias.</p>
<?php else: ?>
<table>
  <tr><th>Fecha</th><th>Cuenta destino</th><th>Valor</th></tr>
  <?php foreach ($transferencias as $transferencia): ?>
  <tr>
    <td><?= e($transferencia->getFecha()) ?></td>
    <td><?= e((string) $transferencia->getNumeroCuentaDestino()) ?></td>
    <td><?= e(formatoMoneda($transferencia->getValor())) ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
