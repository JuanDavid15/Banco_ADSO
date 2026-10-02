<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo ?? 'Banco ADSO') ?> · Banco ADSO</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
  <h1>Banco ADSO</h1>
  <?php if (!empty($autenticado)): ?>
  <nav>
    <a href="index.php?ruta=cuenta/panel">Mi cuenta</a>
    <a href="index.php?ruta=retiros/formulario">Retirar</a>
    <a href="index.php?ruta=retiros/historial">Historial de retiros</a>
    <a href="index.php?ruta=transferencias/formulario">Transferir</a>
    <a href="index.php?ruta=transferencias/historial">Historial de transferencias</a>
    <a class="salir" href="index.php?ruta=sesion/salir">Cerrar sesión</a>
  </nav>
  <?php endif; ?>
</header>
<main>
  <?php if (!empty($mensaje)): ?>
  <p class="mensaje <?= e($mensaje['tipo']) ?>"><?= e($mensaje['texto']) ?></p>
  <?php endif; ?>
<?= $contenido ?>
</main>
<footer><small>Banco ADSO · aplicación bancaria</small></footer>
</body>
</html>
