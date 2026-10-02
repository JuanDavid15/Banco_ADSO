<?php

declare(strict_types=1);

// Front Controller: TODA petición entra por este archivo.
require __DIR__ . '/../vendor/autoload.php';

use App\Nucleo\Router;

// La cookie de sesión no es legible desde JavaScript y no viaja en peticiones
// cruzadas desde otros sitios.
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$ruta = $_GET['ruta'] ?? 'cuenta/panel';

(new Router())->despachar(is_string($ruta) ? $ruta : 'cuenta/panel');
