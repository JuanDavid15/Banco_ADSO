<?php

declare(strict_types=1);

namespace App\Excepciones;

class ValorInvalidoException extends ReglaDeNegocioException
{
    public function __construct(
        string $message = 'El valor debe ser numérico y mayor que 0 (ejemplo: 150000 o 150000.50).'
    ) {
        parent::__construct($message);
    }
}
