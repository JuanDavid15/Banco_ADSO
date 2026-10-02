<?php

declare(strict_types=1);

namespace App\Excepciones;

class CuentaDestinoInvalidaException extends ReglaDeNegocioException
{
    public function __construct(string $message = 'La cuenta destino debe ser diferente a su cuenta.')
    {
        parent::__construct($message);
    }
}
