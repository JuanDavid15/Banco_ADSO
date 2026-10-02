<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Excepciones\CuentaDestinoInvalidaException;
use App\Excepciones\CuentaNoEncontradaException;
use App\Excepciones\SaldoInsuficienteException;
use App\Excepciones\ValorInvalidoException;
use App\Modelos\Cuenta;
use App\Nucleo\Conexion;
use App\Repositorios\CuentaRepositorio;
use App\Repositorios\RetiroRepositorio;
use App\Repositorios\TransferenciaRepositorio;
use PDO;
use Throwable;

class CuentaServicio
{
    private PDO $conexion;
    private CuentaRepositorio $cuentas;
    private RetiroRepositorio $retiros;
    private TransferenciaRepositorio $transferencias;
    private AutenticacionServicio $autenticacion;

    public function __construct()
    {
        // Conexion es un singleton: esta es la MISMA conexión PDO que usan los
        // repositorios, por eso la transacción abierta aquí los cubre a todos.
        $this->conexion = Conexion::obtener();
        $this->cuentas = new CuentaRepositorio();
        $this->retiros = new RetiroRepositorio();
        $this->transferencias = new TransferenciaRepositorio();
        $this->autenticacion = new AutenticacionServicio();
    }

    /**
     * Devuelve la cuenta leyéndola de la base de datos en este instante (RF3).
     */
    public function obtenerCuenta(int $cuentaId): Cuenta
    {
        $cuenta = $this->cuentas->obtenerPorId($cuentaId);

        if ($cuenta === null) {
            throw new CuentaNoEncontradaException();
        }

        return $cuenta;
    }

    /**
     * RF4 — Retiro. Validaciones en orden: contraseña, valor, saldo.
     * Descuento e inserción en retiros quedan juntos o no queda ninguno.
     */
    public function retirar(int $cuentaId, string $valor, string $clave): void
    {
        $this->autenticacion->confirmarClave($cuentaId, $clave);

        $valorNormalizado = $this->normalizarValor($valor);

        $cuenta = $this->obtenerCuenta($cuentaId);
        $this->exigirSaldoSuficiente($cuenta->getSaldo(), $valorNormalizado);

        $this->conexion->beginTransaction();

        try {
            if (!$this->cuentas->debitar($cuentaId, $valorNormalizado)) {
                throw new SaldoInsuficienteException();
            }

            $this->retiros->crear($cuentaId, $valorNormalizado);

            $this->conexion->commit();
        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $e;
        }
    }

    /**
     * RF6 — Transferencia. Validaciones en el orden del taller: contraseña,
     * destino existe, valor, saldo, destino distinto del origen.
     * Débito + abono + registro se ejecutan como UNA sola transacción.
     */
    public function transferir(
        int $cuentaOrigenId,
        string $numeroCuentaDestino,
        string $valor,
        string $clave
    ): void {
        $this->autenticacion->confirmarClave($cuentaOrigenId, $clave);

        $destino = $this->cuentas->obtenerPorNumero($numeroCuentaDestino);

        if ($destino === null) {
            throw new CuentaNoEncontradaException('La cuenta destino no existe.');
        }

        $valorNormalizado = $this->normalizarValor($valor);

        $origen = $this->obtenerCuenta($cuentaOrigenId);
        $this->exigirSaldoSuficiente($origen->getSaldo(), $valorNormalizado);

        if ($destino->getId() === $origen->getId()) {
            throw new CuentaDestinoInvalidaException();
        }

        $this->conexion->beginTransaction();

        try {
            if (!$this->cuentas->debitar($origen->getId(), $valorNormalizado)) {
                throw new SaldoInsuficienteException();
            }

            if (!$this->cuentas->acreditar($destino->getId(), $valorNormalizado)) {
                throw new CuentaNoEncontradaException('La cuenta destino no existe.');
            }

            $this->transferencias->crear(
                $origen->getId(),
                $destino->getId(),
                $valorNormalizado
            );

            $this->conexion->commit();
        } catch (Throwable $e) {
            // Si algo falló después de descontar, el descuento se deshace.
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Acepta solo números positivos con hasta 2 decimales (punto decimal, sin
     * separadores de miles) y los devuelve con formato "1500.50".
     */
    private function normalizarValor(string $valor): string
    {
        $valor = trim($valor);

        if (preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $valor) !== 1) {
            throw new ValorInvalidoException();
        }

        $centavos = $this->aCentavos($valor);

        if ($centavos <= 0) {
            throw new ValorInvalidoException();
        }

        return intdiv($centavos, 100) . '.' . str_pad((string) ($centavos % 100), 2, '0', STR_PAD_LEFT);
    }

    private function exigirSaldoSuficiente(string $saldo, string $valor): void
    {
        if ($this->aCentavos($saldo) < $this->aCentavos($valor)) {
            throw new SaldoInsuficienteException();
        }
    }

    // Convierte "1500.50" en 150050 para comparar dinero con enteros exactos
    // (sin float).
    private function aCentavos(string $decimal): int
    {
        [$enteros, $decimales] = array_pad(explode('.', $decimal, 2), 2, '0');

        return ((int) $enteros) * 100 + (int) str_pad($decimales, 2, '0');
    }
}
