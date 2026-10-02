<?php

declare(strict_types=1);

namespace App\Excepciones;

class CredencialesInvalidasException extends ReglaDeNegocioException
{
    public function __construct(string $message = 'Número de cuenta o contraseña incorrectos.')
    {
        parent::__construct($message);
    }
}
