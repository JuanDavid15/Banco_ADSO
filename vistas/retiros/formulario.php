<h2>Retirar dinero</h2>
<form method="post" action="index.php?ruta=retiros/retirar">
  <label>Valor a retirar
    <input type="text" name="valor" inputmode="decimal" required autofocus>
  </label>

  <label>Contraseña (confirmación)
    <input type="password" name="clave" required>
  </label>

  <p><button type="submit">Retirar</button></p>
</form>
