<h2>Transferir dinero</h2>
<form method="post" action="index.php?ruta=transferencias/transferir">
  <label>Número de cuenta destino
    <input type="text" name="cuenta_destino" required autofocus>
  </label>

  <label>Valor a transferir
    <input type="text" name="valor" inputmode="decimal" required>
  </label>

  <label>Contraseña (confirmación)
    <input type="password" name="clave" required>
  </label>

  <p><button type="submit">Transferir</button></p>
</form>
