<h2>Historial de retiros</h2>

<p>Total de retiros: <strong><?= e((string) $cantidad) ?></strong></p>
<p>Suma total retirada: <strong><?= e(formatoMoneda($total)) ?></strong></p>

<?php if ($retiros === []): ?>
  <p>Aún no ha realizado retiros.</p>
<?php else: ?>
<table>
  <tr><th>Fecha</th><th>Valor</th></tr>
  <?php foreach ($retiros as $retiro): ?>
  <tr>
    <td><?= e($retiro->getFecha()) ?></td>
    <td><?= e(formatoMoneda($retiro->getValor())) ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
