<?php

declare(strict_types=1);

namespace App\Modelos;

class Usuario
{
    public function __construct(
        private readonly int $id,
        private readonly int $cuentaId,
        private readonly string $usuario,
        private readonly string $claveHash
    ) {
    }

    public function getClaveHash(): string
    {
        return $this->claveHash;
    }
}
