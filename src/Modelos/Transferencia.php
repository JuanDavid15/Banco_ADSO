<?php

declare(strict_types=1);

namespace App\Modelos;

class Transferencia
{
    // numeroCuentaDestino no es una columna de la tabla: solo se llena cuando
    // el historial trae el número de la cuenta destino con un JOIN.
    public function __construct(
        private readonly int $id,
        private readonly int $cuentaOrigenId,
        private readonly int $cuentaDestinoId,
        private readonly string $valor,
        private readonly string $fecha,
        private readonly ?string $numeroCuentaDestino = null
    ) {
    }

    public function getValor(): string
    {
        return $this->valor;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function getNumeroCuentaDestino(): ?string
    {
        return $this->numeroCuentaDestino;
    }
}
