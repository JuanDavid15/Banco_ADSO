<?php

declare(strict_types=1);

namespace App\Nucleo;

use RuntimeException;
use Throwable;

class Vista
{
    private const CARPETA = __DIR__ . '/../../vistas';

    public function render(string $vista, array $datos = []): void
    {
        $rutaContenido = self::CARPETA . '/' . $vista . '.php';

        if (!is_file($rutaContenido)) {
            throw new RuntimeException("No existe la vista: {$vista}");
        }

        // Cada clave del arreglo pasa a ser una variable dentro de la vista.
        extract($datos);

        ob_start();

        try {
            require $rutaContenido;
            $contenido = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        require self::CARPETA . '/layout.php';
    }
}
