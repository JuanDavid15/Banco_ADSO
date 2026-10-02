<?php

declare(strict_types=1);

namespace App\Excepciones;

class SaldoInsuficienteException extends ReglaDeNegocioException
{
    public function __construct(string $message = 'Saldo insuficiente: el valor supera su saldo disponible.')
    {
        parent::__construct($message);
    }
}
