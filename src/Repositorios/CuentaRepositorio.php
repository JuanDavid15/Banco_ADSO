<?php

declare(strict_types=1);

namespace App\Repositorios;

use App\Modelos\Cuenta;
use App\Nucleo\Conexion;
use PDO;

class CuentaRepositorio
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = Conexion::obtener();
    }

    // Cada llamada hace un SELECT nuevo: así el saldo siempre se lee en vivo.
    public function obtenerPorId(int $id): ?Cuenta
    {
        $sql = "SELECT id, numero_cuenta, cliente_id, saldo
                FROM cuentas
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ? $this->hidratar($fila) : null;
    }

    public function obtenerPorNumero(string $numeroCuenta): ?Cuenta
    {
        $sql = "SELECT id, numero_cuenta, cliente_id, saldo
                FROM cuentas
                WHERE numero_cuenta = :numero_cuenta";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':numero_cuenta' => $numeroCuenta
        ]);

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ? $this->hidratar($fila) : null;
    }

    // La resta la hace MySQL con DECIMAL (no PHP con float). La condición
    // "saldo >= valor" evita saldos negativos aunque dos peticiones lleguen a la vez.
    // Devuelve true solo si realmente se descontó.
    public function debitar(int $id, string $valor): bool
    {
        $sql = "UPDATE cuentas
                SET saldo = saldo - CAST(:valor AS DECIMAL(12,2))
                WHERE id = :id
                  AND saldo >= CAST(:minimo AS DECIMAL(12,2))";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':valor' => $valor,
            ':minimo' => $valor,
            ':id' => $id
        ]);

        return $stmt->rowCount() === 1;
    }

    public function acreditar(int $id, string $valor): bool
    {
        $sql = "UPDATE cuentas
                SET saldo = saldo + CAST(:valor AS DECIMAL(12,2))
                WHERE id = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':valor' => $valor,
            ':id' => $id
        ]);

        return $stmt->rowCount() === 1;
    }

    private function hidratar(array $fila): Cuenta
    {
        return new Cuenta(
            (int) $fila['id'],
            (string) $fila['numero_cuenta'],
            (int) $fila['cliente_id'],
            (string) $fila['saldo']
        );
    }
}
