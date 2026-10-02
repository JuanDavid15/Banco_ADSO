<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Excepciones\CredencialesInvalidasException;
use App\Repositorios\CuentaRepositorio;
use App\Repositorios\UsuarioRepositorio;

class AutenticacionServicio
{
    // Hash bcrypt de relleno. Si la cuenta no existe se verifica contra este
    // hash para tardar lo mismo que con una cuenta real (así nadie descubre
    // qué cuentas existen midiendo el tiempo de respuesta).
    private const HASH_DE_RELLENO = '$2y$10$E6jkU3bIS57/6zQuWn2UcO1npfW3FgQXsKvLD5NTQCialWM.KKUVu';

    private CuentaRepositorio $cuentas;
    private UsuarioRepositorio $usuarios;

    public function __construct()
    {
        $this->cuentas = new CuentaRepositorio();
        $this->usuarios = new UsuarioRepositorio();
    }

    /**
     * Valida número de cuenta + contraseña (RF1) y devuelve el id de la cuenta.
     * Cuenta inexistente y contraseña incorrecta lanzan la MISMA excepción con
     * el MISMO mensaje: no se filtra cuál de las dos falló.
     */
    public function autenticar(string $numeroCuenta, string $clave): int
    {
        $cuenta = $this->cuentas->obtenerPorNumero($numeroCuenta);
        $usuario = $cuenta !== null
            ? $this->usuarios->obtenerPorCuentaId($cuenta->getId())
            : null;

        $hash = $usuario !== null ? $usuario->getClaveHash() : self::HASH_DE_RELLENO;
        $claveCorrecta = password_verify($clave, $hash);

        if ($cuenta === null || $usuario === null || !$claveCorrecta) {
            throw new CredencialesInvalidasException();
        }

        return $cuenta->getId();
    }

    /**
     * Reconfirmación de contraseña para operaciones sensibles (RN4): misma
     * regla del login, aplicada a la cuenta de la sesión.
     */
    public function confirmarClave(int $cuentaId, string $clave): void
    {
        $usuario = $this->usuarios->obtenerPorCuentaId($cuentaId);

        if ($usuario === null || !password_verify($clave, $usuario->getClaveHash())) {
            throw new CredencialesInvalidasException('La contraseña ingresada es incorrecta.');
        }
    }
}
