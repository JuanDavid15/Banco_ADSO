<?php

declare(strict_types=1);

namespace App\Modelos;

class Cuenta
{
    // El saldo es string porque DECIMAL(12,2) llega de MySQL como texto exacto;
    // un float perdería precisión en dinero.
    public function __construct(
        private readonly int $id,
        private readonly string $numeroCuenta,
        private readonly int $clienteId,
        private readonly string $saldo
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumeroCuenta(): string
    {
        return $this->numeroCuenta;
    }

    public function getClienteId(): int
    {
        return $this->clienteId;
    }

    public function getSaldo(): string
    {
        return $this->saldo;
    }
}
