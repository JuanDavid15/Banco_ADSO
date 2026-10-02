<?php

declare(strict_types=1);

namespace App\Repositorios;

use App\Modelos\Transferencia;
use App\Nucleo\Conexion;
use PDO;

class TransferenciaRepositorio
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::obtener();
    }

    public function crear(
        int $cuentaOrigenId,
        int $cuentaDestinoId,
        string $valor
    ): int {
        $sql = "INSERT INTO transferencias
                    (cuenta_origen_id, cuenta_destino_id, valor)
                VALUES
                    (:cuenta_origen_id, :cuenta_destino_id, :valor)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':cuenta_origen_id' => $cuentaOrigenId,
            ':cuenta_destino_id' => $cuentaDestinoId,
            ':valor' => $valor
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    // Solo las transferencias ENVIADAS por la cuenta (lo que pide RF7).
    /**
     * @return Transferencia[]
     */
    public function obtenerEnviadasPorCuenta(int $cuentaId): array
    {
        $sql = "SELECT
                    t.id,
                    t.cuenta_origen_id,
                    t.cuenta_destino_id,
                    t.valor,
                    t.fecha,
                    cd.numero_cuenta AS numero_cuenta_destino
                FROM transferencias AS t
                INNER JOIN cuentas AS cd
                    ON t.cuenta_destino_id = cd.id
                WHERE t.cuenta_origen_id = :cuenta_id
                ORDER BY t.fecha DESC, t.id DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':cuenta_id' => $cuentaId
        ]);

        return array_map(
            fn (array $fila): Transferencia => $this->hidratar($fila),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /**
     * @return array{cantidad: int, total: string}
     */
    public function resumenEnviadasPorCuenta(int $cuentaId): array
    {
        $sql = "SELECT COUNT(*) AS cantidad,
                       COALESCE(SUM(valor), 0) AS total
                FROM transferencias
                WHERE cuenta_origen_id = :cuenta_id";

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

    private function hidratar(array $fila): Transferencia
    {
        return new Transferencia(
            (int) $fila['id'],
            (int) $fila['cuenta_origen_id'],
            (int) $fila['cuenta_destino_id'],
            (string) $fila['valor'],
            (string) $fila['fecha'],
            (string) $fila['numero_cuenta_destino']
        );
    }
}
