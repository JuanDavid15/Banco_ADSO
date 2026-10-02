<?php

declare(strict_types=1);

namespace App\Modelos;

class Cliente
{
    public function __construct(
        private readonly int $id,
        private readonly string $documento,
        private readonly string $nombre
    ) {
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }
}
