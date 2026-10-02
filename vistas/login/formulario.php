<h2>Iniciar sesión</h2>
<form method="post" action="index.php?ruta=sesion/autenticar">
  <label>Número de cuenta
    <input type="text" name="numero_cuenta" value="<?= e($numeroCuenta) ?>" required autofocus>
  </label>

  <label>Contraseña
    <input type="password" name="clave" required>
  </label>

  <p><button type="submit">Entrar</button></p>
</form>
