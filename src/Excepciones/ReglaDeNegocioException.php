<?php

declare(strict_types=1);

namespace App\Excepciones;

use Exception;

/**
 * Clase padre de todos los errores "esperados" del negocio.
 * El controlador atrapa esta clase y muestra el mensaje; cualquier otra
 * excepción (por ejemplo un fallo de PDO) se trata como error del sistema.
 */
class ReglaDeNegocioException extends Exception
{
}
