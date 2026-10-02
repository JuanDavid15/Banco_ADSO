<?php

declare(strict_types=1);

namespace App\Excepciones;

class CuentaNoEncontradaException extends ReglaDeNegocioException
{
    public function __construct(string $message = 'La cuenta no existe.')
    {
        parent::__construct($message);
    }
}
