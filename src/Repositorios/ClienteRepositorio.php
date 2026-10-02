<?php

declare(strict_types=1);

namespace App\Repositorios;

use App\Modelos\Cliente;
use App\Nucleo\Conexion;
use PDO;

class ClienteRepositorio
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::obtener();
    }

    public function obtenerPorId(int $id): ?Cliente
    {
        $sql = "SELECT id, documento, nombre
                FROM clientes
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ? $this->hidratar($fila) : null;
    }

    private function hidratar(array $fila): Cliente
    {
        return new Cliente(
            (int) $fila['id'],
            (string) $fila['documento'],
            (string) $fila['nombre']
        );
    }
}
