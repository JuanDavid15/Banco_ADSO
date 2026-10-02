<?php

declare(strict_types=1);

namespace App\Nucleo;

use Throwable;

class Router
{
    // ?ruta=controlador/accion/parametros
    //   -> App\Controladores\{Controlador}Controlador::{accion}Accion(parametros)
    public function despachar(string $ruta): void
    {
        try {
            $segmentos = array_values(array_filter(
                explode('/', trim($ruta, '/')),
                fn (string $segmento): bool => $segmento !== ''
            ));

            $controlador = $segmentos[0] ?? 'cuenta';
            $accion = $segmentos[1] ?? 'panel';
            $parametros = array_slice($segmentos, 2);

            // Solo letras: lo que viene de la URL nunca forma otro tipo de nombre.
            if (!$this->esNombreValido($controlador) || !$this->esNombreValido($accion)) {
                $this->noEncontrado();
                return;
            }

            $clase = 'App\\Controladores\\' . ucfirst($controlador) . 'Controlador';
            $metodo = $accion . 'Accion';

            if (!class_exists($clase) || !method_exists($clase, $metodo)) {
                $this->noEncontrado();
                return;
            }

            $objetoControlador = new $clase();

            if (!is_callable([$objetoControlador, $metodo])) {
                $this->noEncontrado();
                return;
            }

            $objetoControlador->$metodo(...$parametros);
        } catch (Throwable $e) {
            $this->errorInterno($e);
        }
    }

    private function esNombreValido(string $segmento): bool
    {
        return preg_match('/^[A-Za-z]+$/', $segmento) === 1;
    }

    private function noEncontrado(): void
    {
        http_response_code(404);
        (new Vista())->render('errores/404', ['titulo' => 'Página no encontrada']);
    }

    // El usuario nunca ve una traza de PHP: el detalle va al log del servidor.
    private function errorInterno(Throwable $e): void
    {
        error_log(sprintf(
            '[Banco ADSO] %s: %s en %s:%d',
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));

        http_response_code(500);

        try {
            (new Vista())->render('errores/500', ['titulo' => 'Error del sistema']);
        } catch (Throwable) {
            echo 'Ocurrió un error en el sistema. Intente más tarde.';
        }
    }
}
