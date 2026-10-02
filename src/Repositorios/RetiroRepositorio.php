<?php

declare(strict_types=1);

namespace App\Repositorios;

use App\Modelos\Retiro;
use App\Nucleo\Conexion;
use PDO;

class RetiroRepositorio
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::obtener();
    }

    public function crear(int $cuentaId, string $valor): int
    {
        $sql = "INSERT INTO retiros
                    (cuenta_id, valor)
                VALUES
                    (:cuenta_id, :valor)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':cuenta_id' => $cuentaId,
            ':valor' => $valor
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    /**
     * @return Retiro[]
     */
    public function obtenerPorCuenta(int $cuentaId): array
    {
        $sql = "SELECT id, cuenta_id, valor, fecha
                FROM retiros
                WHERE cuenta_id = :cuenta_id
                ORDER BY fecha DESC, id DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':cuenta_id' => $cuentaId
        ]);

        return array_map(
            fn (array $fila): Retiro => $this->hidratar($fila),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    // Cantidad y suma calculadas sobre lo que realmente hay en la tabla
    // (no hay un contador aparte que pueda quedar desactualizado).
    /**
     * @return array{cantidad: int, total: string}
     */
    public function resumenPorCuenta(int $cuentaId): array
    {
        $sql = "SELECT COUNT(*) AS cantidad,
                       COALESCE(SUM(valor), 0) AS total
                FROM retiros
                WHERE cuenta_id = :cuenta_id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':cuenta_id' => $cuentaId
        ]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'cantidad' => (int) $fila['cantidad'],
            'total' => (string) $fila['total']
        ];
    }

    private function hidratar(array $fila): Retiro
    {
        return new Retiro(
            (int) $fila['id'],
            (int) $fila['cuenta_id'],
            (string) $fila['valor'],
            (string) $fila['fecha']
        );
    }
}
