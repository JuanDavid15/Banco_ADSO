<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Excepciones\CuentaNoEncontradaException;
use App\Nucleo\ControladorBase;
use App\Repositorios\ClienteRepositorio;
use App\Servicios\CuentaServicio;

class CuentaControlador extends ControladorBase
{
    private CuentaServicio $cuentas;
    private ClienteRepositorio $clientes;

    public function __construct()
    {
        parent::__construct();
        $this->cuentas = new CuentaServicio();
        $this->clientes = new ClienteRepositorio();
    }

    // RF3: el saldo se consulta a la base de datos en cada visita al panel.
    public function panelAccion(): void
    {
        try {
            $cuenta = $this->cuentas->obtenerCuenta($this->cuentaActivaId());
        } catch (CuentaNoEncontradaException) {
            // Sesión que apunta a una cuenta que ya no existe: se cierra.
            $this->cerrarSesion();
            $this->redirigir('sesion/login');
        }

        $cliente = $this->clientes->obtenerPorId($cuenta->getClienteId());

        $this->vista('cuenta/panel', [
            'titulo' => 'Mi cuenta',
            'nombreCliente' => $cliente !== null ? $cliente->getNombre() : '',
            'numeroCuenta' => $cuenta->getNumeroCuenta(),
            'saldo' => $cuenta->getSaldo(),
        ]);
    }
}
